<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'harga' => 'required|integer',
        ]);

        Barang::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'timestamp' => now(), // Trigger in DB completes id_barang
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'harga' => 'required|integer',
        ]);

        $barang->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil dihapus.');
    }

    public function printPdf(Request $request)
    {
        $selectedItems = $request->input('selected_items');
        if (empty($selectedItems)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih untuk dicetak.');
        }

        $barangs = Barang::whereIn('id_barang', $selectedItems)->get();

        // Target: T&J 108 Labels (5 Columns per Row)
        $startX = (int) $request->input('start_x', 1);
        $startY = (int) $request->input('start_y', 1);
        
        // Ensure values are within logical bounds
        $startX = max(1, min(5, $startX));
        $startY = max(1, min(8, $startY));
        
        // Calculate the number of empty labels to skip
        $skipCount = (($startY - 1) * 5) + ($startX - 1);

        $pdf = Pdf::loadView('barang.print', compact('barangs', 'skipCount'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('label_harga.pdf');
    }
}
