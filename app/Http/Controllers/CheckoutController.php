<?php

namespace App\Http\Controllers;

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

            // Buat reservasi
            $reservasi = Reservasi::create([
                'pelanggan_id'      => $user->id, // pakai user_id sebagai pelanggan_id
                'tanggal_reservasi' => now(),
                'status'            => 'pending',
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
                'redirect'     => route('checkout.success', $reservasi->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
        }
    }

    public function success(int $id)
    {
        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', Auth::id())
            ->findOrFail($id);

        return view('checkout-success', compact('reservasi'));
    }
}
