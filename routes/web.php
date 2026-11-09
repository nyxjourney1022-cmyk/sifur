<?php

use App\Http\Controllers\barangController;
use App\Http\Controllers\reservasiController;


use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\TestStatus\Risky;

Route::get('/Home', function () {
    return view('welcome');
});

// Get Data
Route::get('/reservasi',[reservasiController::class, 'index']);
Route::get('/barang',[barangController::class, 'index']);

// Post Semua reservasi
Route::post('/reservasi',[reservasiController::class, 'store']);