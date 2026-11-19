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
            $statusColor = match(strtolower($order->status)) {
                'menunggu'   => 'bg-amber-100 text-amber-700',
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

    {{-- ── Tracking Status ─────────────────── --}}
    <div class="bg-white rounded-3xl p-6 mb-5">
        <h2 class="font-semibold text-ink mb-6">Status Pesanan</h2>
        @php
            $steps = ['Menunggu', 'Diproses', 'Selesai'];
            $currentStatus = $order->status;
            $isCancelled   = $currentStatus === 'Dibatalkan';
            $currentStep   = $isCancelled ? -1 : array_search($currentStatus, $steps);
        @endphp

        @if($isCancelled)
        <div class="flex items-center gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-red-700">Pesanan Dibatalkan</p>
                <p class="text-sm text-red-500 mt-0.5">Pesanan ini telah dibatalkan.</p>
            </div>
        </div>
        @else
        <div class="relative">
            {{-- Garis penghubung --}}
            <div class="absolute top-5 left-5 right-5 h-0.5 bg-sand" style="z-index:0"></div>
            <div class="absolute top-5 left-5 h-0.5 bg-wood transition-all duration-500"
                 style="width: {{ $currentStep === 0 ? '0%' : ($currentStep === 1 ? '50%' : '100%') }}; z-index:1"></div>

            <div class="relative flex justify-between" style="z-index:2">
                @foreach($steps as $i => $step)
                @php
                    $done    = $i <= $currentStep;
                    $active  = $i === $currentStep;
                    $icons   = ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.647 9.172A2 2 0 008.632 19h6.736a2 2 0 001.985-1.828L18 8M10 12v3m4-3v3', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'];
                    $labels  = ['Pesanan Diterima', 'Sedang Diproses', 'Pesanan Selesai'];
                    $descs   = ['Pesanan kamu sudah masuk', 'Sedang disiapkan & dikirim', 'Pesanan telah sampai'];
                @endphp
                <div class="flex flex-col items-center gap-2 w-1/3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                        {{ $done ? 'bg-wood text-white shadow-md' : 'bg-sand text-muted' }}
                        {{ $active ? 'ring-4 ring-wood/20' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$i] }}"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-semibold {{ $done ? 'text-ink' : 'text-muted' }}">{{ $labels[$i] }}</p>
                        <p class="text-xs text-muted mt-0.5 hidden sm:block">{{ $descs[$i] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
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

    {{-- Review per item (hanya jika pesanan selesai) --}}
    @if(strtolower($order->status) === 'selesai')
    <div class="bg-white rounded-3xl p-6 mb-5">
        <h2 class="font-semibold text-ink mb-1">Beri Ulasan</h2>
        <p class="text-sm text-muted mb-5">Bagaimana produk yang kamu terima?</p>
        @foreach($order->detailReservasis as $detail)
        <div class="border-b border-sand pb-6 mb-6 last:border-0 last:pb-0 last:mb-0">
            <div class="flex gap-3 items-center mb-4">
                <div class="w-12 h-12 rounded-xl overflow-hidden bg-sand shrink-0">
                    @if($detail->barang->gambar_url)
                        <img src="{{ $detail->barang->gambar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-1">
                            <div class="w-6 h-6 bg-wood/50 rounded-t-lg"></div>
                        </div>
                    @endif
                </div>
                <p class="font-semibold text-ink text-sm">{{ $detail->barang->nama_barang }}</p>
            </div>
            @include('partials.reviews', ['barangId' => $detail->barang->id])
        </div>
        @endforeach
    </div>
    @endif

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
