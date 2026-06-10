<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiToko;

class KunjunganTokoController extends Controller
{
    public function index()
    {
        $tokos = LokasiToko::all();
        return view('kunjungan_toko.index', compact('tokos'));
    }

    public function create()
    {
        return view('kunjungan_toko.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:8|unique:lokasi_toko,barcode',
            'nama_toko' => 'required|string|max:50',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
        ]);

        LokasiToko::create($request->all());

        return redirect()->route('kunjungan-toko.index')->with('success', 'Titik awal toko berhasil disimpan.');
    }

    public function scan()
    {
        return view('kunjungan_toko.scan');
    }

    public function apiScan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $toko = LokasiToko::find($request->barcode);

        if (!$toko) {
            return response()->json([
                'status' => 'error',
                'message' => 'Toko tidak ditemukan.'
            ], 404);
        }

        // Mode lookup: hanya ambil data toko dari DB
        if ($request->mode === 'lookup') {
            return response()->json([
                'status' => 'lookup',
                'toko' => $toko
            ]);
        }

        // Mode verify: hitung jarak dan tentukan status
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
        ]);

        $sales_lat = $request->latitude;
        $sales_lng = $request->longitude;
        $sales_acc = $request->accuracy;

        $jarak_aktual = $this->haversine($toko->latitude, $toko->longitude, $sales_lat, $sales_lng);
        
        $base_threshold = 300; // 300 meters
        $threshold_efektif = $base_threshold + $toko->accuracy + $sales_acc;

        $is_accepted = $jarak_aktual <= $threshold_efektif;

        return response()->json([
            'status' => 'success',
            'toko' => $toko,
            'jarak_aktual' => round($jarak_aktual, 2),
            'threshold_efektif' => round($threshold_efektif, 2),
            'is_accepted' => $is_accepted,
            'message' => $is_accepted ? 'DITERIMA' : 'DITOLAK'
        ]);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $r = 6371000; // Earth radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $r * $c;
    }
}
