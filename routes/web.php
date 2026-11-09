<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\reservasiController;
use PHPUnit\Framework\TestStatus\Risky;

Route::get('/Home', function () {
    return view('welcome');
});

//  Data semua reservasi
Route::get('/reservasi',[reservasiController::class, 'index']);

// Post Semua reservasi
Route::post('/reservasi',[reservasiController::class, 'store']);