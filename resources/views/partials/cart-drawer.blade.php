{{-- Cart Drawer Overlay + Panel --}}
<div id="cart-overlay"
     onclick="CartDrawer.close()"
     class="fixed inset-0 bg-ink/40 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-opacity duration-300">
</div>

<div id="cart-drawer"
     class="fixed top-0 right-0 h-full w-full max-w-sm bg-cream z-[70] translate-x-full transition-transform duration-300 flex flex-col shadow-2xl">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-sand">
        <h2 class="font-serif text-xl font-medium text-ink">Keranjang</h2>
        <button onclick="CartDrawer.close()" class="p-2 rounded-full hover:bg-sand transition-colors" aria-label="Tutup">
            <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Body --}}
    <div id="cart-body" class="flex-1 overflow-y-auto scroll-y px-6">
        @guest
        <div class="flex flex-col items-center justify-center h-full text-center py-16">
            <div class="w-16 h-16 bg-sand rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="font-serif text-xl text-ink mb-1">Belum login</p>
            <p class="text-sm text-muted mb-5">Login dulu untuk menggunakan keranjang belanja</p>
            <a href="{{ route('login') }}"
               class="bg-wood text-white text-sm font-semibold px-6 py-2.5 rounded-full hover:bg-wood-dark transition-colors">
                Masuk Sekarang
            </a>
            <a href="{{ route('register') }}"
               class="mt-2 text-sm text-muted hover:text-ink transition-colors">
                Atau buat akun baru
            </a>
        </div>
        @endguest
    </div>

    {{-- Footer / checkout --}}
    <div id="cart-footer" class="@guest hidden @endguest border-t border-sand px-6 py-5 bg-white">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-muted font-medium">Total</span>
            <span id="cart-total" class="font-serif text-xl font-medium text-wood">Rp 0</span>
        </div>
        <a href="{{ route('checkout') }}"
           class="block text-center bg-wood hover:bg-wood-dark text-white font-semibold py-3.5 rounded-full transition-colors">
            Lanjut ke Checkout
        </a>
        <button onclick="CartDrawer.close()"
                class="block w-full text-center text-sm text-muted hover:text-ink transition-colors mt-3 py-1">
            Lanjut Belanja
        </button>
    </div>
</div>
