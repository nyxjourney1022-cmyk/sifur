@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-screen bg-cream flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <div class="bg-white rounded-[2rem] shadow-xl p-8 md:p-10">

            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="font-serif text-2xl font-semibold text-ink">
                    {{ $store_name ?? config('app.name') }}
                </a>
                <h1 class="font-serif text-3xl font-medium text-ink mt-4">Buat akun</h1>
                <p class="text-muted text-sm mt-1">Daftar untuk mulai berbelanja</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Google --}}
            <a href="{{ route('google.redirect') }}"
               class="flex items-center justify-center gap-3 w-full border-2 border-sand hover:border-ink/20 text-ink font-medium py-3 rounded-full transition-colors mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>

            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 h-px bg-sand"></div>
                <span class="text-xs text-muted font-medium">atau</span>
                <div class="flex-1 h-px bg-sand"></div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none"
                           placeholder="Nama kamu">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none"
                           placeholder="Min. 8 karakter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none"
                           placeholder="Ulangi password">
                </div>

                <button type="submit"
                        class="w-full bg-wood hover:bg-wood-dark text-white font-semibold py-3 rounded-full transition-colors mt-2">
                    Buat Akun
                </button>
            </form>

            <p class="text-center text-sm text-muted mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-wood font-semibold hover:text-wood-dark transition-colors">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
