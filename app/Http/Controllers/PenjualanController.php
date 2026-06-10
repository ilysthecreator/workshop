<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('penjualan.index');
    }

    /**
     * Cari barang berdasarkan id_barang (AJAX)
     */
    public function cariBarang($id_barang)
    {
        $barang = Barang::find($id_barang);

        if ($barang) {
            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Barang tidak ditemukan'
        ]);
    }

    /**
     * Simpan transaksi penjualan (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::create([
                'timestamp' => now(),
                'total' => $request->total,
            ]);

            foreach ($request->items as $item) {
                PenjualanDetail::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran transaksi berhasil disimpan!',
                'id_penjualan' => $penjualan->id_penjualan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
