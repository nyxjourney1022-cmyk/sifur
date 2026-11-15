<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Ambil pelanggan_id berdasarkan user yang login
    private function getPelangganId(): ?int
    {
        return Pelanggan::where('user_id', Auth::id())->value('id');
    }

    public function index()
    {
        $pelangganId = $this->getPelangganId();

        $orders = $pelangganId
            ? Reservasi::with(['detailReservasis.barang'])
                ->where('pelanggan_id', $pelangganId)
                ->latest()
                ->get()
            : collect();

        $whatsapp = HomeController::getWhatsapp();

        return view('pesanan', compact('orders', 'whatsapp'));
    }

    public function show(int $id)
    {
        $pelangganId = $this->getPelangganId();

        $order = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', $pelangganId ?? 0)
            ->findOrFail($id);

        $whatsapp = HomeController::getWhatsapp();

        return view('pesanan-detail', compact('order', 'whatsapp'));
    }
}
