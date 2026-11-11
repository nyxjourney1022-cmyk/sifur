@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('description', 'Jelajahi seluruh koleksi furniture kayu berkualitas kami.')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════════════ --}}
<section class="bg-sand pt-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        {{-- Tombol kembali — kiri atas --}}
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-ink transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Kembali ke Beranda
        </a>
        <div>
            <span class="text-xs font-semibold tracking-widest uppercase text-olive">Koleksi Lengkap</span>
            <h1 class="font-serif text-5xl md:text-6xl font-medium text-ink mt-2">Katalog Produk</h1>
            <p class="text-muted mt-3 text-lg" id="product-count">
                {{ $products->count() }} produk tersedia untuk Anda
            </p>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FILTER BAR
═══════════════════════════════════════════════════════ --}}
<div class="sticky top-16 z-40 bg-cream/90 backdrop-blur-md border-b border-sand shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-3 py-4">

            {{-- Kategori pills --}}
            <div class="flex items-center gap-2 overflow-x-auto scroll-x pb-1 flex-1 min-w-0">
                <button onclick="filterKatalog('')"
                   class="cat-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap
                          {{ !$selectedKategori ? 'bg-ink text-white' : 'bg-sand hover:bg-ink/10 text-muted' }}"
                   data-kategori="">
                    Semua
                </button>
                @foreach($categories as $cat)
                <button onclick="filterKatalog('{{ $cat }}')"
                   class="cat-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap
                          {{ $selectedKategori === $cat ? 'bg-wood text-white' : 'bg-sand hover:bg-wood/10 text-muted' }}"
                   data-kategori="{{ $cat }}">
                    {{ $cat }}
                </button>
                @endforeach
            </div>

            {{-- Sort dropdown --}}
            <select id="sort-select"
                    onchange="filterKatalog(currentKategori, this.value)"
                    class="shrink-0 bg-sand border-0 text-sm font-medium text-ink rounded-full px-4 py-2 cursor-pointer focus:ring-2 focus:ring-wood focus:outline-none appearance-none pr-8"
                    style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em;">
                <option value="terbaru"    {{ $selectedSort === 'terbaru'    ? 'selected' : '' }}>Terbaru</option>
                <option value="harga_asc"  {{ $selectedSort === 'harga_asc'  ? 'selected' : '' }}>Harga: Termurah</option>
                <option value="harga_desc" {{ $selectedSort === 'harga_desc' ? 'selected' : '' }}>Harga: Termahal</option>
                <option value="nama"       {{ $selectedSort === 'nama'       ? 'selected' : '' }}>Nama A–Z</option>
            </select>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     KONTEN KATALOG
═══════════════════════════════════════════════════════ --}}
<div id="katalog-content" class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
    @include('partials.katalog-content', ['products' => $products, 'byCategory' => $byCategory, 'selectedKategori' => $selectedKategori, 'whatsapp' => $whatsapp])
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
// ── State ─────────────────────────────────────────────────────
let currentKategori = @json($selectedKategori ?? '');
let currentSort     = @json($selectedSort ?? 'terbaru');

// ── Filter utama (kategori + sort) ────────────────────────────
async function filterKatalog(kategori = '', sort = null) {
    currentKategori = kategori ?? '';
    if (sort !== null) currentSort = sort;

    // Update pill aktif
    document.querySelectorAll('.cat-pill').forEach(el => {
        const isActive = el.dataset.kategori === currentKategori;
        el.className = el.className
            .replace(/bg-ink|bg-wood|bg-sand|text-white|text-muted/g, '').trim();
        if (isActive && !currentKategori) {
            el.classList.add('bg-ink', 'text-white');
        } else if (isActive && currentKategori) {
            el.classList.add('bg-wood', 'text-white');
        } else {
            el.classList.add('bg-sand', 'text-muted');
        }
    });

    // Update sort select
    const sel = document.getElementById('sort-select');
    if (sel) sel.value = currentSort;

    // Update URL tanpa reload
    const params = new URLSearchParams();
    if (currentKategori) params.set('kategori', currentKategori);
    if (currentSort && currentSort !== 'terbaru') params.set('sort', currentSort);
    history.pushState({}, '', '/katalog' + (params.toString() ? '?' + params.toString() : ''));

    // Fetch produk via AJAX
    const content = document.getElementById('katalog-content');
    content.style.opacity = '0.4';
    content.style.transition = 'opacity .2s';

    try {
        const res  = await fetch('/katalog/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        const countEl = document.getElementById('product-count');
        if (countEl) countEl.textContent = data.count + ' produk tersedia untuk Anda';

        content.innerHTML = data.html;
        content.style.opacity = '1';
        initScrollButtons();
    } catch(e) {
        content.style.opacity = '1';
    }
}

// ── Horizontal scroll buttons ─────────────────────────────────
function initScrollButtons() {
    document.querySelectorAll('.hs-section').forEach(section => {
        const row      = section.querySelector('.hs-row');
        const btnLeft  = section.querySelector('.hs-btn-left');
        const btnRight = section.querySelector('.hs-btn-right');
        const STEP     = 300;
        if (!row || !btnLeft || !btnRight) return;

        function update() {
            const atStart = row.scrollLeft <= 10;
            const atEnd   = row.scrollLeft + row.clientWidth >= row.scrollWidth - 10;
            btnLeft.classList.toggle('opacity-0',            atStart);
            btnLeft.classList.toggle('pointer-events-none',  atStart);
            btnRight.classList.toggle('opacity-0',           atEnd);
            btnRight.classList.toggle('pointer-events-none', atEnd);
        }
        btnLeft.onclick  = () => row.scrollBy({ left: -STEP, behavior: 'smooth' });
        btnRight.onclick = () => row.scrollBy({ left:  STEP, behavior: 'smooth' });
        row.addEventListener('scroll', update);
        update();
    });
}

document.addEventListener('DOMContentLoaded', initScrollButtons);
</script>
@endpush
