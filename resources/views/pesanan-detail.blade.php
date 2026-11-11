@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto px-6 lg:px-8 py-12">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('pesanan') }}" class="p-2 rounded-full hover:bg-sand transition-colors text-muted hover:text-ink">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <h1 class="font-serif text-3xl font-medium text-ink">Pesanan #{{ $order->id }}</h1>
        @php
            $statusColor = match($order->status) {
                'pending'    => 'bg-amber-100 text-amber-700',
                'diproses'   => 'bg-blue-100 text-blue-700',
                'selesai'    => 'bg-green-100 text-green-700',
                'dibatalkan' => 'bg-red-100 text-red-700',
                default      => 'bg-sand text-muted',
            };
        @endphp
        <span class="text-sm font-semibold px-3 py-1 rounded-full {{ $statusColor }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    {{-- Info pesanan --}}
    <div class="bg-white rounded-3xl p-6 mb-5">
        <h2 class="font-semibold text-ink mb-4">Informasi Pesanan</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-muted">Tanggal Pesan</p>
                <p class="font-medium text-ink mt-0.5">
                    {{ \Carbon\Carbon::parse($order->tanggal_reservasi)->translatedFormat('d F Y, H:i') }}
                </p>
            </div>
            <div>
                <p class="text-muted">Status</p>
                <p class="font-medium text-ink mt-0.5">{{ ucfirst($order->status) }}</p>
            </div>
        </div>
    </div>

    {{-- Item pesanan --}}
    <div class="bg-white rounded-3xl p-6 mb-5">
        <h2 class="font-semibold text-ink mb-4">Item Pesanan</h2>
        <div class="space-y-4">
            @foreach($order->detailReservasis as $detail)
            @php $subtotal = $detail->barang->harga * $detail->jumlah; @endphp
            <div class="flex gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-sand shrink-0">
                    @if($detail->barang->gambar_url)
                        <img src="{{ $detail->barang->gambar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-1.5">
                            <div class="w-8 h-8 bg-wood/50 rounded-t-lg"></div>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-xs text-olive font-medium">{{ $detail->barang->kategori }}</p>
                    <p class="font-semibold text-ink">{{ $detail->barang->nama_barang }}</p>
                    <p class="text-sm text-muted mt-0.5">{{ $detail->jumlah }} pcs × Rp {{ number_format($detail->barang->harga, 0, ',', '.') }}</p>
                </div>
                <p class="font-semibold text-ink shrink-0">
                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>

        <div class="border-t border-sand mt-5 pt-5 space-y-2">
            <div class="flex justify-between text-sm text-muted">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->detailReservasis->sum(fn($d) => $d->barang->harga * $d->jumlah), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-muted">
                <span>Pengiriman</span>
                <span class="text-olive font-medium">Gratis</span>
            </div>
            <div class="flex justify-between font-semibold text-ink pt-1">
                <span>Total</span>
                <span class="font-serif text-xl text-wood">
                    Rp {{ number_format($order->detailReservasis->sum(fn($d) => $d->barang->harga * $d->jumlah), 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('katalog') }}"
           class="flex-1 text-center bg-wood text-white font-semibold px-7 py-3.5 rounded-full hover:bg-wood-dark transition-colors">
            Belanja Lagi
        </a>
        <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}?text={{ urlencode('Halo, saya ingin konfirmasi pesanan #' . $order->id) }}"
           target="_blank"
           class="flex-1 text-center border-2 border-ink/20 text-ink font-semibold px-7 py-3.5 rounded-full hover:border-ink transition-colors">
            Konfirmasi via WhatsApp
        </a>
    </div>
</div>
@endsection
