{{--
    =====================================================================
    DATA YANG DIBUTUHKAN VIEW INI (diisi dari controller / backend)
    =====================================================================
    $categories  (array/Collection)  tiap item: name, room, url (opsional)
    $products    (array/Collection)  tiap item: name, price (angka),
                                     image (path di storage, opsional)
    $stats       (array/Collection)  tiap item: value, label
    $about       (string, opsional)  cerita singkat perusahaan
    $whatsapp    (string, opsional)  nomor format 62..., tanpa + atau 0
    $address, $phone (string, opsional, dipakai di footer)

    Semua data dibaca dengan data_get(), jadi bisa berupa array
    ATAU objek Eloquent (data_get($item, 'name') cocok untuk keduanya).
    Jika variabel belum dikirim, halaman tetap tampil (kosong / placeholder).
--}}
@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    {{-- HERO (teks statis; ganti blok ilustrasi dengan <img> bila sudah ada foto) --}}
    <section class="container hero">
        <div class="hero-text">
            <p class="hero-label">Furniture kayu berkualitas</p>
            <h1>Ruang yang nyaman dimulai dari furniture yang tepat</h1>
            <p class="desc">Dibuat dari kayu pilihan oleh pengrajin berpengalaman, untuk rumah, kantor, dan hunian Anda.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#produk">Lihat Katalog</a>
                <a class="btn btn-outline" href="#kontak">Hubungi Kami</a>
            </div>
        </div>
        <div class="hero-visual" role="img" aria-label="Ilustrasi ruang tamu">
            <div class="floor"></div><div class="sofa"></div><div class="arm-l"></div><div class="arm-r"></div>
            <div class="back"></div><div class="table"></div><div class="leg"></div><div class="sun"></div><div class="window"></div>
        </div>
    </section>

    {{-- KATEGORI  <- BACKEND: $categories --}}
    <section id="koleksi" class="section-sand">
        <div class="container">
            <h2>Belanja berdasarkan kategori</h2>
            <p class="section-sub">Pilih furniture sesuai ruangan yang ingin Anda lengkapi.</p>
            <div class="grid-cat">
                @foreach (($categories ?? []) as $category)
                    <a href="{{ data_get($category, 'url', '#produk') }}" class="cat c{{ ($loop->index % 4) + 1 }}">
                        <strong>{{ data_get($category, 'name') }}</strong>
                        <span>{{ data_get($category, 'room') }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRODUK UNGGULAN  <- BACKEND: $products --}}
    <section id="produk" class="section">
        <div class="container">
            <h2>Produk unggulan</h2>
            <div class="grid-prod">
                @forelse (($products ?? []) as $product)
                    <div class="prod">
                        @if (data_get($product, 'image'))
                            <img src="{{ asset('storage/' . data_get($product, 'image')) }}"
                                 alt="{{ data_get($barang, 'name') }}"
                                 style="width:100%;height:260px;object-fit:cover;border-radius:20px">
                        @else
                            <div class="prod-img" style="background:#E3D6C3">
                                <div style="width:60%;height:110px;background:#8A5A3B"></div>
                            </div>
                        @endif
                        <h3>{{ data_get($product, 'name') }}</h3>
                        <p>Rp {{ number_format((float) data_get($product, 'price', 0), 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p>Produk belum tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- TENTANG  <- BACKEND: $about, $stats --}}
    <section id="tentang" class="about">
        <div class="container about-inner">
            <div class="about-text">
                <h2>Dibuat dengan teliti, dipakai bertahun-tahun</h2>
                <p>{{ $about ?? '[CERITA SINGKAT PERUSAHAAN]' }}</p>
            </div>
            <div class="stats">
                @foreach (($stats ?? []) as $stat)
                    <div><b>{{ data_get($stat, 'value') }}</b><span>{{ data_get($stat, 'label') }}</span></div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- KONTAK  <- BACKEND: $whatsapp --}}
    <section id="kontak" class="section">
        <div class="container">
            <div class="cta">
                <h2>Ingin furniture custom?</h2>
                <p>Ceritakan kebutuhan Anda, kami bantu dari desain sampai pengiriman.</p>
                <a class="btn btn-light" href="https://wa.me/{{ $whatsapp ?? '62xxxxxxxxxx' }}">Chat via WhatsApp</a>
            </div>
        </div>
    </section>

@endsection
