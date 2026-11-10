<?php

use App\Http\Controllers\barangController;
use App\Http\Controllers\kayuController;
use App\Http\Controllers\reservasiController;


use Illuminate\Support\Facades\Route;

Route::get('/Home', function () {
    return view('welcome');
});

// Get Data
Route::get('/reservasi',[reservasiController::class, 'index']);
Route::get('/barang',[barangController::class, 'index']);
Route::get('/kayu',[kayuController::class, 'index']);

// Update
Route::put('/reservasi/{id}',[reservasiController::class, 'Update']);
Route::put('/barang/{id}',[barangController::class, 'Update']);

// Destroy
Route::delete('/reservasi/{id}',[reservasiController::class,'destroy']);
Route::delete('/barang/{id}',[barangController::class,'destroy']);

// Post Semua reservasi
Route::post('/reservasi',[reservasiController::class, 'store']);
Route::post('/barang',[barangController::class, 'store']);