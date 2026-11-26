<?php

namespace App\Http\Controllers;

/**
 * Controller halaman admin.
 * Dashboard, Transaksi, dan Pelanggan masih memakai DATA DUMMY.
 * Barang, Jenis Kayu, dan Reservasi memakai API asli (/api/*) lewat JavaScript.
 */
class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Total Barang',         'value' => '48',            'note' => '+3 minggu ini'],
                ['label' => 'Reservasi Baru',       'value' => '12',            'note' => '5 menunggu diproses'],
                ['label' => 'Total Pelanggan',      'value' => '134',           'note' => '+9 bulan ini'],
                ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp 86.400.000', 'note' => '+12% dari bulan lalu'],
            ],
            'penjualan' => [
                ['bulan' => 'Jun', 'nilai' => 42], ['bulan' => 'Jul', 'nilai' => 55],
                ['bulan' => 'Agu', 'nilai' => 38], ['bulan' => 'Sep', 'nilai' => 61],
                ['bulan' => 'Okt', 'nilai' => 77], ['bulan' => 'Nov', 'nilai' => 86],
            ],
            'kategori' => [
                ['nama' => 'Kursi', 'persen' => 34], ['nama' => 'Meja', 'persen' => 27],
                ['nama' => 'Lemari', 'persen' => 21], ['nama' => 'Tempat Tidur', 'persen' => 18],
            ],
            'stokMenipis' => [
                ['nama' => 'Kursi Makan Jati', 'stok' => 2], ['nama' => 'Meja Kerja Mahoni', 'stok' => 3],
                ['nama' => 'Lemari Pakaian 3 Pintu', 'stok' => 4], ['nama' => 'Rak Buku Minimalis', 'stok' => 5],
            ],
            'reservasiTerbaru' => [
                ['nama' => 'Budi Santoso', 'tanggal' => '25 Nov 2026', 'status' => 'Menunggu'],
                ['nama' => 'Siti Aminah',  'tanggal' => '24 Nov 2026', 'status' => 'Diproses'],
                ['nama' => 'Rudi Hartono', 'tanggal' => '23 Nov 2026', 'status' => 'Selesai'],
                ['nama' => 'Dewi Lestari', 'tanggal' => '22 Nov 2026', 'status' => 'Dibatalkan'],
            ],
        ]);
    }

    public function barang()    { return view('admin.barang'); }
    public function kayu()      { return view('admin.kayu'); }
    public function reservasi() { return view('admin.reservasi'); }

    public function transaksi()
    {
        $transaksi = [
            ['id' => 'TRX-1042', 'pelanggan' => 'Budi Santoso', 'tanggal' => '25 Nov 2026', 'total' => 4500000,
             'status' => 'Lunas', 'metode' => 'Transfer BCA', 'dibayar' => '25 Nov 2026 14:20',
             'items' => [['nama' => 'Kursi Makan Jati', 'qty' => 4, 'harga' => 750000], ['nama' => 'Meja Makan Jati', 'qty' => 1, 'harga' => 1500000]]],
            ['id' => 'TRX-1041', 'pelanggan' => 'Siti Aminah', 'tanggal' => '24 Nov 2026', 'total' => 2800000,
             'status' => 'Menunggu Pembayaran', 'metode' => 'Transfer Mandiri', 'dibayar' => null,
             'items' => [['nama' => 'Lemari Pakaian 3 Pintu', 'qty' => 1, 'harga' => 2800000]]],
            ['id' => 'TRX-1040', 'pelanggan' => 'Rudi Hartono', 'tanggal' => '22 Nov 2026', 'total' => 1250000,
             'status' => 'Lunas', 'metode' => 'QRIS', 'dibayar' => '22 Nov 2026 09:05',
             'items' => [['nama' => 'Rak Buku Minimalis', 'qty' => 1, 'harga' => 1250000]]],
            ['id' => 'TRX-1039', 'pelanggan' => 'Dewi Lestari', 'tanggal' => '20 Nov 2026', 'total' => 3600000,
             'status' => 'Dibatalkan', 'metode' => 'Transfer BCA', 'dibayar' => null,
             'items' => [['nama' => 'Meja Kerja Mahoni', 'qty' => 2, 'harga' => 1800000]]],
            ['id' => 'TRX-1038', 'pelanggan' => 'Agus Prasetyo', 'tanggal' => '18 Nov 2026', 'total' => 5200000,
             'status' => 'Lunas', 'metode' => 'Transfer BRI', 'dibayar' => '18 Nov 2026 16:42',
             'items' => [['nama' => 'Tempat Tidur Jati Queen', 'qty' => 1, 'harga' => 5200000]]],
        ];
        return view('admin.transaksi', compact('transaksi'));
    }

    public function pelanggan()
    {
        $pelanggan = [
            ['id' => 1, 'nama' => 'Budi Santoso', 'no_tlp' => '0812-3456-7890', 'alamat' => 'Jl. Pemuda No. 12, Jepara', 'bergabung' => 'Jan 2026',
             'riwayat' => [['kode' => 'TRX-1042', 'tanggal' => '25 Nov 2026', 'total' => 4500000, 'status' => 'Lunas'], ['kode' => 'TRX-0877', 'tanggal' => '03 Agu 2026', 'total' => 1800000, 'status' => 'Lunas']]],
            ['id' => 2, 'nama' => 'Siti Aminah', 'no_tlp' => '0857-1122-3344', 'alamat' => 'Jl. Kartini No. 5, Kudus', 'bergabung' => 'Mar 2026',
             'riwayat' => [['kode' => 'TRX-1041', 'tanggal' => '24 Nov 2026', 'total' => 2800000, 'status' => 'Menunggu Pembayaran']]],
            ['id' => 3, 'nama' => 'Rudi Hartono', 'no_tlp' => '0821-9988-7766', 'alamat' => 'Jl. Diponegoro No. 20, Semarang', 'bergabung' => 'Jun 2026',
             'riwayat' => [['kode' => 'TRX-1040', 'tanggal' => '22 Nov 2026', 'total' => 1250000, 'status' => 'Lunas']]],
            ['id' => 4, 'nama' => 'Dewi Lestari', 'no_tlp' => '0813-5566-7788', 'alamat' => 'Jl. Sudirman No. 8, Pati', 'bergabung' => 'Sep 2026',
             'riwayat' => [['kode' => 'TRX-1039', 'tanggal' => '20 Nov 2026', 'total' => 3600000, 'status' => 'Dibatalkan']]],
            ['id' => 5, 'nama' => 'Agus Prasetyo', 'no_tlp' => '0878-4433-2211', 'alamat' => 'Jl. Ahmad Yani No. 31, Demak', 'bergabung' => 'Okt 2026',
             'riwayat' => [['kode' => 'TRX-1038', 'tanggal' => '18 Nov 2026', 'total' => 5200000, 'status' => 'Lunas']]],
        ];
        return view('admin.pelanggan', compact('pelanggan'));
    }
}