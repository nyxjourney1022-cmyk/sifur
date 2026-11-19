@extends('layouts.app')
@section('title', 'Wishlist Saya')

@section('content')
<div class="max-w-5xl mx-auto px-6 lg:px-8 py-12">

    <div class="mb-8">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-ink transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Kembali ke Beranda
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-serif text-4xl font-medium text-ink">Wishlist Saya</h1>
                <p class="text-muted mt-1">{{ $items->count() }} produk tersimpan</p>
            </div>
            @if($items->isNotEmpty())
            <button onclick="addAllToCart()"
                    class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-5 py-2.5 rounded-full hover:bg-wood-dark transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Tambah Semua ke Keranjang
            </button>
            @endif
        </div>
    </div>

    @if($items->isEmpty())
    <div class="bg-white rounded-3xl p-16 text-center">
        <div class="w-20 h-20 bg-sand rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
        </div>
        <h3 class="font-serif text-2xl font-medium text-ink mb-2">Wishlist masih kosong</h3>
        <p class="text-muted mb-6">Simpan produk favoritmu agar mudah ditemukan nanti.</p>
        <a href="{{ route('katalog') }}"
           class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-7 py-3 rounded-full hover:bg-wood-dark transition-colors">
            Jelajahi Katalog
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="wishlist-grid">
        @foreach($items as $item)
        <div class="bg-white rounded-3xl overflow-hidden hover:shadow-md transition-shadow group"
             id="wish-card-{{ $item['id'] }}">

            {{-- Gambar --}}
            <div class="w-full h-52 bg-sand overflow-hidden relative">
                @if($item['image'])
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-end justify-center pb-6 bg-gradient-to-br from-sand to-[#ddd0bc]">
                        <div class="w-3/5 h-20 bg-wood/70 rounded-t-2xl rounded-b-lg"></div>
                    </div>
                @endif

                {{-- Tombol hapus wishlist --}}
                <button onclick="removeWishlist({{ $item['id'] }})"
                        class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </button>

                @if($item['stok'] <= 0)
                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                    HABIS
                </span>
                @elseif($item['stok'] <= 3)
                <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                    STOK TERBATAS
                </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="p-5">
                <span class="text-[11px] font-semibold tracking-wider uppercase text-olive">
                    {{ $item['kategori'] }}
                </span>
                @if($item['kayu'])
                <span class="text-[11px] text-muted bg-sand px-2 py-0.5 rounded-full ml-1">
                    {{ $item['kayu'] }}
                </span>
                @endif
                <h3 class="font-semibold text-ink text-base mt-1 mb-3">{{ $item['name'] }}</h3>
                <div class="flex items-center justify-between">
                    <p class="font-serif text-xl font-medium text-wood">
                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                    </p>
                    @if($item['stok'] > 0)
                    <button onclick="CartDrawer.add({{ $item['id'] }})"
                            class="text-xs font-semibold text-wood hover:text-white hover:bg-wood border border-wood px-3 py-1.5 rounded-full transition-colors">
                        + Keranjang
                    </button>
                    @else
                    <span class="text-xs text-red-500 font-medium">Habis</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const wishlistIds = @json($items->pluck('id')->toArray());

async function removeWishlist(barangId) {
    try {
        const res  = await fetch(`/wishlist/${barangId}`, {
            method:  'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const data = await res.json();
        if (data.success && !data.in_wishlist) {
            const card = document.getElementById(`wish-card-${barangId}`);
            card?.remove();
            // Update jumlah
            const remaining = document.querySelectorAll('[id^="wish-card-"]').length;
            if (remaining === 0) location.reload();
            showToast('Dihapus dari wishlist.');
        }
    } catch(e) {}
}

async function addAllToCart() {
    for (const id of wishlistIds) {
        await CartDrawer.add(id, false);
    }
    showToast('Semua produk ditambahkan ke keranjang!');
}
</script>
@endpush
