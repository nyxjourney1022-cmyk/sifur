@extends('layouts.app')
@section('title', 'Pesanan Berhasil')
@section('content')
<div class="max-w-2xl mx-auto px-6 py-20 text-center">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="font-serif text-4xl font-medium text-ink mb-3">Pesanan Diterima!</h1>
    <p class="text-muted text-lg mb-2">Nomor Pesanan: <strong class="text-ink">#{{ $reservasi->id }}</strong></p>
    <p class="text-muted mb-10">Kami akan segera memproses pesanan Anda. Terima kasih!</p>

    <div class="bg-white rounded-3xl p-6 text-left mb-8">
        <h2 class="font-semibold text-ink mb-4">Detail Pesanan</h2>
        <div class="space-y-3">
            @foreach($reservasi->detailReservasis as $detail)
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-sand overflow-hidden shrink-0">
                    @if($detail->barang->gambar_url)
                        <img src="{{ $detail->barang->gambar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-1">
                            <div class="w-6 h-6 bg-wood/50 rounded-t-lg"></div>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-ink">{{ $detail->barang->nama_barang }}</p>
                    <p class="text-xs text-muted">{{ $detail->jumlah }} pcs</p>
                </div>
                <p class="text-sm font-semibold text-wood">
                    Rp {{ number_format($detail->barang->harga * $detail->jumlah, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('home') }}"
           class="bg-wood text-white font-semibold px-8 py-3.5 rounded-full hover:bg-wood-dark transition-colors">
            Kembali Belanja
        </a>
        <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}?text={{ urlencode('Halo, saya ingin konfirmasi pesanan #' . $reservasi->id) }}"
           target="_blank"
           class="border-2 border-ink/20 text-ink font-semibold px-8 py-3.5 rounded-full hover:border-ink transition-colors">
            Konfirmasi via WhatsApp
        </a>
    </div>
</div>
@endsection
