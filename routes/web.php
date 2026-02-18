<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return redirect()->route('home'); 
});

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::resource('kategori', KategoriController::class);
Route::resource('buku', BukuController::class);
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('otp-verifikasi', [GoogleAuthController::class, 'showOtpForm'])->name('otp.view');
Route::post('otp-verifikasi', [GoogleAuthController::class, 'verifyOtp'])->name('otp.verify');