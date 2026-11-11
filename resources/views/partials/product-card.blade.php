{{--
    Product Card Partial
    Props:
        $product  (array)  — id, name, price, kategori, stok, kayu, image
        $compact  (bool)   — optional, smaller height for horizontal scroll
        $whatsapp (string) — from parent layout scope
--}}
@php
    $isCompact = $compact ?? false;
    $imgH      = $isCompact ? 'h-52' : 'h-64';
    $wa        = $whatsapp ?? '62xxxxxxxxxx';
@endphp

<div class="group bg-cream rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    {{-- Image --}}
    <div class="w-full {{ $imgH }} bg-sand overflow-hidden relative">
        @if(data_get($product, 'image'))
            <img src="{{ data_get($product, 'image') }}"
                 alt="{{ data_get($product, 'name') }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-end justify-center pb-6 bg-gradient-to-br from-sand to-[#ddd0bc]">
                <div class="w-3/5 h-24 bg-wood/70 rounded-t-2xl rounded-b-lg"></div>
            </div>
        @endif

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if(data_get($product, 'stok') <= 3 && data_get($product, 'stok') > 0)
            <span class="bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                STOK TERBATAS
            </span>
            @endif
            @if(data_get($product, 'stok') <= 0)
            <span class="bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                HABIS
            </span>
            @endif
        </div>

        {{-- Hover actions --}}
        <div class="absolute bottom-3 right-3 flex gap-2">
            {{-- Add to Cart --}}
            @if(data_get($product, 'stok') > 0)
            <button onclick="CartDrawer.add({{ data_get($product, 'id') }})"
                    class="w-9 h-9 bg-wood/90 hover:bg-wood backdrop-blur-sm rounded-full flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200"
                    title="Tambah ke keranjang">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </button>
            @endif

            {{-- WA Quick --}}
            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya tertarik dengan produk ' . data_get($product, 'name') . '. Apakah masih tersedia?') }}"
               target="_blank"
               class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200 delay-75 hover:bg-green-50"
               title="Tanya via WhatsApp">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Info --}}
    <div class="p-5">
        <div class="flex items-start justify-between gap-2 mb-1">
            <span class="text-[11px] font-semibold tracking-wider uppercase text-olive">
                {{ data_get($product, 'kategori', '—') }}
            </span>
            @if(data_get($product, 'kayu'))
            <span class="text-[11px] text-muted bg-sand px-2 py-0.5 rounded-full whitespace-nowrap">
                {{ data_get($product, 'kayu') }}
            </span>
            @endif
        </div>
        <h3 class="font-semibold text-ink text-base leading-snug mt-1">
            {{ data_get($product, 'name') }}
        </h3>
        <div class="flex items-center justify-between mt-3">
            <p class="font-serif text-xl font-medium text-wood">
                Rp {{ number_format((float) data_get($product, 'price', 0), 0, ',', '.') }}
            </p>
            @if(data_get($product, 'stok') > 0)
            <button onclick="CartDrawer.add({{ data_get($product, 'id') }})"
                    class="text-xs font-semibold text-wood hover:text-white hover:bg-wood border border-wood px-3 py-1.5 rounded-full transition-colors">
                + Keranjang
            </button>
            @else
            <span class="text-xs text-red-500 font-medium">Habis</span>
            @endif
        </div>
    </div>
</div>
