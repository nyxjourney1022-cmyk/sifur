@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">

    <div class="mb-8">
        <h1 class="font-serif text-4xl font-medium text-ink">Checkout</h1>
        <p class="text-muted mt-1">Periksa pesanan dan pilih alamat pengiriman</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── KIRI: Alamat ─────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Alamat tersimpan --}}
            @if($addresses->isNotEmpty())
            <div class="bg-white rounded-3xl p-6">
                <h2 class="font-semibold text-ink mb-4">Pilih Alamat Tersimpan</h2>
                <div class="space-y-3" id="saved-addresses">
                    @foreach($addresses as $addr)
                    <label class="flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-colors
                                  {{ $addr->is_default ? 'border-wood bg-wood/5' : 'border-sand hover:border-wood/40' }}"
                           id="addr-label-{{ $addr->id }}">
                        <input type="radio" name="address_choice" value="{{ $addr->id }}"
                               class="mt-0.5 text-wood focus:ring-wood"
                               {{ $addr->is_default ? 'checked' : '' }}
                               onchange="selectAddress({{ $addr->id }})">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-ink text-sm">{{ $addr->nama_penerima }}</span>
                                <span class="text-xs bg-sand text-muted px-2 py-0.5 rounded-full">{{ $addr->label }}</span>
                                @if($addr->is_default)
                                <span class="text-xs bg-wood/10 text-wood font-semibold px-2 py-0.5 rounded-full">Utama</span>
                                @endif
                            </div>
                            <p class="text-sm text-muted mt-0.5">{{ $addr->no_tlp }}</p>
                            <p class="text-sm text-ink mt-1">{{ $addr->alamat }}, {{ $addr->kota }}, {{ $addr->provinsi }} {{ $addr->kode_pos }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                <button type="button" onclick="toggleNewAddress()"
                        class="mt-4 flex items-center gap-2 text-sm font-medium text-wood hover:text-wood-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Gunakan alamat baru
                </button>
            </div>
            @endif

            {{-- Form alamat baru --}}
            <div id="new-address-form" class="{{ $addresses->isNotEmpty() ? 'hidden' : '' }} bg-white rounded-3xl p-6">
                <h2 class="font-semibold text-ink mb-4">
                    {{ $addresses->isEmpty() ? 'Alamat Pengiriman' : 'Alamat Baru' }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Nama Penerima</label>
                        <input type="text" id="f_nama" placeholder="Nama lengkap"
                               class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">No. Telepon</label>
                        <input type="text" id="f_tlp" placeholder="08xxxxxxxxxx"
                               class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Alamat Lengkap</label>
                        <textarea id="f_alamat" rows="2" placeholder="Jl. ..."
                                  class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Kota</label>
                        <input type="text" id="f_kota" placeholder="Jakarta"
                               class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Provinsi</label>
                        <input type="text" id="f_provinsi" placeholder="DKI Jakarta"
                               class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Kode Pos</label>
                        <input type="text" id="f_kodepos" placeholder="12345" maxlength="5"
                               class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" id="f_save" class="text-wood focus:ring-wood rounded">
                        <label for="f_save" class="text-sm text-muted">Simpan alamat ini</label>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="bg-white rounded-3xl p-6">
                <h2 class="font-semibold text-ink mb-3">Catatan (opsional)</h2>
                <textarea id="f_catatan" rows="2" placeholder="Catatan untuk penjual..."
                          class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none resize-none"></textarea>
            </div>
        </div>

        {{-- ── KANAN: Ringkasan ─────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 sticky top-24">
                <h2 class="font-semibold text-ink mb-5">Ringkasan Pesanan</h2>

                <div class="space-y-4 mb-5">
                    @foreach($items as $item)
                    <div class="flex gap-3">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-sand shrink-0">
                            @if($item->barang->gambar_url)
                                <img src="{{ $item->barang->gambar_url }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-end justify-center pb-1">
                                    <div class="w-7 h-7 bg-wood/50 rounded-t-lg"></div>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-ink leading-snug truncate">{{ $item->barang->nama_barang }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $item->jumlah }} × Rp {{ number_format($item->barang->harga, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-ink shrink-0">
                            Rp {{ number_format($item->barang->harga * $item->jumlah, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-sand pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-muted">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-muted">
                        <span>Pengiriman</span>
                        <span class="text-olive font-medium">Gratis</span>
                    </div>
                </div>

                <div class="border-t border-sand mt-3 pt-4 flex justify-between items-center mb-6">
                    <span class="font-semibold text-ink">Total</span>
                    <span class="font-serif text-2xl font-medium text-wood">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- Error msg --}}
                <div id="checkout-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4"></div>

                <button id="btn-checkout" onclick="submitCheckout()"
                        class="w-full bg-wood hover:bg-wood-dark text-white font-semibold py-4 rounded-full transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Konfirmasi Pesanan
                </button>

                <p class="text-xs text-muted text-center mt-3">
                    Dengan memesan, Anda menyetujui syarat & ketentuan kami
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedAddressId = {{ $addresses->where('is_default', true)->first()?->id ?? 'null' }};
let useNewAddress     = {{ $addresses->isEmpty() ? 'true' : 'false' }};

function selectAddress(id) {
    selectedAddressId = id;
    useNewAddress = false;
    document.getElementById('new-address-form')?.classList.add('hidden');
    document.querySelectorAll('[id^="addr-label-"]').forEach(el => {
        el.classList.remove('border-wood', 'bg-wood/5');
        el.classList.add('border-sand');
    });
    const lbl = document.getElementById(`addr-label-${id}`);
    lbl?.classList.add('border-wood', 'bg-wood/5');
    lbl?.classList.remove('border-sand');
}

function toggleNewAddress() {
    const form = document.getElementById('new-address-form');
    form?.classList.toggle('hidden');
    useNewAddress = !form?.classList.contains('hidden');
    if (useNewAddress) {
        selectedAddressId = null;
        document.querySelectorAll('[name="address_choice"]').forEach(r => r.checked = false);
    }
}

async function submitCheckout() {
    const btn    = document.getElementById('btn-checkout');
    const errDiv = document.getElementById('checkout-error');
    errDiv.classList.add('hidden');

    let payload = { catatan: document.getElementById('f_catatan')?.value ?? '' };

    if (!useNewAddress && selectedAddressId) {
        payload.address_id = selectedAddressId;
    } else {
        payload.nama_penerima = document.getElementById('f_nama')?.value?.trim();
        payload.no_tlp        = document.getElementById('f_tlp')?.value?.trim();
        payload.alamat        = document.getElementById('f_alamat')?.value?.trim();
        payload.kota          = document.getElementById('f_kota')?.value?.trim();
        payload.provinsi      = document.getElementById('f_provinsi')?.value?.trim();
        payload.kode_pos      = document.getElementById('f_kodepos')?.value?.trim();
        payload.save_address  = document.getElementById('f_save')?.checked ? 1 : 0;

        const required = ['nama_penerima','no_tlp','alamat','kota','provinsi','kode_pos'];
        for (const f of required) {
            if (!payload[f]) {
                errDiv.textContent = 'Harap lengkapi semua field alamat.';
                errDiv.classList.remove('hidden');
                return;
            }
        }
    }

    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memproses...`;

    try {
        const res  = await fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            updateCartBadge(0);
            window.location.href = data.redirect;
        } else {
            errDiv.textContent = data.message ?? 'Terjadi kesalahan.';
            errDiv.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Konfirmasi Pesanan`;
        }
    } catch(e) {
        errDiv.textContent = 'Gagal terhubung ke server.';
        errDiv.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = `Konfirmasi Pesanan`;
    }
}
</script>
@endpush
