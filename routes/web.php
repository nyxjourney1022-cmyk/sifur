<?php

use App\Http\Controllers\barangController;
use App\Http\Controllers\kayuController;
use App\Http\Controllers\reservasiController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// Get Data
Route::get('/reservasi',[reservasiController::class, 'index']);
Route::get('/barang',[barangController::class, 'index']);
Route::get('/kayu',[kayuController::class, 'index']);

// Update
Route::put('/reservasi/{id}',[reservasiController::class, 'update']);
Route::put('/barang/{id}',[barangController::class, 'update']);
Route::put('/kayu/{id}',[kayuController::class, 'update']);

// Destroy
Route::delete('/reservasi/{id}',[reservasiController::class,'destroy']);
Route::delete('/barang/{id}',[barangController::class,'destroy']);
Route::delete('/kayu/{id}',[kayuController::class,'destroy']);

// Post Semua reservasi
Route::post('/reservasi',[reservasiController::class, 'store']);
Route::post('/barang',[barangController::class, 'store']);
Route::post('/kayu',[kayuController::class, 'store']);