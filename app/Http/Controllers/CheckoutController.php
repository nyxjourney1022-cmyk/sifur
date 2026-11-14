<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Reservasi;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function index()
    {
        $user      = Auth::user();
        $cart      = $user->getOrCreateCart();
        $items     = $cart->items()->with('barang.kayu')->get();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        if ($items->isEmpty()) {
            return redirect()->route('katalog')
                ->with('error', 'Keranjang Anda kosong.');
        }

        $total = $items->sum(fn($i) => $i->barang->harga * $i->jumlah);

        return view('checkout', compact('items', 'addresses', 'total', 'user'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_id'     => 'nullable|exists:user_addresses,id',
            'nama_penerima'  => 'required_without:address_id|string|max:255',
            'no_tlp'         => 'required_without:address_id|string|max:20',
            'alamat'         => 'required_without:address_id|string',
            'kota'           => 'required_without:address_id|string|max:100',
            'provinsi'       => 'required_without:address_id|string|max:100',
            'kode_pos'       => 'required_without:address_id|string|max:10',
            'save_address'   => 'boolean',
            'catatan'        => 'nullable|string|max:500',
        ]);

        $user  = Auth::user();
        $cart  = $user->getOrCreateCart();
        $items = $cart->items()->with('barang')->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 422);
        }

        // Cek stok semua item dulu
        foreach ($items as $item) {
            if ($item->barang->stok_barang < $item->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok ' . $item->barang->nama_barang . ' tidak mencukupi.',
                ], 422);
            }
        }

        // Ambil atau pakai alamat baru
        if ($request->address_id) {
            $address = $user->addresses()->findOrFail($request->address_id);
            $alamatStr = $address->alamat . ', ' . $address->kota . ', ' . $address->provinsi . ' ' . $address->kode_pos;
        } else {
            $alamatStr = $request->alamat . ', ' . $request->kota . ', ' . $request->provinsi . ' ' . $request->kode_pos;

            // Simpan alamat kalau diminta
            if ($request->boolean('save_address')) {
                if ($user->addresses()->count() === 0) {
                    $request->merge(['is_default' => true]);
                }
                $user->addresses()->create($request->only([
                    'nama_penerima', 'no_tlp', 'alamat', 'kota', 'provinsi', 'kode_pos',
                ]) + ['label' => 'Alamat Baru', 'is_default' => $user->addresses()->count() === 0]);
            }
        }

        DB::beginTransaction();
        try {
            $total = $items->sum(fn($i) => $i->barang->harga * $i->jumlah);

            // Cari atau buat data pelanggan berdasarkan user yang login
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_tlp' => $user->phone ?? $request->no_tlp ?? ''],
                [
                    'nama'   => $user->name,
                    'alamat' => $alamatStr,
                    'no_tlp' => $request->no_tlp ?? '-',
                ]
            );

            // Buat reservasi
            $reservasi = Reservasi::create([
                'pelanggan_id'      => $pelanggan->id,
                'tanggal_reservasi' => now(),
                'status'            => 'Menunggu',
            ]);

            // Kurangi stok + buat detail reservasi
            foreach ($items as $item) {
                $item->barang->decrement('stok_barang', $item->jumlah);
                $reservasi->detailReservasis()->create([
                    'barang_id' => $item->barang_id,
                    'jumlah'    => $item->jumlah,
                ]);
            }

            // Kosongkan keranjang
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat!',
                'reservasi_id' => $reservasi->id,
                'redirect'     => route('checkout.payment', $reservasi->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
        }
    }

    public function payment(int $id)
    {
        $user      = Auth::user();

        // Cari reservasi — bisa lewat pelanggan yang terkait user ini
        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->findOrFail($id);

        $total     = $reservasi->detailReservasis->sum(fn($d) => $d->barang->harga * $d->jumlah);
        $whatsapp  = \App\Http\Controllers\HomeController::getWhatsapp();

        // Build WA draft
        $lines   = ["Halo, saya ingin melanjutkan pembayaran pesanan berikut:"];
        $lines[] = "";
        $lines[] = "📋 *Nomor Pesanan: #" . $reservasi->id . "*";
        $lines[] = "📅 Tanggal: " . \Carbon\Carbon::parse($reservasi->tanggal_reservasi)->translatedFormat('d F Y');
        $lines[] = "";
        $lines[] = "🛒 *Detail Pesanan:*";
        foreach ($reservasi->detailReservasis as $d) {
            $sub     = $d->barang->harga * $d->jumlah;
            $lines[] = "• " . $d->barang->nama_barang;
            $lines[] = "  " . $d->jumlah . " pcs × Rp " . number_format($d->barang->harga, 0, ',', '.') . " = Rp " . number_format($sub, 0, ',', '.');
        }
        $lines[] = "";
        $lines[] = "💰 *Total: Rp " . number_format($total, 0, ',', '.') . "*";
        $lines[] = "";
        $lines[] = "👤 Nama: " . $user->name;
        $lines[] = "";
        $lines[] = "Mohon konfirmasi ketersediaan dan metode pembayaran. Terima kasih!";

        $waDraft = implode("\n", $lines);

        return view('checkout-payment', compact('reservasi', 'total', 'whatsapp', 'waDraft'));
    }

    public function success(int $id)
    {
        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', Auth::id())
            ->findOrFail($id);

        return view('checkout-success', compact('reservasi'));
    }
}
