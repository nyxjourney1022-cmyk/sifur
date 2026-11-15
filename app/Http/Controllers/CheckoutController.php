<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Reservasi;
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
            'address_id'    => 'nullable|exists:user_addresses,id',
            'nama_penerima' => 'required_without:address_id|string|max:255',
            'no_tlp'        => 'required_without:address_id|string|max:20',
            'alamat'        => 'required_without:address_id|string',
            'kota'          => 'required_without:address_id|string|max:100',
            'provinsi'      => 'required_without:address_id|string|max:100',
            'kode_pos'      => 'required_without:address_id|string|max:10',
            'save_address'  => 'boolean',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $user  = Auth::user();
        $cart  = $user->getOrCreateCart();
        $items = $cart->items()->with('barang')->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 422);
        }

        // Cek stok
        foreach ($items as $item) {
            if ($item->barang->stok_barang < $item->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok ' . $item->barang->nama_barang . ' tidak mencukupi.',
                ], 422);
            }
        }

        // Ambil data alamat
        if ($request->address_id) {
            $address   = $user->addresses()->findOrFail($request->address_id);
            $noTlp     = $address->no_tlp;
            $alamatStr = $address->alamat . ', ' . $address->kota . ', ' . $address->provinsi . ' ' . $address->kode_pos;
        } else {
            $noTlp     = $request->no_tlp;
            $alamatStr = $request->alamat . ', ' . $request->kota . ', ' . $request->provinsi . ' ' . $request->kode_pos;

            if ($request->boolean('save_address')) {
                $isFirst = $user->addresses()->count() === 0;
                $user->addresses()->create($request->only([
                    'nama_penerima', 'no_tlp', 'alamat', 'kota', 'provinsi', 'kode_pos',
                ]) + ['label' => 'Alamat Baru', 'is_default' => $isFirst]);
            }
        }

        DB::beginTransaction();
        try {
            $total = $items->sum(fn($i) => $i->barang->harga * $i->jumlah);

            // Cari atau buat pelanggan yang terkait dengan user ini
            $pelanggan = Pelanggan::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama'   => $user->name,
                    'no_tlp' => $noTlp ?? '-',
                    'alamat' => $alamatStr,
                ]
            );

            // Update no_tlp dan alamat jika berubah
            $pelanggan->update([
                'no_tlp' => $noTlp ?? $pelanggan->no_tlp,
                'alamat' => $alamatStr,
            ]);

            $reservasi = Reservasi::create([
                'pelanggan_id'      => $pelanggan->id,
                'tanggal_reservasi' => now(),
                'status'            => 'Menunggu',
            ]);

            foreach ($items as $item) {
                $item->barang->decrement('stok_barang', $item->jumlah);
                $reservasi->detailReservasis()->create([
                    'barang_id' => $item->barang_id,
                    'jumlah'    => $item->jumlah,
                ]);
            }

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
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function payment(int $id)
    {
        $user        = Auth::user();
        $pelanggan   = Pelanggan::where('user_id', $user->id)->first();
        $whatsapp    = HomeController::getWhatsapp();

        // Verifikasi kepemilikan
        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', $pelanggan?->id ?? 0)
            ->findOrFail($id);

        $total = $reservasi->detailReservasis->sum(fn($d) => $d->barang->harga * $d->jumlah);

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
        $pelanggan = Pelanggan::where('user_id', Auth::id())->first();
        $whatsapp  = HomeController::getWhatsapp();

        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', $pelanggan?->id ?? 0)
            ->findOrFail($id);

        return view('checkout-success', compact('reservasi', 'whatsapp'));
    }
}
