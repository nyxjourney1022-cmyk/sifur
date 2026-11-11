<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $store_name ?? config('app.name')) – Furniture Kayu Berkualitas</title>
    <meta name="description" content="@yield('description', 'Furniture kayu berkualitas untuk rumah, kantor, dan hunian Anda.')">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream:     '#F4EFE6',
                        sand:      '#EBE3D4',
                        wood:      '#8A5A3B',
                        'wood-dark': '#6A4429',
                        olive:     '#5B6B4A',
                        ink:       '#2A2420',
                        muted:     '#4A413A',
                    },
                    fontFamily: {
                        serif: ['Fraunces', 'Georgia', 'serif'],
                        sans:  ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Scrollbar halus untuk horizontal scroll */
        .scroll-x::-webkit-scrollbar { height: 4px; }
        .scroll-x::-webkit-scrollbar-track { background: transparent; }
        .scroll-x::-webkit-scrollbar-thumb { background: #8A5A3B55; border-radius: 99px; }
        .scroll-x::-webkit-scrollbar-thumb:hover { background: #8A5A3B; }

        /* Snap scroll */
        .snap-x-mandatory { scroll-snap-type: x mandatory; }
        .snap-start { scroll-snap-align: start; }

        /* Fade in animation */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease both; }
        .fade-up-2 { animation: fadeUp .6s .15s ease both; }
        .fade-up-3 { animation: fadeUp .6s .3s ease both; }

        /* Nav active */
        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: #8A5A3B;
            transition: width .25s ease;
        }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        body { font-family: 'Inter', system-ui, sans-serif; }
        h1,h2,h3 { font-family: 'Fraunces', Georgia, serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-cream text-ink antialiased">

    {{-- NAVBAR --}}
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-cream/80 backdrop-blur-md border-b border-sand">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="font-serif text-2xl font-semibold text-ink tracking-tight">
                {{ $store_name ?? config('app.name') }}
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-muted">
                <a href="{{ url('/') }}#koleksi"  class="nav-link hover:text-ink transition-colors">Koleksi</a>
                <a href="{{ url('/katalog') }}"   class="nav-link hover:text-ink transition-colors">Katalog</a>
                <a href="{{ url('/') }}#tentang"  class="nav-link hover:text-ink transition-colors">Tentang</a>
                <a href="{{ url('/') }}#kontak"   class="nav-link hover:text-ink transition-colors">Kontak</a>
            </nav>

            {{-- CTA --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-wood text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-wood-dark transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>
            </div>

            {{-- Mobile Hamburger --}}
            <button id="menu-btn" class="md:hidden p-2 rounded-lg hover:bg-sand transition-colors" aria-label="Menu">
                <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-sand bg-cream/95 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col gap-4 text-sm font-medium">
                <a href="{{ url('/') }}#koleksi" class="text-muted hover:text-ink transition-colors">Koleksi</a>
                <a href="{{ url('/katalog') }}"  class="text-muted hover:text-ink transition-colors">Katalog</a>
                <a href="{{ url('/') }}#tentang" class="text-muted hover:text-ink transition-colors">Tentang</a>
                <a href="{{ url('/') }}#kontak"  class="text-muted hover:text-ink transition-colors">Kontak</a>
                <a href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 bg-wood text-white font-semibold px-5 py-3 rounded-full hover:bg-wood-dark transition-colors">
                    Chat via WhatsApp
                </a>
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-ink text-white/70">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div>
                    <p class="font-serif text-2xl font-semibold text-white mb-3">{{ $store_name ?? config('app.name') }}</p>
                    <p class="text-sm leading-relaxed">Furniture kayu berkualitas untuk rumah, kantor, dan hunian Anda. Dibuat dengan teliti oleh pengrajin berpengalaman.</p>
                </div>
                <div>
                    <p class="text-white font-semibold mb-3">Navigasi</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}"         class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/katalog') }}"  class="hover:text-white transition-colors">Katalog</a></li>
                        <li><a href="{{ url('/') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ url('/') }}#kontak"  class="hover:text-white transition-colors">Kontak</a></li>
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
        // Mobile menu toggle
        const btn       = document.getElementById('menu-btn');
        const menu      = document.getElementById('mobile-menu');
        const iconOpen  = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');
        btn?.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });

        // Close mobile menu on link click
        menu?.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                menu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
