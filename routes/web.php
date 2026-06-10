<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenjualanController;

Route::get('/', function () {
    return redirect()->route('login'); 
});

Route::get('/tugas-jquery/biasa', function () {
    return view('tugas_jquery_biasa');
})->name('tugas.jquery.biasa');

Route::get('/tugas-jquery/datatables', function () {
    return view('tugas_jquery_datatables');
})->name('tugas.jquery.datatables');

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('otp-verifikasi', [GoogleController::class, 'showOtpForm'])->name('otp.verify');
Route::post('otp-verifikasi', [GoogleController::class, 'verifyOtp']);
Route::get('/download-sertifikat', [PdfController::class, 'generateSertifikat']);
Route::get('/download-undangan', [PdfController::class, 'generateUndangan']);

Route::get('kategori/print', [KategoriController::class, 'print'])->name('kategori.print');
Route::resource('kategori', KategoriController::class);

Route::get('buku/print', [BukuController::class, 'print'])->name('buku.print');
Route::resource('buku', BukuController::class);
Route::post('barang/print-pdf', [BarangController::class, 'printPdf'])->name('barang.printPdf');
Route::get('barang/scan', [BarangController::class, 'scan'])->name('barang.scan');
Route::get('barang/api/scan/{id_barang}', [BarangController::class, 'getBarangById'])->name('barang.api.scan');
Route::resource('barang', BarangController::class);

// Wilayah Indonesia (Cascading Select)
Route::get('wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
Route::get('wilayah/regencies/{province_id}', [WilayahController::class, 'getRegencies']);
Route::get('wilayah/districts/{regency_id}', [WilayahController::class, 'getDistricts']);
Route::get('wilayah/villages/{district_id}', [WilayahController::class, 'getVillages']);

// POS / Kasir (Penjualan)
Route::get('penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('penjualan/cari-barang/{id_barang}', [PenjualanController::class, 'cariBarang']);
Route::post('penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('otp-verifikasi', [GoogleController::class, 'showOtpForm'])->name('otp.verify');
Route::post('otp-verifikasi', [GoogleController::class, 'verifyOtp']);
Route::get('/download-sertifikat', [PdfController::class, 'generateSertifikat']);
Route::get('/download-undangan', [PdfController::class, 'generateUndangan']);

// Kantin Customer
Route::prefix('kantin')->name('kantin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\KantinController::class, 'index'])->name('index');
    Route::get('/menu/{idvendor}', [\App\Http\Controllers\KantinController::class, 'getMenuByVendor']);
    Route::post('/store', [\App\Http\Controllers\KantinController::class, 'store'])->name('store');
    Route::get('/success/{idpesanan}', [\App\Http\Controllers\KantinController::class, 'success'])->name('success');
    Route::get('/invoice/{idpesanan}', [\App\Http\Controllers\KantinController::class, 'invoice'])->name('invoice');
    Route::post('/midtrans/callback', [\App\Http\Controllers\KantinController::class, 'callback'])->name('midtrans.callback');
});

// Vendor & Kelola Kantin
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorController::class, 'index'])->name('index');
    Route::post('/store', [\App\Http\Controllers\VendorController::class, 'store'])->name('store');
    Route::delete('/{id}', [\App\Http\Controllers\VendorController::class, 'destroy'])->name('destroy');

    Route::get('/menu', [\App\Http\Controllers\VendorController::class, 'menu'])->name('menu');
    Route::post('/menu', [\App\Http\Controllers\VendorController::class, 'storeMenu'])->name('menu.store');
    Route::delete('/menu/{id}', [\App\Http\Controllers\VendorController::class, 'destroyMenu'])->name('menu.destroy');
    Route::get('/pesanan', [\App\Http\Controllers\VendorController::class, 'pesanan'])->name('pesanan');
    Route::get('/pesanan/sync/{id}', [\App\Http\Controllers\KantinController::class, 'syncStatus'])->name('pesanan.sync');
    Route::get('/pesanan/struk/{id}', [\App\Http\Controllers\KantinController::class, 'getStrukData'])->name('pesanan.struk');

    // Vendor QR Scanner (Praktikum 2)
    Route::get('/scan', [\App\Http\Controllers\VendorController::class, 'scanQR'])->name('scan');
    Route::get('/scan/api/{idpesanan}', [\App\Http\Controllers\VendorController::class, 'getPesananByQR'])->name('scan.api');
});

// Customer (Kamera Modul)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CustomerController::class, 'index'])->name('index');
    Route::get('/create1', [\App\Http\Controllers\CustomerController::class, 'create1'])->name('create1');
    Route::post('/store1', [\App\Http\Controllers\CustomerController::class, 'store1'])->name('store1');
    Route::get('/create2', [\App\Http\Controllers\CustomerController::class, 'create2'])->name('create2');
    Route::post('/store2', [\App\Http\Controllers\CustomerController::class, 'store2'])->name('store2');
});

// Kunjungan Toko Geolocation
Route::prefix('kunjungan-toko')->name('kunjungan-toko.')->group(function () {
    Route::get('/', [\App\Http\Controllers\KunjunganTokoController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\KunjunganTokoController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\KunjunganTokoController::class, 'store'])->name('store');
    Route::get('/scan', [\App\Http\Controllers\KunjunganTokoController::class, 'scan'])->name('scan');
    Route::post('/api-scan', [\App\Http\Controllers\KunjunganTokoController::class, 'apiScan'])->name('api-scan');
});

// Sistem Antrian Real-Time (SSE)
Route::get('guest', [\App\Http\Controllers\AntrianController::class, 'guest'])->name('antrian.guest');
Route::post('guest', [\App\Http\Controllers\AntrianController::class, 'store'])->name('antrian.store');
Route::get('guest/tiket/{id}', [\App\Http\Controllers\AntrianController::class, 'tiket'])->name('antrian.tiket');
Route::get('admin', [\App\Http\Controllers\AntrianController::class, 'admin'])->name('antrian.admin');
Route::post('admin/panggil', [\App\Http\Controllers\AntrianController::class, 'panggilBerikutnya'])->name('antrian.panggil');
Route::post('admin/terlambat', [\App\Http\Controllers\AntrianController::class, 'tandaiTerlambat'])->name('antrian.terlambat');
Route::post('admin/selesaikan', [\App\Http\Controllers\AntrianController::class, 'selesaikanCurrent'])->name('antrian.selesaikan');
Route::post('admin/panggil-ulang/{id}', [\App\Http\Controllers\AntrianController::class, 'panggilUlang'])->name('antrian.panggilUlang');
Route::get('papan', [\App\Http\Controllers\AntrianController::class, 'papan'])->name('antrian.papan');
Route::get('sse/antrian', [\App\Http\Controllers\AntrianController::class, 'stream'])->name('sse.antrian');

// Modul 11 - Web NFC API (Sistem Absensi NFC - Raw SQL)
Route::prefix('nfc-absensi')->name('nfc.')->group(function () {
    Route::get('/students', [\App\Http\Controllers\NfcAttendanceController::class, 'studentIndex'])->name('students.index');
    Route::post('/students/store', [\App\Http\Controllers\NfcAttendanceController::class, 'storeStudent'])->name('students.store');
    Route::delete('/students/{id}', [\App\Http\Controllers\NfcAttendanceController::class, 'destroyStudent'])->name('students.destroy');
    Route::post('/register-card', [\App\Http\Controllers\NfcAttendanceController::class, 'registerCard'])->name('register-card');
    Route::get('/scan', [\App\Http\Controllers\NfcAttendanceController::class, 'scanIndex'])->name('scan.index');
    Route::post('/tap', [\App\Http\Controllers\NfcAttendanceController::class, 'tapCard'])->name('tap');
    Route::get('/tap-direct/{nfc_serial}', [\App\Http\Controllers\NfcAttendanceController::class, 'tapCardDirect'])->name('tap-direct');
    Route::get('/history', [\App\Http\Controllers\NfcAttendanceController::class, 'historyIndex'])->name('history.index');
});