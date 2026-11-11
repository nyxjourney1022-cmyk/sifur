@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto px-6 lg:px-8 py-12">

    <div class="mb-8">
        <h1 class="font-serif text-4xl font-medium text-ink">Profil Saya</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- ── Info Akun ───────────────────────── --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-3xl p-6 text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-4" referrerpolicy="no-referrer">
                @else
                    <div class="w-20 h-20 rounded-full bg-wood text-white text-3xl font-bold flex items-center justify-center mx-auto mb-4">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h2 class="font-semibold text-ink text-lg">{{ $user->name }}</h2>
                <p class="text-sm text-muted mt-1">{{ $user->email }}</p>
                @if($user->google_id)
                <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-600 font-medium px-3 py-1 rounded-full mt-2">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Akun Google
                </span>
                @endif

                <div class="border-t border-sand mt-5 pt-5 space-y-2">
                    <a href="{{ route('katalog') }}"
                       class="block text-center bg-wood text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-wood-dark transition-colors">
                        Lanjut Belanja
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-sm font-medium text-red-600 hover:text-red-700 py-2 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Alamat ──────────────────────────── --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-3xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-ink text-lg">Alamat Tersimpan</h2>
                    <button onclick="showAddressModal()"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-wood hover:text-wood-dark transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Alamat
                    </button>
                </div>

                <div id="address-list" class="space-y-3">
                    @forelse($addresses as $addr)
                    <div class="border-2 {{ $addr->is_default ? 'border-wood bg-wood/5' : 'border-sand' }} rounded-2xl p-4"
                         id="addr-card-{{ $addr->id }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="font-semibold text-ink text-sm">{{ $addr->nama_penerima }}</span>
                                    <span class="text-xs bg-sand text-muted px-2 py-0.5 rounded-full">{{ $addr->label }}</span>
                                    @if($addr->is_default)
                                    <span class="text-xs bg-wood/10 text-wood font-semibold px-2 py-0.5 rounded-full">Utama</span>
                                    @endif
                                </div>
                                <p class="text-sm text-muted">{{ $addr->no_tlp }}</p>
                                <p class="text-sm text-ink mt-1">{{ $addr->alamat }}, {{ $addr->kota }}, {{ $addr->provinsi }} {{ $addr->kode_pos }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                @if(!$addr->is_default)
                                <button onclick="setDefault({{ $addr->id }})"
                                        title="Jadikan utama"
                                        class="p-1.5 rounded-lg hover:bg-sand transition-colors text-muted hover:text-wood">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                                @endif
                                <button onclick="deleteAddress({{ $addr->id }})"
                                        title="Hapus"
                                        class="p-1.5 rounded-lg hover:bg-red-50 transition-colors text-muted hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div id="empty-address" class="text-center py-10 text-muted">
                        <p class="text-sm">Belum ada alamat tersimpan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Modal Tambah Alamat ════════════════════════════════════ --}}
<div id="address-modal" class="hidden fixed inset-0 bg-ink/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-serif text-xl font-medium text-ink">Tambah Alamat</h3>
            <button onclick="hideAddressModal()" class="p-2 hover:bg-sand rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="modal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink mb-1.5">Label</label>
                <input type="text" id="m_label" placeholder="Rumah, Kantor, dll"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Nama Penerima</label>
                <input type="text" id="m_nama" placeholder="Nama lengkap"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">No. Telepon</label>
                <input type="text" id="m_tlp" placeholder="08xxxxxxxxxx"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink mb-1.5">Alamat Lengkap</label>
                <textarea id="m_alamat" rows="2" placeholder="Jl. ..."
                          class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Kota</label>
                <input type="text" id="m_kota" placeholder="Jakarta"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Provinsi</label>
                <input type="text" id="m_provinsi" placeholder="DKI Jakarta"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Kode Pos</label>
                <input type="text" id="m_kodepos" maxlength="5" placeholder="12345"
                       class="w-full bg-sand border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none">
            </div>
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" id="m_default" class="text-wood focus:ring-wood rounded">
                <label for="m_default" class="text-sm text-muted">Jadikan alamat utama</label>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="hideAddressModal()"
                    class="flex-1 border-2 border-sand text-muted font-medium py-3 rounded-full hover:border-ink/20 transition-colors">
                Batal
            </button>
            <button id="btn-save-addr" onclick="saveAddress()"
                    class="flex-1 bg-wood text-white font-semibold py-3 rounded-full hover:bg-wood-dark transition-colors">
                Simpan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showAddressModal() { document.getElementById('address-modal').classList.remove('hidden'); }
function hideAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    document.getElementById('modal-error').classList.add('hidden');
    ['m_label','m_nama','m_tlp','m_alamat','m_kota','m_provinsi','m_kodepos'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('m_default').checked = false;
}

async function saveAddress() {
    const btn    = document.getElementById('btn-save-addr');
    const errDiv = document.getElementById('modal-error');
    errDiv.classList.add('hidden');

    const payload = {
        label:         document.getElementById('m_label').value.trim(),
        nama_penerima: document.getElementById('m_nama').value.trim(),
        no_tlp:        document.getElementById('m_tlp').value.trim(),
        alamat:        document.getElementById('m_alamat').value.trim(),
        kota:          document.getElementById('m_kota').value.trim(),
        provinsi:      document.getElementById('m_provinsi').value.trim(),
        kode_pos:      document.getElementById('m_kodepos').value.trim(),
        is_default:    document.getElementById('m_default').checked ? 1 : 0,
    };

    for (const [k, v] of Object.entries(payload)) {
        if (k !== 'is_default' && !v) {
            errDiv.textContent = 'Harap lengkapi semua field.';
            errDiv.classList.remove('hidden');
            return;
        }
    }

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const res  = await fetch('/addresses', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            hideAddressModal();
            // Inject ke DOM tanpa reload
            const a = data.data;
            const emptyEl = document.getElementById('empty-address');
            emptyEl?.remove();

            if (a.is_default) {
                document.querySelectorAll('[id^="addr-card-"]').forEach(el => {
                    el.classList.remove('border-wood','bg-wood/5');
                    el.classList.add('border-sand');
                    el.querySelectorAll('.text-xs.bg-wood\\/10').forEach(b => b.remove());
                });
            }

            const html = `
                <div class="border-2 ${a.is_default ? 'border-wood bg-wood/5' : 'border-sand'} rounded-2xl p-4"
                     id="addr-card-${a.id}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-ink text-sm">${a.nama_penerima}</span>
                                <span class="text-xs bg-sand text-muted px-2 py-0.5 rounded-full">${a.label}</span>
                                ${a.is_default ? '<span class="text-xs bg-wood/10 text-wood font-semibold px-2 py-0.5 rounded-full">Utama</span>' : ''}
                            </div>
                            <p class="text-sm text-muted">${a.no_tlp}</p>
                            <p class="text-sm text-ink mt-1">${a.alamat}, ${a.kota}, ${a.provinsi} ${a.kode_pos}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            ${!a.is_default ? `<button onclick="setDefault(${a.id})" class="p-1.5 rounded-lg hover:bg-sand transition-colors text-muted hover:text-wood"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></button>` : ''}
                            <button onclick="deleteAddress(${a.id})" class="p-1.5 rounded-lg hover:bg-red-50 transition-colors text-muted hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                </div>`;
            document.getElementById('address-list').insertAdjacentHTML('beforeend', html);
            showToast('Alamat berhasil disimpan.');
        } else {
            errDiv.textContent = data.message ?? 'Gagal menyimpan.';
            errDiv.classList.remove('hidden');
        }
    } catch(e) {
        errDiv.textContent = 'Gagal terhubung ke server.';
        errDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan';
    }
}

async function deleteAddress(id) {
    if (!confirm('Hapus alamat ini?')) return;
    try {
        const res  = await fetch(`/addresses/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`addr-card-${id}`)?.remove();
            if (!document.querySelector('[id^="addr-card-"]')) {
                document.getElementById('address-list').innerHTML =
                    `<div id="empty-address" class="text-center py-10 text-muted"><p class="text-sm">Belum ada alamat tersimpan.</p></div>`;
            }
            showToast('Alamat dihapus.');
        }
    } catch(e) {}
}

async function setDefault(id) {
    try {
        const res  = await fetch(`/addresses/${id}/default`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const data = await res.json();
        if (data.success) {
            document.querySelectorAll('[id^="addr-card-"]').forEach(el => {
                el.classList.remove('border-wood','bg-wood/5');
                el.classList.add('border-sand');
            });
            const card = document.getElementById(`addr-card-${id}`);
            card?.classList.add('border-wood','bg-wood/5');
            card?.classList.remove('border-sand');
            showToast('Alamat utama diperbarui.');
        }
    } catch(e) {}
}
</script>
@endpush
