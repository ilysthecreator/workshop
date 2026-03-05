<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;

Route::get('/', function () {
    return redirect()->route('login'); 
});

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
Route::resource('barang', BarangController::class);
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('otp-verifikasi', [GoogleController::class, 'showOtpForm'])->name('otp.verify');
Route::post('otp-verifikasi', [GoogleController::class, 'verifyOtp']);
Route::get('/download-sertifikat', [PdfController::class, 'generateSertifikat']);
Route::get('/download-undangan', [PdfController::class, 'generateUndangan']);