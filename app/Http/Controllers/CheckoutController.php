<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        $items = $cart->items()
            ->with('barang.kayu')
            ->get();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('katalog')
                ->with('error', 'Keranjang Anda kosong.');
        }

        $total = $items->sum(
            fn ($item) => $item->barang->harga * $item->jumlah
        );

        return view('checkout', compact(
            'items',
            'addresses',
            'total',
            'user'
        ));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'address_id'    => 'nullable|exists:user_addresses,id',
            'nama_penerima' => 'required_without:address_id|string|max:255',
            'no_tlp'        => 'required_without:address_id|string|max:20',
            'alamat'        => 'required_without:address_id|string',
            'kota'          => 'required_without:address_id|string|max:100',
            'provinsi'      => 'required_without:address_id|string|max:100',
            'kode_pos'      => 'required_without:address_id|string|max:10',
            'save_address'  => 'nullable|boolean',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        try {
            $reservasi = DB::transaction(function () use (
                $request,
                $validated,
                $user,
                $cart
            ) {
                // Ambil ulang isi keranjang di dalam transaksi.
                $items = $cart->items()->get();

                if ($items->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' => 'Keranjang Anda kosong.',
                    ]);
                }

                // Kunci seluruh barang yang dipesan.
                $barangs = Barang::whereIn(
                    'id',
                    $items->pluck('barang_id')->unique()
                )
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Periksa seluruh stok sebelum mengurangi satu pun.
                foreach ($items as $item) {
                    /** @var Barang|null $barang */
                    $barang = $barangs->get($item->barang_id);

                    if (!$barang) {
                        throw ValidationException::withMessages([
                            'cart' => 'Salah satu barang sudah tidak tersedia.',
                        ]);
                    }

                    if ($barang->stok_barang < $item->jumlah) {
                        throw ValidationException::withMessages([
                            'cart' => 'Stok ' . $barang->nama_barang .
                                ' tidak mencukupi. Silakan hubungi admin melalui WhatsApp.',
                        ]);
                    }
                }

                // Ambil data alamat milik user atau buat alamat baru.
                if (!empty($validated['address_id'])) {
                    $address = $user->addresses()
                        ->findOrFail($validated['address_id']);

                    $noTlp = $address->no_tlp;

                    $alamatStr = $address->alamat . ', ' .
                        $address->kota . ', ' .
                        $address->provinsi . ' ' .
                        $address->kode_pos;
                } else {
                    $noTlp = $validated['no_tlp'];

                    $alamatStr = $validated['alamat'] . ', ' .
                        $validated['kota'] . ', ' .
                        $validated['provinsi'] . ' ' .
                        $validated['kode_pos'];

                    if ($request->boolean('save_address')) {
                        $isFirst = $user->addresses()->count() === 0;

                        $user->addresses()->create([
                            'nama_penerima' => $validated['nama_penerima'],
                            'no_tlp'        => $validated['no_tlp'],
                            'alamat'        => $validated['alamat'],
                            'kota'          => $validated['kota'],
                            'provinsi'      => $validated['provinsi'],
                            'kode_pos'      => $validated['kode_pos'],
                            'label'         => 'Alamat Baru',
                            'is_default'    => $isFirst,
                        ]);
                    }
                }

                // Cari atau buat pelanggan berdasarkan user yang login.
                $pelanggan = Pelanggan::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama'   => $user->name,
                        'no_tlp' => $noTlp,
                        'alamat' => $alamatStr,
                    ]
                );

                // Update no_tlp saja jika berubah, jangan timpa alamat utama
                // karena alamat pengiriman sudah tersimpan di user_addresses
                if ($pelanggan->no_tlp !== $noTlp) {
                    $pelanggan->update(['no_tlp' => $noTlp]);
                }

                // Hitung total berdasarkan harga barang saat checkout.
                $total = 0;

                foreach ($items as $item) {
                    /** @var Barang $barang */
                    $barang = $barangs->get($item->barang_id);

                    $total += $barang->harga * $item->jumlah;
                }

                // Buat reservasi dan tandai bahwa stok akan dikurangi.
                $reservasi = Reservasi::create([
                    'pelanggan_id'      => $pelanggan->id,
                    'tanggal_reservasi' => now(),
                    'status'            => 'Menunggu',
                    'stok_dikurangi'    => true,
                ]);

                // Kurangi stok dan simpan detail reservasi.
                foreach ($items as $item) {
                    /** @var Barang $barang */
                    $barang = $barangs->get($item->barang_id);

                    $barang->decrement(
                        'stok_barang',
                        $item->jumlah
                    );

                    $reservasi->detailReservasis()->create([
                        'barang_id' => $barang->id,
                        'jumlah'    => $item->jumlah,
                    ]);
                }

                // Hapus isi keranjang setelah reservasi berhasil dibuat.
                $cart->items()->delete();

                return $reservasi;
            }, 3);

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat!',
                'reservasi_id' => $reservasi->id,
                'redirect'     => route('checkout.payment', $reservasi->id),
            ]);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            Log::error('Checkout gagal', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal diproses. Silakan coba kembali.',
            ], 500);
        }
    }

    public function payment(int $id)
    {
        $user      = Auth::user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();
        $whatsapp  = HomeController::getWhatsapp();

        if (!$pelanggan) {
            return redirect()->route('pesanan')
                ->with('error', 'Belum ada pesanan yang ditemukan.');
        }

        // Pastikan reservasi merupakan milik pelanggan yang login.
        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', $pelanggan->id)
            ->find($id);

        if (!$reservasi) {
            return redirect()->route('pesanan')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $total = $reservasi->detailReservasis->sum(
            fn ($detail) => $detail->barang->harga * $detail->jumlah
        );

        $lines = [
            'Halo, saya ingin melanjutkan pembayaran pesanan berikut:',
            '',
            ' *Nomor Pesanan: #' . $reservasi->id . '*',
            ' Tanggal: ' . \Carbon\Carbon::parse(
                $reservasi->tanggal_reservasi
            )->translatedFormat('d F Y'),
            '',
            '*Detail Pesanan:*',
        ];

        foreach ($reservasi->detailReservasis as $detail) {
            $subtotal = $detail->barang->harga * $detail->jumlah;

            $lines[] = '• ' . $detail->barang->nama_barang;

            $lines[] = '  ' . $detail->jumlah . ' pcs × Rp ' .
                number_format($detail->barang->harga, 0, ',', '.') .
                ' = Rp ' .
                number_format($subtotal, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = ' *Total: Rp ' .
            number_format($total, 0, ',', '.') . '*';
        $lines[] = '';
        $lines[] = ' Nama: ' . $user->name;
        $lines[] = '';
        $lines[] = 'Mohon konfirmasi ketersediaan dan metode pembayaran. Terima kasih!';

        $waDraft = implode("\n", $lines);

        return view('checkout-payment', compact(
            'reservasi',
            'total',
            'whatsapp',
            'waDraft'
        ));
    }

    public function success(int $id)
    {
        $pelanggan = Pelanggan::where('user_id', Auth::id())->first();
        $whatsapp  = HomeController::getWhatsapp();

        if (!$pelanggan) {
            return redirect()->route('pesanan')
                ->with('error', 'Belum ada pesanan yang ditemukan.');
        }

        $reservasi = Reservasi::with(['detailReservasis.barang'])
            ->where('pelanggan_id', $pelanggan->id)
            ->find($id);

        if (!$reservasi) {
            return redirect()->route('pesanan')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('checkout-success', compact('reservasi', 'whatsapp'));
    }
}
