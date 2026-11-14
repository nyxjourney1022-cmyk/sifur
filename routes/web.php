<?php

use App\Http\Controllers\barangController;
use App\Http\Controllers\kayuController;
use App\Http\Controllers\reservasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// ── Public pages ──────────────────────────────────────────────
Route::get('/',             [HomeController::class, 'index'])->name('home');
Route::get('/katalog',      [HomeController::class, 'katalog'])->name('katalog');
Route::get('/katalog/data', [HomeController::class, 'katalogData'])->name('katalog.data');

// ── Auth ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Google OAuth
Route::get('/auth/google',          [AuthController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');

// ── Authenticated routes ──────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Cart (semua JSON / AJAX)
    Route::get('/cart',             [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',        [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{id}',[CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',          [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout',              [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout',             [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/payment/{id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Addresses (JSON)
    Route::get('/addresses',              [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses',             [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{id}',         [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{id}',      [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Profile
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil');

    // Pesanan
    Route::get('/pesanan',       [OrderController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{id}',  [OrderController::class, 'show'])->name('pesanan.show');
});

// ── Chat CS (public, no auth required) ───────────────────────
Route::post('/chat',                    [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/rekomendasi',        [ChatController::class, 'rekomendasiKategori'])->name('chat.rekomendasi');

// ── API: Backend CRUD (tidak perlu auth untuk demo, bisa ditambah middleware) ──
Route::prefix('api')->group(function () {
    Route::get('/reservasi',          [reservasiController::class, 'index']);
    Route::post('/reservasi',         [reservasiController::class, 'store']);
    Route::put('/reservasi/{id}',     [reservasiController::class, 'update']);
    Route::delete('/reservasi/{id}',  [reservasiController::class, 'destroy']);

    Route::get('/barang',             [barangController::class, 'index']);
    Route::post('/barang',            [barangController::class, 'store']);
    Route::put('/barang/{id}',        [barangController::class, 'update']);
    Route::delete('/barang/{id}',     [barangController::class, 'destroy']);

    Route::get('/kayu',               [kayuController::class, 'index']);
    Route::post('/kayu',              [kayuController::class, 'store']);
    Route::put('/kayu/{id}',          [kayuController::class, 'update']);
    Route::delete('/kayu/{id}',       [kayuController::class, 'destroy']);
});
