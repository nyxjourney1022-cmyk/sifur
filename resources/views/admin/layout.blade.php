<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-cream text-ink font-sans antialiased">
@php
    $menu = [
        ['admin.dashboard', 'Dashboard',  'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['admin.barang',    'Barang',     'M20 7l-8-4-8 4v10l8 4 8-4V7zM4 7l8 4 8-4M12 11v10'],
        ['admin.kayu',      'Jenis Kayu', 'M12 3v18M5 8c0-3 3-5 7-5s7 2 7 5-3 5-7 5-7-2-7-5z'],
        ['admin.reservasi', 'Reservasi',  'M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z'],
        ['admin.transaksi', 'Transaksi',  'M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z'],
        ['admin.pelanggan', 'Pelanggan',  'M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 10a4 4 0 100-8 4 4 0 000 8zM21 20v-2a4 4 0 00-3-3.9'],
    ];
    $admin = auth()->user();
@endphp

{{-- Overlay (ponsel) --}}
<div id="overlay" onclick="toggleSidebar(false)" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

{{-- SIDEBAR: tersembunyi di ponsel, tetap tampil di laptop --}}
<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-ink text-cream flex flex-col z-40 -translate-x-full lg:translate-x-0 transition-transform">
    <div class="h-16 flex items-center justify-between px-5 border-b border-white/10">
        <span class="font-serif text-xl font-semibold">Sifur Admin</span>
        <button onclick="toggleSidebar(false)" class="lg:hidden p-1" aria-label="Tutup menu">✕</button>
    </div>
    <nav class="flex-1 p-3 space-y-1 text-sm overflow-y-auto">
        @foreach($menu as [$route, $label, $icon])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs($route) ? 'bg-wood text-white' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
                {{ $label }}
            </a>
        @endforeach
    </nav>
    <a href="{{ route('home') }}" class="m-3 px-3 py-2.5 rounded-lg text-sm hover:bg-white/10">↗ Lihat Toko</a>
</aside>

<div class="lg:pl-64 min-h-screen flex flex-col">
    {{-- NAVBAR --}}
    <header class="h-16 sticky top-0 z-20 bg-cream/90 backdrop-blur border-b border-sand flex items-center justify-between px-4 lg:px-8">
        <div class="flex items-center gap-3 min-w-0">
            <button onclick="toggleSidebar(true)" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-sand" aria-label="Buka menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="font-serif text-lg sm:text-xl font-semibold truncate">@yield('title')</h1>
        </div>

        {{-- PROFIL ADMIN --}}
        <div class="relative">
            <button onclick="document.getElementById('profMenu').classList.toggle('hidden')" class="flex items-center gap-2 p-1 pr-3 rounded-full hover:bg-sand">
                <span class="w-8 h-8 rounded-full bg-wood text-white flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}</span>
                <span class="hidden sm:block text-sm font-medium max-w-[120px] truncate">{{ $admin->name ?? 'Admin' }}</span>
            </button>
            <div id="profMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-sand rounded-xl shadow-lg p-2 text-sm">
                <div class="px-3 py-2 border-b border-sand mb-1">
                    <p class="font-medium truncate">{{ $admin->name ?? 'Admin' }}</p>
                    <p class="text-xs text-muted truncate">{{ $admin->email ?? '' }}</p>
                    <p class="text-xs text-wood mt-0.5">Administrator</p>
                </div>
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-sand">Lihat Toko</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-sand text-red-700">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 p-4 lg:p-8 min-w-0">@yield('content')</main>
</div>

<div id="toast" class="fixed bottom-5 right-5 hidden px-4 py-2 rounded-lg text-sm text-white shadow-lg z-[60]"></div>

<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

function toggleSidebar(open) {
    document.getElementById('sidebar').classList.toggle('-translate-x-full', !open);
    document.getElementById('overlay').classList.toggle('hidden', !open);
}
function toast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'fixed bottom-5 right-5 px-4 py-2 rounded-lg text-sm text-white shadow-lg z-[60] ' + (ok ? 'bg-olive' : 'bg-red-700');
    setTimeout(() => t.classList.add('hidden'), 3000);
}
async function api(method, url, body = null) {
    const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, credentials: 'same-origin' };
    if (body instanceof FormData) {
        if (method !== 'POST') { body.append('_method', method); opts.method = 'POST'; }
        opts.body = body;
    } else if (body) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const first = data.errors ? Object.values(data.errors)[0][0] : null;
        throw new Error(first || data.message || 'Terjadi kesalahan');
    }
    return data;
}
const rupiah = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const openModal  = id => { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); };
const closeModal = id => { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); };
const BADGE = {
    'Menunggu':'bg-amber-100 text-amber-800', 'Diproses':'bg-blue-100 text-blue-800',
    'Selesai':'bg-green-100 text-green-800', 'Dibatalkan':'bg-red-100 text-red-800',
    'Lunas':'bg-green-100 text-green-800', 'Menunggu Pembayaran':'bg-amber-100 text-amber-800',
};
const badge = s => `<span class="px-2 py-0.5 rounded-full text-xs whitespace-nowrap ${BADGE[s] || 'bg-sand'}">${esc(s)}</span>`;
</script>
@stack('scripts')
</body>
</html>
