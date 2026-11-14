<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $store_name ?? config('app.name')) – Furniture Kayu Berkualitas</title>
    <meta name="description" content="@yield('description', 'Furniture kayu berkualitas untuk rumah, kantor, dan hunian Anda.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    </style>
    @stack('styles')
</head>
<body class="bg-cream text-ink antialiased">

{{-- ══ NAVBAR ══════════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-50 bg-cream/80 backdrop-blur-md border-b border-sand">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="font-serif text-2xl font-semibold text-ink tracking-tight">
            {{ $store_name ?? config('app.name') }}
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-muted">
            <a href="{{ route('home') }}#koleksi" class="nav-link hover:text-ink transition-colors">Koleksi</a>
            <a href="{{ route('katalog') }}"      class="nav-link hover:text-ink transition-colors">Katalog</a>
            <a href="{{ route('home') }}#tentang" class="nav-link hover:text-ink transition-colors">Tentang</a>
            <a href="{{ route('home') }}#kontak"  class="nav-link hover:text-ink transition-colors">Kontak</a>
        </nav>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">

            {{-- Cart button --}}
            <button id="cart-btn" onclick="CartDrawer.open()"
                    class="relative p-2.5 rounded-full hover:bg-sand transition-colors" aria-label="Keranjang">
                <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span id="cart-badge"
                      class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-wood text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">
                    0
                </span>
            </button>

            @auth
            {{-- User dropdown --}}
            <div class="relative hidden md:block" id="user-menu-wrapper">
                <button id="user-btn" onclick="toggleUserMenu()"
                        class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full hover:bg-sand transition-colors">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="" 
                             referrerpolicy="no-referrer"
                             class="w-7 h-7 rounded-full object-cover">
                    @else
                        <div class="w-7 h-7 rounded-full bg-wood text-white text-xs font-bold flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-sm font-medium text-ink max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                    <svg class="w-3.5 h-3.5 text-muted" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-dropdown"
                     class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-sand overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-sand">
                        <p class="text-xs text-muted">Masuk sebagai</p>
                        <p class="text-sm font-semibold text-ink truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profil') }}"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-ink hover:bg-sand transition-colors">
                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil & Alamat
                    </a>
                    <a href="{{ route('pesanan') }}"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-ink hover:bg-sand transition-colors">
                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Pesanan Saya
                    </a>
                    <div class="border-t border-sand">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-muted hover:text-ink transition-colors px-4 py-2">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                   class="text-sm font-semibold bg-wood text-white px-5 py-2.5 rounded-full hover:bg-wood-dark transition-colors">
                    Daftar
                </a>
            </div>
            @endauth

            {{-- Mobile hamburger --}}
            <button id="menu-btn" class="md:hidden p-2 rounded-lg hover:bg-sand transition-colors" aria-label="Menu">
                <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-sand bg-cream/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col gap-3 text-sm font-medium">
            <a href="{{ route('home') }}#koleksi" class="text-muted hover:text-ink py-1">Koleksi</a>
            <a href="{{ route('katalog') }}"      class="text-muted hover:text-ink py-1">Katalog</a>
            <a href="{{ route('home') }}#tentang" class="text-muted hover:text-ink py-1">Tentang</a>
            <a href="{{ route('home') }}#kontak"  class="text-muted hover:text-ink py-1">Kontak</a>
            <div class="border-t border-sand pt-3 flex flex-col gap-2">
                @auth
                    <a href="{{ route('profil') }}" class="text-ink font-semibold py-1">Profil & Alamat</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 font-semibold py-1 text-left w-full">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"    class="text-center border-2 border-ink/20 text-ink font-semibold px-5 py-3 rounded-full">Masuk</a>
                    <a href="{{ route('register') }}" class="text-center bg-wood text-white font-semibold px-5 py-3 rounded-full">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- ══ CONTENT ═══════════════════════════════════════════════ --}}
<main class="pt-16">
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success'))));
    </script>
    @endif
    @yield('content')
</main>

{{-- ══ CART DRAWER ════════════════════════════════════════════ --}}
@include('partials.cart-drawer')

{{-- ══ TOAST ══════════════════════════════════════════════════ --}}
<div id="toast"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] pointer-events-none opacity-0 translate-y-4">
    <div id="toast-inner" class="bg-ink text-white text-sm font-medium px-5 py-3 rounded-full shadow-xl flex items-center gap-2 whitespace-nowrap">
        <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span id="toast-msg"></span>
    </div>
</div>

{{-- ══ FOOTER ═════════════════════════════════════════════════ --}}
<footer class="bg-ink text-white/70">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div>
                <p class="font-serif text-2xl font-semibold text-white mb-3">{{ $store_name ?? config('app.name') }}</p>
                <p class="text-sm leading-relaxed">Furniture kayu berkualitas untuk rumah, kantor, dan hunian Anda.</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Navigasi</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}"         class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('katalog') }}"      class="hover:text-white transition-colors">Katalog</a></li>
                    <li><a href="{{ route('home') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('home') }}#kontak"  class="hover:text-white transition-colors">Kontak</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Hubungi Kami</p>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-wood" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $address ?? '[ALAMAT SHOWROOM]' }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-wood" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $phone ?? '[NOMOR TELEPON]' }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span>&copy; {{ date('Y') }} {{ $store_name ?? config('app.name') }}. All rights reserved.</span>
            <span>Dibuat dengan ❤ untuk furniture berkualitas</span>
        </div>
    </div>
</footer>

<script>
// Scroll ke atas saat halaman load/refresh
if (history.scrollRestoration) history.scrollRestoration = 'manual';
window.addEventListener('load', () => window.scrollTo(0, 0));

// Mobile menu toggle
const btn       = document.getElementById('menu-btn');
const mobileM  = document.getElementById('mobile-menu');
const iconOpen = document.getElementById('icon-open');
const iconClose= document.getElementById('icon-close');
btn?.addEventListener('click', () => {
    mobileM.classList.toggle('hidden');
    iconOpen.classList.toggle('hidden');
    iconClose.classList.toggle('hidden');
});
mobileM?.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
        mobileM.classList.add('hidden');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
    });
});

// ── User dropdown ────────────────────────────────────────────
function toggleUserMenu() {
    document.getElementById('user-dropdown')?.classList.toggle('hidden');
}
document.addEventListener('click', e => {
    const wrapper = document.getElementById('user-menu-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('user-dropdown')?.classList.add('hidden');
    }
});

// ── Toast helper ─────────────────────────────────────────────
function showToast(msg, isError = false) {
    const toast    = document.getElementById('toast');
    const inner    = document.getElementById('toast-inner');
    const msgEl    = document.getElementById('toast-msg');
    msgEl.textContent = msg;
    inner.className = `${isError ? 'bg-red-700' : 'bg-ink'} text-white text-sm font-medium px-5 py-3 rounded-full shadow-xl flex items-center gap-2 whitespace-nowrap`;
    toast.classList.remove('opacity-0','translate-y-4');
    toast.classList.add('opacity-100','translate-y-0');
    setTimeout(() => {
        toast.classList.add('opacity-0','translate-y-4');
        toast.classList.remove('opacity-100','translate-y-0');
    }, 3000);
}

// ── CSRF helper ──────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

// ── Cart state ───────────────────────────────────────────────
const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

// ── CartDrawer ───────────────────────────────────────────────
const CartDrawer = {
    open() {
        const drawer = document.getElementById('cart-drawer');
        const overlay= document.getElementById('cart-overlay');
        drawer?.classList.remove('translate-x-full');
        overlay?.classList.remove('opacity-0','pointer-events-none');
        if (isLoggedIn) this.load();
    },
    close() {
        document.getElementById('cart-drawer')?.classList.add('translate-x-full');
        const overlay = document.getElementById('cart-overlay');
        overlay?.classList.add('opacity-0','pointer-events-none');
    },
    async load() {
        if (!isLoggedIn) return;
        const body = document.getElementById('cart-body');
        const foot = document.getElementById('cart-footer');
        body.innerHTML = `<div class="flex items-center justify-center py-16"><div class="w-6 h-6 border-2 border-wood border-t-transparent rounded-full animate-spin"></div></div>`;

        try {
            const res  = await fetch('/cart', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
            const data = await res.json();

            if (!data.items?.length) {
                body.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                        <div class="w-16 h-16 bg-sand rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <p class="font-serif text-xl text-ink mb-1">Keranjang kosong</p>
                        <p class="text-sm text-muted">Tambahkan produk dari katalog</p>
                        <a href="/katalog" onclick="CartDrawer.close()"
                           class="mt-5 bg-wood text-white text-sm font-semibold px-6 py-2.5 rounded-full hover:bg-wood-dark transition-colors">
                            Lihat Katalog
                        </a>
                    </div>`;
                foot.classList.add('hidden');
                return;
            }

            body.innerHTML = data.items.map(item => `
                <div class="flex gap-3 py-4 border-b border-sand" id="cart-item-${item.id}">
                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-sand shrink-0">
                        ${item.image
                            ? `<img src="${item.image}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-end justify-center pb-1"><div class="w-8 h-8 bg-wood/60 rounded-t-lg"></div></div>`}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-olive font-medium">${item.kategori ?? ''}</p>
                        <p class="text-sm font-semibold text-ink leading-snug truncate">${item.name}</p>
                        <p class="text-sm text-wood font-medium mt-0.5">${item.harga_fmt}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <button onclick="CartDrawer.changeQty(${item.id}, ${item.jumlah - 1}, ${item.stok})"
                                    class="btn-minus w-6 h-6 rounded-full bg-sand hover:bg-wood/10 flex items-center justify-center text-ink text-sm font-bold transition-colors">−</button>
                            <span id="qty-${item.id}" class="text-sm font-semibold w-5 text-center">${item.jumlah}</span>
                            <button onclick="CartDrawer.changeQty(${item.id}, ${item.jumlah + 1}, ${item.stok})"
                                    class="btn-plus w-6 h-6 rounded-full bg-sand hover:bg-wood/10 flex items-center justify-center text-ink text-sm font-bold transition-colors">+</button>
                        </div>
                    </div>
                    <div class="flex flex-col items-end justify-between shrink-0">
                        <button onclick="CartDrawer.remove(${item.id})"
                                class="p-1 hover:text-red-500 transition-colors text-muted" aria-label="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <p id="sub-${item.id}" class="text-sm font-semibold text-ink">${item.subtotal_fmt}</p>
                    </div>
                </div>`).join('');

            document.getElementById('cart-total').textContent = data.total_fmt;
            foot.classList.remove('hidden');
            updateCartBadge(data.total_items);
        } catch(e) {
            body.innerHTML = `<p class="text-center text-muted py-10 text-sm">Gagal memuat keranjang.</p>`;
        }
    },
    async add(barangId, jumlah = 1) {
        if (!isLoggedIn) {
            window.location.href = '/login';
            return;
        }
        try {
            const res  = await fetch('/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ barang_id: barangId, jumlah }),
            });
            const data = await res.json();
            if (data.success) {
                updateCartBadge(data.total_items);
                showToast(data.message);
                // Reload drawer kalau sedang terbuka supaya data fresh
                if (this.isOpen) this.load();
            } else {
                showToast(data.message, true);
            }
        } catch(e) {
            showToast('Gagal menambahkan ke keranjang.', true);
        }
    },
    async changeQty(itemId, newQty, maxStok) {
        if (newQty < 1) { this.remove(itemId); return; }
        if (newQty > maxStok) { showToast('Melebihi stok yang tersedia.', true); return; }

        // Optimistic update — langsung update DOM tanpa loading
        const qtyEl = document.getElementById(`qty-${itemId}`);
        const oldQty = parseInt(qtyEl?.textContent ?? newQty);
        if (qtyEl) qtyEl.textContent = newQty;

        try {
            const res  = await fetch(`/cart/item/${itemId}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ jumlah: newQty }),
            });
            const data = await res.json();
            if (data.success) {
                // Update subtotal dan total tanpa reload
                document.getElementById(`sub-${itemId}`).textContent = data.subtotal_fmt;
                document.getElementById('cart-total').textContent    = data.total_fmt;
                updateCartBadge(data.total_items);

                // Update onclick buttons ke qty baru
                const btnMinus = document.querySelector(`#cart-item-${itemId} .btn-minus`);
                const btnPlus  = document.querySelector(`#cart-item-${itemId} .btn-plus`);
                if (btnMinus) btnMinus.setAttribute('onclick', `CartDrawer.changeQty(${itemId}, ${newQty - 1}, ${maxStok})`);
                if (btnPlus)  btnPlus.setAttribute('onclick',  `CartDrawer.changeQty(${itemId}, ${newQty + 1}, ${maxStok})`);
            } else {
                // Rollback kalau gagal
                if (qtyEl) qtyEl.textContent = oldQty;
                showToast(data.message, true);
            }
        } catch(e) {
            if (qtyEl) qtyEl.textContent = oldQty;
        }
    },
    async remove(itemId) {
        try {
            const res  = await fetch(`/cart/item/${itemId}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById(`cart-item-${itemId}`)?.remove();
                document.getElementById('cart-total').textContent = data.total_fmt;
                updateCartBadge(data.total_items);
                if (data.total_items === 0) this.load();
            }
        } catch(e) {}
    },
};

// Init badge on load
document.addEventListener('DOMContentLoaded', async () => {
    if (!isLoggedIn) return;
    try {
        const res  = await fetch('/cart', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) updateCartBadge(data.total_items);
    } catch(e) {}
});
</script>
{{-- ══ CS CHAT WIDGET ══════════════════════════════════════════ --}}
<div id="cs-widget" class="fixed bottom-6 right-6 z-[80] flex flex-col items-end gap-3">

    {{-- Chat window --}}
    <div id="cs-window"
         class="hidden w-[340px] bg-white rounded-[1.5rem] shadow-2xl border border-sand overflow-hidden flex flex-col"
         style="max-height: 520px;">

        {{-- Header --}}
        <div class="bg-wood px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm leading-tight">Customer Service</p>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        <span class="text-white/70 text-xs">Online sekarang</span>
                    </div>
                </div>
            </div>
            <button onclick="CSWidget.close()" class="p-1.5 rounded-full hover:bg-white/10 transition-colors" aria-label="Tutup">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div id="cs-messages" class="flex-1 overflow-y-auto scroll-y px-4 py-4 space-y-3 bg-cream/40"
             style="min-height: 280px; max-height: 340px;">
            {{-- Welcome bubble --}}
            <div class="flex gap-2 items-end">
                <div class="w-7 h-7 rounded-full bg-wood shrink-0 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-sm px-3.5 py-2.5 shadow-sm max-w-[220px]">
                    <p class="text-sm text-ink leading-relaxed">Halo! 👋 Ada yang bisa saya bantu seputar produk furniture kami?</p>
                </div>
            </div>
        </div>

        {{-- Suggested questions --}}
        <div id="cs-suggestions" class="px-4 py-2 flex gap-2 overflow-x-auto scroll-x shrink-0 border-t border-sand/60">
            @foreach(['Produk apa saja?', 'Cara pesan?', 'Ada diskon?', 'Lokasi toko?'] as $q)
            <button onclick="CSWidget.sendSuggestion(this, '{{ $q }}')"
                    class="shrink-0 text-xs font-medium text-wood border border-wood/30 bg-wood/5 hover:bg-wood hover:text-white px-3 py-1.5 rounded-full transition-colors whitespace-nowrap">
                {{ $q }}
            </button>
            @endforeach
        </div>

        {{-- Input --}}
        <div class="px-4 py-3 border-t border-sand flex gap-2 shrink-0 bg-white">
            <input id="cs-input" type="text"
                   placeholder="Tulis pesan..."
                   onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); CSWidget.send(); }"
                   class="flex-1 bg-sand border-0 rounded-full px-4 py-2.5 text-sm focus:ring-2 focus:ring-wood focus:outline-none text-ink placeholder-muted">
            <button onclick="CSWidget.send()"
                    class="w-9 h-9 bg-wood hover:bg-wood-dark text-white rounded-full flex items-center justify-center transition-colors shrink-0" aria-label="Kirim">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Bubble toggle button --}}
    <button id="cs-btn" onclick="CSWidget.toggle()"
            class="w-14 h-14 bg-wood hover:bg-wood-dark text-white rounded-full shadow-xl flex items-center justify-center transition-all hover:scale-105 relative"
            aria-label="Customer Service">
        <svg id="cs-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg id="cs-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{-- Unread dot --}}
        <span id="cs-unread" class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full hidden"></span>
    </button>
</div>

<script>
const CSWidget = {
    history:  [],
    isOpen:   false,
    isTyping: false,

    toggle() {
        this.isOpen ? this.close() : this.open();
    },
    open() {
        this.isOpen = true;
        document.getElementById('cs-window').classList.remove('hidden');
        document.getElementById('cs-window').classList.add('flex');
        document.getElementById('cs-icon-open').classList.add('hidden');
        document.getElementById('cs-icon-close').classList.remove('hidden');
        document.getElementById('cs-unread').classList.add('hidden');
        setTimeout(() => document.getElementById('cs-input')?.focus(), 100);
        this.scrollBottom();
    },
    close() {
        this.isOpen = false;
        document.getElementById('cs-window').classList.add('hidden');
        document.getElementById('cs-window').classList.remove('flex');
        document.getElementById('cs-icon-open').classList.remove('hidden');
        document.getElementById('cs-icon-close').classList.add('hidden');
    },

    scrollBottom() {
        const el = document.getElementById('cs-messages');
        if (el) el.scrollTop = el.scrollHeight;
    },

    addBubble(role, text) {
        const msgs   = document.getElementById('cs-messages');
        const isUser = role === 'user';
        const div    = document.createElement('div');
        div.className = `flex gap-2 items-end ${isUser ? 'flex-row-reverse' : ''}`;
        div.innerHTML = `
            ${!isUser ? `<div class="w-7 h-7 rounded-full bg-wood shrink-0 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>` : ''}
            <div class="${isUser
                ? 'bg-wood text-white rounded-2xl rounded-br-sm'
                : 'bg-white text-ink rounded-2xl rounded-bl-sm shadow-sm'} px-3.5 py-2.5 max-w-[220px]">
                <p class="text-sm leading-relaxed">${text.replace(/\n/g, '<br>')}</p>
            </div>`;
        msgs.appendChild(div);
        this.scrollBottom();
    },

    addTyping() {
        const msgs = document.getElementById('cs-messages');
        const div  = document.createElement('div');
        div.id = 'cs-typing';
        div.className = 'flex gap-2 items-end';
        div.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-wood shrink-0 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="bg-white rounded-2xl rounded-bl-sm shadow-sm px-4 py-3 flex gap-1 items-center">
                <span class="w-1.5 h-1.5 bg-muted rounded-full animate-bounce" style="animation-delay:0ms"></span>
                <span class="w-1.5 h-1.5 bg-muted rounded-full animate-bounce" style="animation-delay:150ms"></span>
                <span class="w-1.5 h-1.5 bg-muted rounded-full animate-bounce" style="animation-delay:300ms"></span>
            </div>`;
        msgs.appendChild(div);
        this.scrollBottom();
    },

    removeTyping() {
        document.getElementById('cs-typing')?.remove();
    },

    async send() {
        const input = document.getElementById('cs-input');
        const msg   = input?.value?.trim();
        if (!msg || this.isTyping) return;

        input.value = '';
        this.isTyping = true;

        // Sembunyikan suggestions setelah kirim pertama
        document.getElementById('cs-suggestions')?.classList.add('hidden');

        this.addBubble('user', msg);
        this.addTyping();

        try {
            const res  = await fetch('/chat', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ message: msg, history: this.history }),
            });
            const data = await res.json();

            this.removeTyping();

            const reply = data.success ? data.reply : (data.message ?? 'Maaf, terjadi kesalahan.');
            this.addBubble('assistant', reply);

            // Simpan ke history (maks 10 pasang)
            this.history.push({ role: 'user', content: msg });
            this.history.push({ role: 'assistant', content: reply });
            if (this.history.length > 20) this.history = this.history.slice(-20);

        } catch(e) {
            this.removeTyping();
            this.addBubble('assistant', 'Koneksi gagal. Silakan coba lagi.');
        } finally {
            this.isTyping = false;
            input?.focus();
        }
    },

    sendSuggestion(btn, text) {
        const input = document.getElementById('cs-input');
        if (input) input.value = text;
        this.send();
    },
};

// Tampilkan unread dot setelah 3 detik kalau belum dibuka
setTimeout(() => {
    if (!CSWidget.isOpen) {
        document.getElementById('cs-unread')?.classList.remove('hidden');
    }
}, 3000);
</script>

@stack('scripts')
</body>
</html>
