<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AntrianController extends Controller
{
    /**
     * Halaman Guest — Form pendaftaran antrian
     */
    public function guest()
    {
        return view('antrian.guest');
    }

    /**
     * Simpan antrian baru dari guest
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        // Generate nomor antrian hari ini (reset setiap hari)
        $today = now()->toDateString();
        $lastAntrian = Antrian::whereDate('created_at', $today)->max('nomor_antrian');
        $nomorBaru = ($lastAntrian ?? 0) + 1;

        $antrian = Antrian::create([
            'nomor_antrian' => $nomorBaru,
            'nama' => $request->nama,
            'status' => 'menunggu',
        ]);

        // Trigger SSE update via cache
        Cache::put('antrian_last_update', now()->timestamp);

        return redirect()->route('antrian.tiket', $antrian->id);
    }

    /**
     * Halaman tiket antrian untuk guest
     */
    public function tiket(int $id)
    {
        $antrian = Antrian::findOrFail($id);
        return view('antrian.tiket', compact('antrian'));
    }

    /**
     * Halaman Admin — Dashboard kelola antrian
     */
    public function admin()
    {
        $today = now()->toDateString();
        $daftarAntrian = Antrian::whereDate('created_at', $today)
            ->orderBy('nomor_antrian')
            ->get();

        $sedangDipanggil = Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->latest('updated_at')
            ->first();

        return view('antrian.admin', compact('daftarAntrian', 'sedangDipanggil'));
    }

    /**
     * Admin memanggil nomor antrian berikutnya
     */
    public function panggilBerikutnya(Request $request)
    {
        $today = now()->toDateString();

        // Set antrian yang sedang dipanggil menjadi selesai
        Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->update(['status' => 'selesai']);

        // Ambil antrian menunggu berikutnya
        $next = Antrian::whereDate('created_at', $today)
            ->where('status', 'menunggu')
            ->orderBy('nomor_antrian')
            ->first();

        if ($next) {
            $next->status = 'dipanggil';
            $next->save();

            // Trigger SSE update
            Cache::put('antrian_last_update', now()->timestamp);
            Cache::put('antrian_dipanggil', $next->nomor_antrian);

            return response()->json([
                'success' => true,
                'message' => 'Nomor antrian ' . $next->nomor_antrian . ' dipanggil.',
                'nomor' => $next->nomor_antrian,
                'nama' => $next->nama,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian yang menunggu.',
        ]);
    }

    /**
     * Tandai antrian yang sedang dipanggil sebagai terlambat
     */
    public function tandaiTerlambat(Request $request)
    {
        $today = now()->toDateString();

        $current = Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->latest('updated_at')
            ->first();

        if ($current) {
            $current->status = 'terlambat';
            $current->save();

            // Trigger SSE update
            Cache::put('antrian_last_update', now()->timestamp);

            return response()->json([
                'success' => true,
                'message' => 'Nomor antrian ' . $current->nomor_antrian . ' ditandai terlambat.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian yang sedang dipanggil.',
        ]);
    }

    /**
     * Selesaikan antrian yang sedang dipanggil
     */
    public function selesaikanCurrent(Request $request)
    {
        $today = now()->toDateString();

        $current = Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->latest('updated_at')
            ->first();

        if ($current) {
            $current->status = 'selesai';
            $current->save();

            // Trigger SSE update
            Cache::put('antrian_last_update', now()->timestamp);

            return response()->json([
                'success' => true,
                'message' => 'Nomor antrian ' . $current->nomor_antrian . ' diselesaikan.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian yang sedang dipanggil.',
        ]);
    }

    /**
     * Panggil ulang antrian tertentu (misalnya dari daftar terlambat atau menunggu)
     */
    public function panggilUlang(Request $request, int $id)
    {
        $today = now()->toDateString();
        $antrian = Antrian::findOrFail($id);

        // Set antrian lain yang sedang dipanggil menjadi selesai
        Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->update(['status' => 'selesai']);

        // Set status antrian terpilih menjadi dipanggil
        $antrian->status = 'dipanggil';
        $antrian->save();

        // Trigger SSE update
        Cache::put('antrian_last_update', now()->timestamp);
        Cache::put('antrian_dipanggil', $antrian->nomor_antrian);

        return response()->json([
            'success' => true,
            'message' => 'Memanggil ulang nomor antrian ' . $antrian->nomor_antrian . '.',
            'nomor' => $antrian->nomor_antrian,
            'nama' => $antrian->nama,
        ]);
    }

    /**
     * Halaman Papan Antrian — Display publik
     */
    public function papan()
    {
        return view('antrian.papan');
    }

    /**
     * SSE Endpoint — Stream antrian real-time
     * Mengirim data sekali lalu koneksi ditutup.
     * EventSource di browser akan otomatis reconnect setiap 3 detik (retry).
     * Pendekatan ini cocok untuk php artisan serve yang single-threaded.
     */
    public function stream()
    {
        $today = now()->toDateString();

        // Ambil data antrian hari ini
        $daftarAntrian = Antrian::whereDate('created_at', $today)
            ->orderBy('nomor_antrian')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_antrian' => $item->nomor_antrian,
                    'nama' => $item->nama,
                    'status' => $item->status,
                ];
            });

        // Ambil nomor yang sedang dipanggil
        $dipanggil = Antrian::whereDate('created_at', $today)
            ->where('status', 'dipanggil')
            ->latest('updated_at')
            ->first();

        $data = json_encode([
            'daftar' => $daftarAntrian,
            'dipanggil' => $dipanggil ? [
                'nomor_antrian' => $dipanggil->nomor_antrian,
                'nama' => $dipanggil->nama,
            ] : null,
            'timestamp' => now()->timestamp,
        ]);

        return response()->stream(function () use ($data) {
            echo "retry: 3000\n"; // reconnect setiap 3 detik
            echo "data: {$data}\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
