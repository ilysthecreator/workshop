<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;

Route::get('/', function () {
    return redirect()->route('home'); 
});

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::resource('kategori', KategoriController::class);
Route::resource('buku', BukuController::class);