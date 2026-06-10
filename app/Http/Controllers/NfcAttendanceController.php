<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NfcAttendanceController extends Controller
{
    /**
     * Halaman Kelola Mahasiswa (Master Mahasiswa)
     */
    public function studentIndex(Request $request)
    {
        // 1. Ambil data semua mahasiswa dengan Raw SQL
        $students = DB::select("SELECT * FROM students ORDER BY nim ASC");

        return view('nfc.students', compact('students'));
    }

    /**
     * Tambah Mahasiswa Baru
     */
    public function storeStudent(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'class' => 'required|string|max:50',
        ]);

        $nim = $request->nim;
        $name = $request->name;
        $class = $request->class;

        // Cek apakah NIM sudah ada
        $existing = DB::selectOne("SELECT id FROM students WHERE nim = ?", [$nim]);
        if ($existing) {
            return redirect()->back()->with('error', 'NIM ini sudah terdaftar di sistem.');
        }

        // Insert menggunakan Raw SQL
        DB::insert(
            "INSERT INTO students (nim, name, class, created_at, updated_at) VALUES (?, ?, ?, ?, ?)",
            [$nim, $name, $class, now(), now()]
        );

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    /**
     * Hapus Mahasiswa
     */
    public function destroyStudent(int $id)
    {
        // Delete menggunakan Raw SQL
        DB::delete("DELETE FROM students WHERE id = ?", [$id]);

        return redirect()->back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    /**
     * Daftarkan (Bind) Serial Number NFC ke Mahasiswa
     */
    public function registerCard(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'nfc_serial' => 'required|string|max:100',
        ]);

        $studentId = $request->student_id;
        $nfcSerial = trim($request->nfc_serial);

        // 1. Cek apakah kartu NFC ini sudah dipakai oleh mahasiswa lain
        $duplicate = DB::selectOne("SELECT name FROM students WHERE nfc_serial = ? AND id != ?", [$nfcSerial, $studentId]);
        if ($duplicate) {
            return response()->json([
                'status' => 'error',
                'message' => "Kartu NFC ini sudah terdaftar atas nama {$duplicate->name}!"
            ], 422);
        }

        // 2. Update serial number kartu mahasiswa menggunakan Raw SQL
        DB::update(
            "UPDATE students SET nfc_serial = ?, updated_at = ? WHERE id = ?",
            [$nfcSerial, now(), $studentId]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Kartu NFC berhasil didaftarkan!'
        ]);
    }

    /**
     * Halaman Scanner Kehadiran Mobile
     */
    public function scanIndex()
    {
        return view('nfc.scan');
    }

    /**
     * API Tap Absensi (Pencatatan Kehadiran secara Real-time)
     */
    public function tapCard(Request $request)
    {
        $request->validate([
            'nfc_serial' => 'required|string|max:100',
        ]);

        $nfcSerial = trim($request->nfc_serial);

        // 1. Cari data mahasiswa berdasarkan nfc_serial menggunakan Raw SQL
        $student = DB::selectOne("SELECT * FROM students WHERE nfc_serial = ?", [$nfcSerial]);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu NFC tidak dikenali / Belum terdaftar!'
            ], 404);
        }

        // 2. Cek pencegahan double tap (antispam) dalam 1 menit terakhir
        $recentTap = DB::selectOne("
            SELECT id FROM attendances 
            WHERE student_id = ? 
            AND tapped_at >= ?
            LIMIT 1
        ", [$student->id, Carbon::now()->subMinute()]);

        if ($recentTap) {
            return response()->json([
                'status' => 'warning',
                'message' => "Sudah melakukan absensi! Coba lagi dalam 1 menit.",
                'student' => [
                    'nim' => $student->nim,
                    'name' => $student->name,
                    'class' => $student->class
                ]
            ]);
        }

        // 3. Catat absensi baru menggunakan Raw SQL
        DB::insert(
            "INSERT INTO attendances (student_id, status, tapped_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?)",
            [$student->id, 'Hadir', now(), now(), now()]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil dicatat!',
            'student' => [
                'nim' => $student->nim,
                'name' => $student->name,
                'class' => $student->class,
                'tapped_at' => Carbon::now()->translatedFormat('H:i:s')
            ]
        ]);
    }

    /**
     * Halaman Rekap/Riwayat Absensi
     */
    public function historyIndex()
    {
        // 1. Ambil data riwayat absensi beserta relasi join menggunakan Raw SQL
        $history = DB::select("
            SELECT a.id, a.status, a.tapped_at, s.name, s.nim, s.class 
            FROM attendances a 
            JOIN students s ON a.student_id = s.id 
            ORDER BY a.tapped_at DESC
        ");

        // 2. Hitung statistik menggunakan Raw SQL
        $todayAttendance = DB::selectOne("
            SELECT COUNT(id) as total FROM attendances 
            WHERE DATE(tapped_at) = CURDATE()
        ");

        $totalStudents = DB::selectOne("
            SELECT COUNT(id) as total FROM students
        ");

        $registeredCards = DB::selectOne("
            SELECT COUNT(id) as total FROM students 
            WHERE nfc_serial IS NOT NULL
        ");

        $stats = [
            'today_attendance' => $todayAttendance->total ?? 0,
            'total_students' => $totalStudents->total ?? 0,
            'registered_cards' => $registeredCards->total ?? 0,
        ];

        return view('nfc.history', compact('history', 'stats'));
    }

    /**
     * Absensi Langsung (GET) untuk integrasi dengan iOS Shortcuts / Pintasan
     */
    public function tapCardDirect(Request $request, string $nfc_serial)
    {
        $nfcSerial = trim($nfc_serial);

        // 1. Cari data mahasiswa berdasarkan nfc_serial menggunakan Raw SQL
        $student = DB::selectOne("SELECT * FROM students WHERE nfc_serial = ?", [$nfcSerial]);

        if (!$student) {
            return view('nfc.tap_result', [
                'status' => 'error',
                'message' => 'Kartu KTM NFC tidak dikenali atau belum terdaftar di sistem!',
                'student' => null,
                'nfc_serial' => $nfcSerial,
                'tapped_at' => now()->translatedFormat('H:i:s')
            ]);
        }

        // 2. Cek pencegahan double tap (antispam) dalam 1 menit terakhir
        $recentTap = DB::selectOne("
            SELECT id FROM attendances 
            WHERE student_id = ? 
            AND tapped_at >= ?
            LIMIT 1
        ", [$student->id, Carbon::now()->subMinute()]);

        if ($recentTap) {
            return view('nfc.tap_result', [
                'status' => 'warning',
                'message' => 'Anda sudah melakukan absensi! Harap tunggu 1 menit.',
                'student' => $student,
                'nfc_serial' => $nfcSerial,
                'tapped_at' => now()->translatedFormat('H:i:s')
            ]);
        }

        // 3. Catat absensi baru menggunakan Raw SQL
        DB::insert(
            "INSERT INTO attendances (student_id, status, tapped_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?)",
            [$student->id, 'Hadir', now(), now(), now()]
        );

        return view('nfc.tap_result', [
            'status' => 'success',
            'message' => 'Absensi Anda berhasil dicatat!',
            'student' => $student,
            'nfc_serial' => $nfcSerial,
            'tapped_at' => now()->translatedFormat('H:i:s')
        ]);
    }
}
