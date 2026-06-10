<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;

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

        // Generate barcode images (base64 PNG) for each item when generator is available.
        foreach ($barangs as $it) {
            try {
                if (class_exists(\Picqer\Barcode\BarcodeGeneratorPNG::class)) {
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    // Use CODE 128 for compact alphanumeric barcodes
                    $png = $generator->getBarcode((string)$it->id_barang, $generator::TYPE_CODE_128);
                    $it->barcode = 'data:image/png;base64,' . base64_encode($png);
                } else {
                    Log::warning('Picqer barcode generator not installed. Run: composer require picqer/php-barcode-generator');
                    $it->barcode = null;
                }
            } catch (\Throwable $e) {
                Log::error('Barcode generation failed', ['id' => $it->id_barang, 'error' => $e->getMessage()]);
                $it->barcode = null;
            }
        }

        // Target: T&J 108 Labels (5 Columns x 8 Rows per Page)
        $cols = 5;
        $rows = 8;
        $totalPerPage = $cols * $rows;

        $startX = max(1, (int)$request->input('start_x', 1));
        $startY = max(1, (int)$request->input('start_y', 1));
        
        // Ensure values are within logical bounds
        $startX = min($cols, $startX);
        $startY = min($rows, $startY);
        
        // Calculate the number of empty labels to skip on the first page
        $skipCount = (($startY - 1) * $cols) + ($startX - 1);

        $slots = [];
        for ($i = 0; $i < $skipCount; $i++) {
            $slots[] = null;
        }
        foreach ($barangs as $barang) {
            $slots[] = $barang;
        }

        // Chunk into pages of 40 (5 columns x 8 rows)
        $pagesFlat = array_chunk($slots, $totalPerPage);
        $pages = [];
        
        foreach ($pagesFlat as $pageFlat) {
            // Guarantee full 8x5 grid matrix for each page, filling with nulls
            $pageFlat = array_pad($pageFlat, $totalPerPage, null);
            
            $grid = [];
            for ($r = 0; $r < $rows; $r++) {
                $row = [];
                for ($c = 0; $c < $cols; $c++) {
                    $row[] = $pageFlat[$r * $cols + $c];
                }
                $grid[] = $row;
            }
            $pages[] = $grid;
        }

        $pdf = Pdf::loadView('barang.print', compact('pages'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('label_harga.pdf');
    }

    /**
     * Praktikum 1: Halaman Barcode Scanner
     */
    public function scan()
    {
        return view('barang.scan');
    }

    /**
     * Praktikum 1: API endpoint – ambil data barang berdasarkan id_barang (dari barcode)
     */
    public function getBarangById($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan ID ' . $id_barang . ' tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama' => $barang->nama,
                'harga' => $barang->harga,
                'harga_format' => 'Rp ' . number_format($barang->harga, 0, ',', '.'),
            ],
        ]);
    }
}
