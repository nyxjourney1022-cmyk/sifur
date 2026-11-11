@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-6 lg:px-8 py-12">

    <div class="mb-8">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-ink transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Kembali ke Beranda
        </a>
        <h1 class="font-serif text-4xl font-medium text-ink">Pesanan Saya</h1>
        <p class="text-muted mt-1">Riwayat semua pesanan kamu</p>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center">
        <div class="w-16 h-16 bg-sand rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h3 class="font-serif text-2xl font-medium text-ink mb-2">Belum ada pesanan</h3>
        <p class="text-muted mb-6">Yuk mulai belanja furniture impianmu!</p>
        <a href="{{ route('katalog') }}"
           class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-7 py-3 rounded-full hover:bg-wood-dark transition-colors">
            Lihat Katalog
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        @php
            $statusColor = match($order->status) {
                'pending'   => 'bg-amber-100 text-amber-700',
                'diproses'  => 'bg-blue-100 text-blue-700',
                'selesai'   => 'bg-green-100 text-green-700',
                'dibatalkan'=> 'bg-red-100 text-red-700',
                default     => 'bg-sand text-muted',
            };
            $total = $order->detailReservasis->sum(fn($d) => $d->barang->harga * $d->jumlah);
        @endphp
        <div class="bg-white rounded-3xl p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="font-semibold text-ink">Pesanan #{{ $order->id }}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColor }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-muted mt-1">
                        {{ \Carbon\Carbon::parse($order->tanggal_reservasi)->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-serif text-xl font-medium text-wood">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-muted mt-0.5">{{ $order->detailReservasis->count() }} item</p>
                </div>
            </div>

            {{-- Preview items --}}
            <div class="flex gap-2 mb-4">
                @foreach($order->detailReservasis->take(4) as $detail)
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-sand shrink-0">
                    @if($detail->barang->gambar_url)
                        <img src="{{ $detail->barang->gambar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-1">
                            <div class="w-7 h-7 bg-wood/50 rounded-t-lg"></div>
                        </div>
                    @endif
                </div>
                @endforeach
                @if($order->detailReservasis->count() > 4)
                <div class="w-14 h-14 rounded-xl bg-sand flex items-center justify-center shrink-0">
                    <span class="text-xs font-semibold text-muted">+{{ $order->detailReservasis->count() - 4 }}</span>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('pesanan.show', $order->id) }}"
                   class="text-sm font-semibold text-wood hover:text-wood-dark transition-colors flex items-center gap-1">
                    Lihat Detail
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                @if($order->status === 'pending')
                <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}?text={{ urlencode('Halo, saya ingin konfirmasi pesanan #' . $order->id) }}"
                   target="_blank"
                   class="text-sm font-medium text-muted hover:text-ink transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Konfirmasi
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
