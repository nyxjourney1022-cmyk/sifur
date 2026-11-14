@extends('layouts.app')
@section('title', 'Pilih Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto px-6 lg:px-8 py-16">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="font-serif text-4xl font-medium text-ink mb-2">Pesanan Diterima!</h1>
        <p class="text-muted">Nomor Pesanan: <strong class="text-ink">#{{ $reservasi->id }}</strong></p>
    </div>

    {{-- Ringkasan pesanan --}}
    <div class="bg-white rounded-3xl p-6 mb-6">
        <h2 class="font-semibold text-ink mb-4">Detail Pesanan</h2>
        <div class="space-y-3 mb-5">
            @foreach($reservasi->detailReservasis as $detail)
            @php $sub = $detail->barang->harga * $detail->jumlah; @endphp
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-sand overflow-hidden shrink-0">
                    @if($detail->barang->gambar_url)
                        <img src="{{ $detail->barang->gambar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-end justify-center pb-1">
                            <div class="w-6 h-6 bg-wood/50 rounded-t-lg"></div>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-ink truncate">{{ $detail->barang->nama_barang }}</p>
                    <p class="text-xs text-muted">{{ $detail->jumlah }} pcs × Rp {{ number_format($detail->barang->harga, 0, ',', '.') }}</p>
                </div>
                <p class="text-sm font-semibold text-ink shrink-0">
                    Rp {{ number_format($sub, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>
        <div class="border-t border-sand pt-4 flex justify-between items-center">
            <span class="font-semibold text-ink">Total</span>
            <span class="font-serif text-2xl font-medium text-wood">
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Pilih metode pembayaran --}}
    <div class="bg-white rounded-3xl p-6 mb-6">
        <h2 class="font-semibold text-ink mb-5">Pilih Metode Pembayaran</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- QRIS --}}
            <button onclick="showQRIS()"
                    class="group border-2 border-sand hover:border-wood rounded-2xl p-5 text-left transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-wood/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-wood" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 11-1 0 .5.5 0 011 0zM6 7.5a.5.5 0 11-1 0 .5.5 0 011 0zm0 10a.5.5 0 11-1 0 .5.5 0 011 0zM19 7.5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-ink">Bayar via QRIS</p>
                        <p class="text-xs text-muted">GoPay, OVO, Dana, dll</p>
                    </div>
                </div>
                <p class="text-xs text-muted">Scan QR code dan transfer langsung. Konfirmasi otomatis.</p>
            </button>

            {{-- Nego via WA --}}
            <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode($waDraft) }}"
               target="_blank"
               class="group border-2 border-sand hover:border-green-500 rounded-2xl p-5 text-left transition-all block">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-ink">Nego via WhatsApp</p>
                        <p class="text-xs text-muted">Diskusi langsung dengan penjual</p>
                    </div>
                </div>
                <p class="text-xs text-muted">Pesan dengan detail pesanan sudah disiapkan otomatis, tinggal kirim.</p>
            </a>
        </div>
    </div>

    {{-- QRIS Section (hidden by default) --}}
    <div id="qris-section" class="hidden bg-white rounded-3xl p-6 mb-6 text-center">
        <h2 class="font-semibold text-ink mb-2">Scan QRIS</h2>
        <p class="text-sm text-muted mb-5">Scan QR code di bawah menggunakan aplikasi dompet digital Anda</p>

        {{-- Placeholder QRIS — ganti dengan gambar QRIS asli --}}
        <div class="w-48 h-48 bg-sand rounded-2xl flex items-center justify-center mx-auto mb-5 border-2 border-dashed border-wood/30">
            @if(file_exists(public_path('storage/qris.jpg')))
                <img src="{{ asset('storage/qris.jpg') }}" class="w-full h-full object-contain rounded-2xl p-2">
            @else
                <div class="text-center">
                    <svg class="w-12 h-12 text-muted mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 11-1 0 .5.5 0 011 0zM6 7.5a.5.5 0 11-1 0 .5.5 0 011 0zm0 10a.5.5 0 11-1 0 .5.5 0 011 0zM19 7.5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                    </svg>
                    <p class="text-xs text-muted">Taruh file QRIS di<br><code class="text-wood">storage/app/public/qris.png</code></p>
                </div>
            @endif
        </div>

        <div class="bg-sand rounded-2xl px-5 py-3 inline-block mb-5">
            <p class="text-sm text-muted">Jumlah Transfer</p>
            <p class="font-serif text-2xl font-medium text-wood">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>

        <p class="text-xs text-muted mb-4">Setelah transfer, konfirmasi ke WhatsApp kami</p>

        <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Halo, saya sudah transfer QRIS untuk pesanan #' . $reservasi->id . ' sebesar Rp ' . number_format($total, 0, ',', '.') . '. Mohon dikonfirmasi. Terima kasih!') }}"
           target="_blank"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-full transition-colors text-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Konfirmasi Pembayaran
        </a>
    </div>

    {{-- Preview WA Draft --}}
    <div class="bg-sand rounded-3xl p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-ink text-sm">Preview Pesan WhatsApp</h2>
            <button onclick="copyDraft()" class="text-xs font-medium text-wood hover:text-wood-dark transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Salin
            </button>
        </div>
        <pre id="wa-draft" class="text-xs text-muted whitespace-pre-wrap font-sans leading-relaxed bg-white rounded-xl p-4 border border-sand">{{ $waDraft }}</pre>
    </div>

    <div class="flex justify-center">
        <a href="{{ route('pesanan') }}"
           class="text-sm text-muted hover:text-ink transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Lihat semua pesanan
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
function showQRIS() {
    const sec = document.getElementById('qris-section');
    sec.classList.toggle('hidden');
    if (!sec.classList.contains('hidden')) {
        sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function copyDraft() {
    const text = document.getElementById('wa-draft').textContent;
    navigator.clipboard.writeText(text).then(() => showToast('Pesan disalin!'));
}
</script>
@endpush
