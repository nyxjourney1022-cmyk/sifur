@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ --}}
<section class="relative min-h-[92vh] flex items-center overflow-hidden bg-cream">

    {{-- Background decorative shapes --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full bg-sand opacity-60"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 rounded-full bg-sand opacity-40"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 w-full py-20 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

        {{-- Text --}}
        <div class="fade-up">
            <span class="inline-block text-xs font-semibold tracking-[.18em] uppercase text-olive mb-5">
                Furniture Kayu Berkualitas
            </span>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl font-medium text-ink leading-[1.05] mb-6">
                Ruang yang nyaman dimulai dari
                <em class="not-italic text-wood">furniture</em>
                yang tepat
            </h1>
            <p class="text-muted text-lg leading-relaxed mb-10 max-w-lg">
                Dibuat dari kayu pilihan oleh pengrajin berpengalaman, untuk rumah, kantor, dan hunian Anda.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ url('/katalog') }}"
                   class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-7 py-3.5 rounded-full hover:bg-wood-dark transition-colors shadow-lg shadow-wood/20">
                    Lihat Katalog
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                <a href="#kontak"
                   class="inline-flex items-center gap-2 border-2 border-ink/20 text-ink font-semibold px-7 py-3.5 rounded-full hover:border-ink transition-colors">
                    Hubungi Kami
                </a>
            </div>

            {{-- Stats --}}
            @if(!empty($stats))
            <div class="flex flex-wrap gap-8 mt-14 pt-10 border-t border-sand">
                @foreach($stats as $stat)
                <div>
                    <p class="font-serif text-3xl font-medium text-ink">{{ data_get($stat, 'value') }}</p>
                    <p class="text-sm text-muted mt-0.5">{{ data_get($stat, 'label') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Visual --}}
        <div class="fade-up-2 relative hidden lg:block">
            <div class="relative w-full aspect-square max-w-lg mx-auto">

                {{-- Foto hero --}}
                <div class="absolute inset-0 rounded-[3rem] overflow-hidden shadow-2xl">
                    <img src="{{ asset('storage/hero.jpg') }}"
                         alt="Ruang tamu furniture kayu"
                         class="w-full h-full object-cover">
                    {{-- Subtle gradient overlay bawah --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/20 via-transparent to-transparent"></div>
                </div>

                {{-- Floating badge 1 — kanan atas --}}
                <div class="absolute -right-6 top-8 bg-white rounded-2xl shadow-xl px-5 py-3.5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-wood/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-wood" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Kualitas</p>
                        <p class="text-sm font-semibold text-ink">Premium Wood</p>
                    </div>
                </div>

                {{-- Floating badge 2 — kiri bawah --}}
                <div class="absolute -left-6 bottom-10 bg-white rounded-2xl shadow-xl px-5 py-3.5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-olive/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-olive" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Garansi</p>
                        <p class="text-sm font-semibold text-ink">2 Tahun</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     KATEGORI
═══════════════════════════════════════════════════════ --}}
<section id="koleksi" class="py-24 bg-sand">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="mb-12">
            <span class="text-xs font-semibold tracking-widest uppercase text-olive">Koleksi Kami</span>
            <h2 class="font-serif text-4xl md:text-5xl font-medium text-ink mt-2">Belanja berdasarkan kategori</h2>
            <p class="text-muted mt-3 max-w-xl">Pilih furniture sesuai ruangan yang ingin Anda lengkapi.</p>
        </div>

        @if($categories->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $i => $category)
            @php
                $colors = [
                    'bg-wood text-white',
                    'bg-olive text-white',
                    'bg-ink text-white',
                    'bg-wood-dark text-white',
                ];
                $color = $colors[$i % 4];
            @endphp
            <a href="{{ url('/katalog') }}?kategori={{ urlencode(data_get($category, 'name')) }}"
               class="group {{ $color }} rounded-3xl p-7 min-h-[160px] flex flex-col justify-end hover:scale-[1.02] transition-transform cursor-pointer">
                <strong class="font-serif text-2xl font-medium block leading-tight">{{ data_get($category, 'name') }}</strong>
                <span class="text-sm opacity-75 mt-1">{{ data_get($category, 'room') }}</span>
            </a>
            @endforeach
        </div>
        @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([['Ruang Tamu','Living Room','bg-wood'],['Kamar Tidur','Bedroom','bg-olive'],['Ruang Makan','Dining Room','bg-ink'],['Ruang Kerja','Home Office','bg-wood-dark']] as $cat)
            <div class="{{ $cat[2] }} text-white rounded-3xl p-7 min-h-[160px] flex flex-col justify-end">
                <strong class="font-serif text-2xl font-medium">{{ $cat[0] }}</strong>
                <span class="text-sm opacity-75 mt-1">{{ $cat[1] }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     PRODUK UNGGULAN — Horizontal Scroll
═══════════════════════════════════════════════════════ --}}
<section id="produk" class="py-24 bg-cream overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div>
                <span class="text-xs font-semibold tracking-widest uppercase text-olive">Pilihan Terbaik</span>
                <h2 class="font-serif text-4xl md:text-5xl font-medium text-ink mt-2">Produk unggulan</h2>
            </div>
            <a href="{{ url('/katalog') }}"
               class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-wood hover:text-wood-dark transition-colors">
                Lihat semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Scroll container — full bleed --}}
    <div class="relative">
        <div id="prod-scroll"
             class="flex gap-6 overflow-x-auto scroll-x snap-x-mandatory pb-4 px-6 lg:px-8"
             style="scroll-padding-left: 2rem;">
            @forelse($products as $product)
            <div class="snap-start shrink-0 w-72 group">
                {{-- Image --}}
                <div class="w-full h-64 rounded-3xl overflow-hidden bg-sand mb-5 relative">
                    @if(data_get($product, 'image'))
                        <img src="{{ data_get($product, 'image') }}"
                             alt="{{ data_get($product, 'name') }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-6">
                            <div class="w-3/5 h-28 bg-wood rounded-t-2xl rounded-b-lg"></div>
                        </div>
                    @endif
                    {{-- Stok badge --}}
                    @if(data_get($product, 'stok') <= 3)
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                        Stok Terbatas
                    </span>
                    @endif
                </div>
                {{-- Info --}}
                <div class="px-1">
                    <span class="text-xs font-medium text-olive uppercase tracking-wider">{{ data_get($product, 'kategori') }}</span>
                    <h3 class="font-semibold text-ink text-lg mt-1 mb-1 leading-snug">{{ data_get($product, 'name') }}</h3>
                    @if(data_get($product, 'kayu'))
                    <p class="text-xs text-muted mb-2">Kayu {{ data_get($product, 'kayu') }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-3">
                        <p class="font-serif text-xl font-medium text-wood">
                            Rp {{ number_format((float) data_get($product, 'price', 0), 0, ',', '.') }}
                        </p>
                        <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}?text={{ urlencode('Halo, saya tertarik dengan produk ' . data_get($product, 'name')) }}"
                           target="_blank"
                           class="w-9 h-9 flex items-center justify-center bg-wood/10 hover:bg-wood hover:text-white text-wood rounded-full transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="w-full text-center py-16 text-muted">
                <p>Produk belum tersedia.</p>
            </div>
            @endforelse

            {{-- Last card: Lihat Semua --}}
            <div class="snap-start shrink-0 w-72 flex items-center justify-center">
                <a href="{{ url('/katalog') }}"
                   class="flex flex-col items-center gap-4 p-10 rounded-3xl border-2 border-dashed border-sand hover:border-wood transition-colors group text-center">
                    <div class="w-14 h-14 rounded-full bg-sand group-hover:bg-wood/10 flex items-center justify-center transition-colors">
                        <svg class="w-6 h-6 text-muted group-hover:text-wood transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-muted group-hover:text-wood transition-colors text-sm">Lihat semua produk</span>
                </a>
            </div>
        </div>

        {{-- Scroll buttons --}}
        <button id="scroll-left"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10 opacity-0 pointer-events-none"
                aria-label="Scroll kiri">
            <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button id="scroll-right"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10"
                aria-label="Scroll kanan">
            <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     TENTANG
═══════════════════════════════════════════════════════ --}}
<section id="tentang" class="py-24 bg-olive text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-xs font-semibold tracking-widest uppercase text-white/60 mb-4 block">Tentang Kami</span>
                <h2 class="font-serif text-4xl md:text-5xl font-medium leading-tight mb-6">
                    Dibuat dengan teliti,<br>dipakai bertahun-tahun
                </h2>
                <p class="text-white/80 text-lg leading-relaxed mb-8">
                    {{ $about ?? 'Kami adalah toko furniture kayu pilihan yang telah berdiri sejak 2019.' }}
                </p>
                <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-white text-olive font-semibold px-7 py-3.5 rounded-full hover:bg-cream transition-colors">
                    Konsultasi Gratis
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>

            @if(!empty($stats))
            <div class="grid grid-cols-3 gap-6">
                @foreach($stats as $stat)
                <div class="bg-white/10 rounded-3xl p-6 text-center backdrop-blur-sm">
                    <p class="font-serif text-4xl font-medium text-white mb-1">{{ data_get($stat, 'value') }}</p>
                    <p class="text-sm text-white/70">{{ data_get($stat, 'label') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     KEUNGGULAN
═══════════════════════════════════════════════════════ --}}
<section class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-xs font-semibold tracking-widest uppercase text-olive">Kenapa Kami</span>
            <h2 class="font-serif text-4xl md:text-5xl font-medium text-ink mt-2">Keunggulan kami</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title'=>'Bahan Berkualitas', 'desc'=>'Kayu pilihan yang telah melewati seleksi ketat untuk ketahanan dan keindahan.'],
                ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title'=>'Pengerjaan Cepat', 'desc'=>'Estimasi pengerjaan yang jelas dan tepat waktu sesuai perjanjian.'],
                ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title'=>'Harga Transparan', 'desc'=>'Tidak ada biaya tersembunyi. Harga yang kami tawarkan sudah final.'],
                ['icon'=>'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title'=>'Garansi 2 Tahun', 'desc'=>'Kami percaya diri dengan kualitas produk kami, buktikan sendiri.'],
            ] as $item)
            <div class="bg-sand rounded-3xl p-7">
                <div class="w-12 h-12 bg-wood/10 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-wood" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink text-lg mb-2">{{ $item['title'] }}</h3>
                <p class="text-muted text-sm leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CTA / KONTAK
═══════════════════════════════════════════════════════ --}}
<section id="kontak" class="py-24 bg-sand">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="bg-ink rounded-[2.5rem] px-8 md:px-16 py-16 text-center relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-wood/10"></div>
            <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full bg-olive/10"></div>

            <div class="relative">
                <span class="text-xs font-semibold tracking-widest uppercase text-white/50 mb-4 block">Hubungi Kami</span>
                <h2 class="font-serif text-4xl md:text-5xl font-medium text-white mb-5">
                    Ingin furniture custom?
                </h2>
                <p class="text-white/60 text-lg mb-10 max-w-xl mx-auto">
                    Ceritakan kebutuhan Anda, kami bantu dari desain sampai pengiriman ke seluruh Indonesia.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}"
                       target="_blank"
                       class="inline-flex items-center gap-3 bg-white text-ink font-semibold px-8 py-4 rounded-full hover:bg-cream transition-colors shadow-lg">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Chat via WhatsApp
                    </a>
                    <a href="{{ url('/katalog') }}"
                       class="inline-flex items-center gap-2 border-2 border-white/20 text-white font-semibold px-8 py-4 rounded-full hover:border-white/50 transition-colors">
                        Lihat Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    const scrollEl = document.getElementById('prod-scroll');
    const btnLeft  = document.getElementById('scroll-left');
    const btnRight = document.getElementById('scroll-right');
    const STEP     = 320;

    function updateButtons() {
        if (!scrollEl) return;
        const atStart = scrollEl.scrollLeft <= 10;
        const atEnd   = scrollEl.scrollLeft + scrollEl.clientWidth >= scrollEl.scrollWidth - 10;
        btnLeft.classList.toggle('opacity-0', atStart);
        btnLeft.classList.toggle('pointer-events-none', atStart);
        btnRight.classList.toggle('opacity-0', atEnd);
        btnRight.classList.toggle('pointer-events-none', atEnd);
    }

    btnLeft?.addEventListener('click',  () => { scrollEl.scrollBy({ left: -STEP, behavior: 'smooth' }); });
    btnRight?.addEventListener('click', () => { scrollEl.scrollBy({ left:  STEP, behavior: 'smooth' }); });
    scrollEl?.addEventListener('scroll', updateButtons);
    updateButtons();
</script>
@endpush
