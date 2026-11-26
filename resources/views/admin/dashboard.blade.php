@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
@php
    $badge = ['Menunggu'=>'bg-amber-100 text-amber-800','Diproses'=>'bg-blue-100 text-blue-800','Selesai'=>'bg-green-100 text-green-800','Dibatalkan'=>'bg-red-100 text-red-800'];
    $maks = max(array_column($penjualan, 'nilai'));
@endphp

<p class="text-xs text-muted mb-4">Data pada halaman ini masih dummy.</p>

{{-- Kartu statistik --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $s)
        <div class="bg-white rounded-xl border border-sand p-5">
            <p class="text-sm text-muted">{{ $s['label'] }}</p>
            <p class="font-serif text-2xl sm:text-3xl font-semibold mt-1 break-words">{{ $s['value'] }}</p>
            <p class="text-xs text-olive mt-1">{{ $s['note'] }}</p>
        </div>
    @endforeach
</div>

{{-- Grafik --}}
<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <section class="lg:col-span-2 bg-white rounded-xl border border-sand p-5">
        <h2 class="font-semibold mb-4">Penjualan 6 Bulan Terakhir <span class="text-xs text-muted font-normal">(juta rupiah)</span></h2>
        <div class="flex items-end gap-2 sm:gap-4 h-48">
            @foreach($penjualan as $p)
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-1">
                    <span class="text-xs text-muted">{{ $p['nilai'] }}</span>
                    <div class="w-full bg-wood rounded-t-md hover:bg-wood-dark transition-colors" style="height: {{ round($p['nilai'] / $maks * 100) }}%"></div>
                    <span class="text-xs mt-1">{{ $p['bulan'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white rounded-xl border border-sand p-5">
        <h2 class="font-semibold mb-4">Penjualan per Kategori</h2>
        <div class="space-y-4">
            @foreach($kategori as $k)
                <div>
                    <div class="flex justify-between text-sm mb-1"><span>{{ $k['nama'] }}</span><span class="text-muted">{{ $k['persen'] }}%</span></div>
                    <div class="h-2 bg-sand rounded-full"><div class="h-2 bg-olive rounded-full" style="width: {{ $k['persen'] }}%"></div></div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<div class="grid lg:grid-cols-2 gap-4">
    <section class="bg-white rounded-xl border border-sand p-5">
        <h2 class="font-semibold mb-3">Stok Menipis</h2>
        @foreach($stokMenipis as $b)
            <div class="flex justify-between items-center py-2.5 border-b border-sand last:border-0 text-sm gap-3">
                <span class="truncate">{{ $b['nama'] }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-800 shrink-0">Sisa {{ $b['stok'] }}</span>
            </div>
        @endforeach
        <a href="{{ route('admin.barang') }}" class="block mt-3 text-sm text-wood hover:underline">Kelola barang →</a>
    </section>

    <section class="bg-white rounded-xl border border-sand p-5">
        <h2 class="font-semibold mb-3">Reservasi Terbaru</h2>
        @foreach($reservasiTerbaru as $r)
            <div class="flex justify-between items-center py-2.5 border-b border-sand last:border-0 text-sm gap-3">
                <div class="min-w-0"><p class="font-medium truncate">{{ $r['nama'] }}</p><p class="text-xs text-muted">{{ $r['tanggal'] }}</p></div>
                <span class="px-2 py-0.5 rounded-full text-xs shrink-0 {{ $badge[$r['status']] }}">{{ $r['status'] }}</span>
            </div>
        @endforeach
        <a href="{{ route('admin.reservasi') }}" class="block mt-3 text-sm text-wood hover:underline">Lihat semua →</a>
    </section>
</div>
@endsection
