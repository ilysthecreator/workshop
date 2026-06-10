<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$barangs = App\Models\Barang::limit(6)->get();
$barcodeGenerator = new Picqer\Barcode\BarcodeGeneratorPNG();
$barcodes = [];
foreach ($barangs as $barang) {
    $barcodePng = $barcodeGenerator->getBarcode($barang->id_barang, Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128, 1, 25);
    $barcodes[$barang->id_barang] = 'data:image/png;base64,' . base64_encode($barcodePng);
}
$html = view('barang.print', ['barangs' => $barangs, 'skipCount' => 0, 'barcodes' => $barcodes])->render();
file_put_contents('public/test-print.html', $html);
