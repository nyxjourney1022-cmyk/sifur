@if($products->isEmpty())
<div class="text-center py-24">
    <div class="w-20 h-20 bg-sand rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
    </div>
    <h3 class="font-serif text-2xl font-medium text-ink mb-2">Produk tidak ditemukan</h3>
    <p class="text-muted mb-6">Coba ubah filter atau lihat semua kategori.</p>
    <button onclick="filterKatalog('')"
            class="inline-flex items-center gap-2 bg-wood text-white font-semibold px-6 py-3 rounded-full hover:bg-wood-dark transition-colors">
        Lihat Semua Produk
    </button>
</div>

@elseif($selectedKategori)
{{-- Grid biasa saat satu kategori dipilih --}}
<div class="mb-10">
    <h2 class="font-serif text-3xl font-medium text-ink">{{ $selectedKategori }}</h2>
    <p class="text-muted mt-1">{{ $products->count() }} produk ditemukan</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($products as $product)
        @include('partials.product-card', ['product' => $product, 'whatsapp' => $whatsapp])
    @endforeach
</div>

@else
{{-- Horizontal scroll per kategori --}}
@foreach($byCategory as $kategori => $items)
<div class="hs-section mb-16">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-serif text-3xl font-medium text-ink">{{ $kategori }}</h2>
            <p class="text-sm text-muted mt-0.5">{{ $items->count() }} produk</p>
        </div>
        <button onclick="filterKatalog('{{ $kategori }}')"
                class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-wood hover:text-wood-dark transition-colors">
            Lihat semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </button>
    </div>

    <div class="relative -mx-6 lg:-mx-8">
        <div class="hs-row flex gap-5 overflow-x-auto scroll-x snap-x-mandatory pb-3 px-6 lg:px-8">
            @foreach($items as $product)
            <div class="snap-start shrink-0 w-64 sm:w-72">
                @include('partials.product-card', ['product' => $product, 'compact' => true, 'whatsapp' => $whatsapp])
            </div>
            @endforeach

            <div class="snap-start shrink-0 w-48 flex items-center justify-center">
                <button onclick="filterKatalog('{{ $kategori }}')"
                        class="flex flex-col items-center gap-3 p-8 rounded-3xl border-2 border-dashed border-sand hover:border-wood transition-colors group text-center w-full h-full min-h-[280px] justify-center">
                    <div class="w-12 h-12 rounded-full bg-sand group-hover:bg-wood/10 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-muted group-hover:text-wood transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-muted group-hover:text-wood transition-colors">Lihat semua {{ $kategori }}</span>
                </button>
            </div>
        </div>

        <button class="hs-btn-left absolute left-1 top-1/2 -translate-y-6 w-9 h-9 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10 opacity-0 pointer-events-none">
            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button class="hs-btn-right absolute right-1 top-1/2 -translate-y-6 w-9 h-9 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-sand transition-colors z-10">
            <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
@endforeach
@endif
