@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('description', 'Jelajahi seluruh koleksi furniture kayu berkualitas kami.')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════════════ --}}
<section class="bg-sand pt-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <span class="text-xs font-semibold tracking-widest uppercase text-olive">Koleksi Lengkap</span>
                <h1 class="font-serif text-5xl md:text-6xl font-medium text-ink mt-2">Katalog Produk</h1>
                <p class="text-muted mt-3 text-lg">
                    {{ $products->count() }} produk tersedia untuk Anda
                </p>
            </div>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-ink transition-colors self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FILTER BAR
═══════════════════════════════════════════════════════ --}}
<div class="sticky top-16 z-40 bg-cream/90 backdrop-blur-md border-b border-sand shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <form method="GET" action="{{ url('/katalog') }}" id="filter-form"
              class="flex flex-wrap items-center gap-3 py-4">

            {{-- Kategori pills --}}
            <div class="flex items-center gap-2 overflow-x-auto scroll-x pb-1 flex-1 min-w-0">
                <a href="{{ url('/katalog') }}{{ $selectedSort ? '?sort='.$selectedSort : '' }}"
                   class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap
                          {{ !$selectedKategori ? 'bg-ink text-white' : 'bg-sand hover:bg-ink/10 text-muted' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ url('/katalog') }}?kategori={{ urlencode($cat) }}{{ $selectedSort ? '&sort='.$selectedSort : '' }}"
                   class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap
                          {{ $selectedKategori === $cat ? 'bg-wood text-white' : 'bg-sand hover:bg-wood/10 text-muted' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>

            {{-- Sort dropdown --}}
            <select name="sort" onchange="this.form.submit()"
                    class="shrink-0 bg-sand border-0 text-sm font-medium text-ink rounded-full px-4 py-2 cursor-pointer focus:ring-2 focus:ring-wood focus:outline-none appearance-none pr-8"
                    style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em;">
                @if($selectedKategori)
                <input type="hidden" name="kategori" value="{{ $selectedKategori }}">
                @endif
                <option value="terbaru"    {{ $selectedSort === 'terbaru'    ? 'selected' : '' }}>Terbaru</option>
                <option value="harga_asc"  {{ $selectedSort === 'harga_asc'  ? 'selected' : '' }}>Harga: Termurah</option>
                <option value="harga_desc" {{ $selectedSort === 'harga_desc' ? 'selected' : '' }}>Harga: Termahal</option>
                <option value="nama"       {{ $selectedSort === 'nama'       ? 'selected' : '' }}>Nama A–Z</option>
            </select>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     KONTEN KATALOG
═══════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

    @if($products->isEmpty())
    {{-- Empty state --}}
    <div class="text-center py-24">
        <div class="w-20 h-20 bg-sand rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
        </div>
        <h3 class="font-serif text-2xl font-medium text-ink mb-2">Produk tidak ditemukan</h3>
        <p class="text-muted mb-6">Coba ubah filter atau lihat semua kategori.</p>
        <a href="{{ url('/katalog') }}"
           class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-6 py-3 rounded-full hover:bg-wood-dark transition-colors">
            Lihat Semua Produk
        </a>
    </div>

    @elseif($selectedKategori)
    {{-- ── Mode: satu kategori dipilih → Grid biasa ── --}}
    <div class="mb-10">
        <h2 class="font-serif text-3xl font-medium text-ink">{{ $selectedKategori }}</h2>
        <p class="text-muted mt-1">{{ $products->count() }} produk ditemukan</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>

    @else
    {{-- ── Mode: semua kategori → Horizontal scroll per kategori ── --}}
    @foreach($byCategory as $kategori => $items)
    <div class="mb-16">
        {{-- Section header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-serif text-3xl font-medium text-ink">{{ $kategori }}</h2>
                <p class="text-sm text-muted mt-0.5">{{ $items->count() }} produk</p>
            </div>
            <a href="{{ url('/katalog') }}?kategori={{ urlencode($kategori) }}"
               class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-wood hover:text-wood-dark transition-colors">
                Lihat semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        {{-- Horizontal scroll row --}}
        <div class="relative -mx-6 lg:-mx-8">
            <div class="hs-row flex gap-5 overflow-x-auto scroll-x snap-x-mandatory pb-3 px-6 lg:px-8">
                @foreach($items as $product)
                <div class="snap-start shrink-0 w-64 sm:w-72">
                    @include('partials.product-card', ['product' => $product, 'compact' => true])
                </div>
                @endforeach

                {{-- See more card --}}
                <div class="snap-start shrink-0 w-48 flex items-center justify-center">
                    <a href="{{ url('/katalog') }}?kategori={{ urlencode($kategori) }}"
                       class="flex flex-col items-center gap-3 p-8 rounded-3xl border-2 border-dashed border-sand hover:border-wood transition-colors group text-center w-full h-full min-h-[280px] justify-center">
                        <div class="w-12 h-12 rounded-full bg-sand group-hover:bg-wood/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-muted group-hover:text-wood transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-muted group-hover:text-wood transition-colors">Lihat semua {{ $kategori }}</span>
                    </a>
                </div>
            </div>

            {{-- Scroll buttons per row --}}
            <button class="hs-btn-left absolute left-1 top-1/2 -translate-y-6 w-9 h-9 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10 opacity-0 pointer-events-none"
                    aria-label="Scroll kiri">
                <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="hs-btn-right absolute right-1 top-1/2 -translate-y-6 w-9 h-9 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10"
                    aria-label="Scroll kanan">
                <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
    @endforeach
    @endif

</div>

{{-- ═══════════════════════════════════════════════════════
     CTA BAWAH
═══════════════════════════════════════════════════════ --}}
<section class="py-16 bg-sand">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="bg-ink rounded-[2rem] px-8 md:px-14 py-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <h3 class="font-serif text-3xl font-medium text-white mb-2">Tidak menemukan yang Anda cari?</h3>
                <p class="text-white/60">Kami menerima pesanan custom sesuai kebutuhan dan ukuran Anda.</p>
            </div>
            <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}"
               target="_blank"
               class="shrink-0 inline-flex items-center gap-3 bg-white text-ink font-semibold px-7 py-3.5 rounded-full hover:bg-cream transition-colors shadow-lg whitespace-nowrap">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Pesan Custom
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Horizontal scroll buttons per row
    document.querySelectorAll('.mb-16').forEach(section => {
        const row      = section.querySelector('.hs-row');
        const btnLeft  = section.querySelector('.hs-btn-left');
        const btnRight = section.querySelector('.hs-btn-right');
        const STEP     = 300;

        if (!row || !btnLeft || !btnRight) return;

        function update() {
            const atStart = row.scrollLeft <= 10;
            const atEnd   = row.scrollLeft + row.clientWidth >= row.scrollWidth - 10;
            btnLeft.classList.toggle('opacity-0',          atStart);
            btnLeft.classList.toggle('pointer-events-none', atStart);
            btnRight.classList.toggle('opacity-0',          atEnd);
            btnRight.classList.toggle('pointer-events-none', atEnd);
        }

        btnLeft.addEventListener('click',  () => row.scrollBy({ left: -STEP, behavior: 'smooth' }));
        btnRight.addEventListener('click', () => row.scrollBy({ left:  STEP, behavior: 'smooth' }));
        row.addEventListener('scroll', update);
        update();
    });
</script>
@endpush
