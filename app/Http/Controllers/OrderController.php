<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Ambil semua reservasi milik user yang login
        $orders = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', Auth::id())
            ->latest()
            ->get();

        return view('pesanan', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', Auth::id())
            ->findOrFail($id);

        return view('pesanan-detail', compact('order'));
    }
}
