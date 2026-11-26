@extends('admin.layout')
@section('title', 'Reservasi')
@section('content')
<div class="flex flex-col sm:flex-row gap-3 mb-4">
    <select id="fStatus" onchange="render()" class="px-3 py-2 rounded-lg border border-sand bg-white text-sm">
        <option value="">Semua status</option>
        @foreach(['Menunggu','Diproses','Selesai','Dibatalkan'] as $s)<option>{{ $s }}</option>@endforeach
    </select>
    <button onclick="openForm()" class="sm:ml-auto px-4 py-2 rounded-lg bg-wood hover:bg-wood-dark text-white text-sm font-medium">+ Tambah Reservasi</button>
</div>
<div class="bg-white rounded-xl border border-sand overflow-x-auto">
    <table class="w-full text-sm min-w-[560px]">
        <thead class="bg-sand/60 text-left"><tr>
            <th class="p-3">Pelanggan</th><th class="p-3 hidden sm:table-cell">Tanggal</th><th class="p-3 hidden md:table-cell">Barang</th><th class="p-3">Status</th><th class="p-3"></th>
        </tr></thead>
        <tbody id="rows"><tr><td colspan="5" class="p-6 text-center text-muted">Memuat...</td></tr></tbody>
    </table>
</div>
<div class="flex justify-center items-center gap-2 mt-4 text-sm">
    <button id="prev" onclick="load(page-1)" class="px-3 py-1 rounded border border-sand bg-white disabled:opacity-40">‹</button>
    <span id="pageInfo"></span>
    <button id="next" onclick="load(page+1)" class="px-3 py-1 rounded border border-sand bg-white disabled:opacity-40">›</button>
</div>

{{-- Detail pesanan --}}
<div id="detailModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto text-sm" id="detailBody"></div>
</div>

{{-- Form tambah/edit --}}
<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 sm:p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto text-sm">
        <h2 id="mTitle" class="font-serif text-xl font-semibold mb-4"></h2>
        <div id="pelangganBox" class="space-y-3 mb-3">
            <input id="f_nama" placeholder="Nama pelanggan" class="w-full px-3 py-2 rounded-lg border border-sand">
            <input id="f_tlp" placeholder="No. telepon" class="w-full px-3 py-2 rounded-lg border border-sand">
            <textarea id="f_alamat" placeholder="Alamat" rows="2" class="w-full px-3 py-2 rounded-lg border border-sand"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <input id="f_tgl" type="date" class="px-3 py-2 rounded-lg border border-sand">
            <select id="f_status" class="px-3 py-2 rounded-lg border border-sand">
                @foreach(['Menunggu','Diproses','Selesai','Dibatalkan'] as $s)<option>{{ $s }}</option>@endforeach
            </select>
        </div>
        <div id="details" class="space-y-2"></div>
        <button onclick="addDetail()" class="mt-2 text-wood hover:underline">+ Tambah barang</button>
        <div class="flex justify-end gap-2 mt-5">
            <button onclick="closeModal('modal')" class="px-4 py-2 rounded-lg border border-sand">Batal</button>
            <button onclick="save()" class="px-4 py-2 rounded-lg bg-wood text-white font-medium">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let barang = [], items = [], page = 1, editId = null;

async function load(p = 1) {
    try {
        if (!barang.length) barang = (await api('GET', '/api/barang')).data;
        const r = (await api('GET', `/api/reservasi?page=${p}`)).data;
        page = r.current_page; items = r.data;
        pageInfo.textContent = `Halaman ${r.current_page} / ${r.last_page}`;
        prev.disabled = r.current_page <= 1; next.disabled = r.current_page >= r.last_page;
        render();
    } catch (e) { toast(e.message, false); }
}

function render() {
    const list = items.filter(x => !fStatus.value || x.status === fStatus.value);
    rows.innerHTML = list.length ? list.map(x => `
        <tr class="border-t border-sand">
            <td class="p-3"><div class="font-medium">${esc(x.pelanggan?.nama)}</div>
                <div class="text-xs text-muted">${esc(x.pelanggan?.no_tlp)}</div>
                <div class="sm:hidden text-xs text-muted">${esc(x.tanggal_reservasi).slice(0,10)}</div></td>
            <td class="p-3 hidden sm:table-cell whitespace-nowrap">${esc(x.tanggal_reservasi).slice(0,10)}</td>
            <td class="p-3 hidden md:table-cell">${x.detail_reservasis.map(d => `${esc(d.barang?.nama_barang)} ×${d.jumlah}`).join('<br>')}</td>
            <td class="p-3">${badge(x.status)}</td>
            <td class="p-3 text-right whitespace-nowrap">
                <button onclick="showDetail(${x.id})" class="text-ink hover:underline mr-2">Detail</button>
                <button onclick="openForm(${x.id})" class="text-wood hover:underline mr-2">Edit</button>
                <button onclick="hapus(${x.id})" class="text-red-700 hover:underline">Hapus</button>
            </td>
        </tr>`).join('') : '<tr><td colspan="5" class="p-6 text-center text-muted">Tidak ada reservasi.</td></tr>';
}

function showDetail(id) {
    const x = items.find(i => i.id === id);
    const total = x.detail_reservasis.reduce((s, d) => s + d.jumlah * Number(d.barang?.harga || 0), 0);
    detailBody.innerHTML = `
        <div class="flex justify-between items-start mb-3"><h2 class="font-serif text-xl font-semibold">Reservasi #${x.id}</h2>${badge(x.status)}</div>
        <p class="font-medium">${esc(x.pelanggan?.nama)}</p>
        <p class="text-muted">${esc(x.pelanggan?.no_tlp)}</p>
        <p class="text-muted mb-3">${esc(x.pelanggan?.alamat)}</p>
        <p class="text-xs text-muted mb-2">Tanggal reservasi: ${esc(x.tanggal_reservasi).slice(0,10)}</p>
        <div class="border-t border-sand">${x.detail_reservasis.map(d => `
            <div class="flex justify-between py-2 border-b border-sand gap-3">
                <span>${esc(d.barang?.nama_barang)} <span class="text-muted">×${d.jumlah}</span></span>
                <span class="whitespace-nowrap">${rupiah(d.jumlah * Number(d.barang?.harga || 0))}</span>
            </div>`).join('')}</div>
        <div class="flex justify-between font-semibold mt-3"><span>Perkiraan total</span><span>${rupiah(total)}</span></div>
        <button onclick="closeModal('detailModal')" class="mt-5 w-full px-4 py-2 rounded-lg border border-sand">Tutup</button>`;
    openModal('detailModal');
}

function detailRow(d = {}) {
    return `<div class="flex gap-2 det">
        <select class="d_barang flex-1 min-w-0 px-3 py-2 rounded-lg border border-sand">
            ${barang.map(b => `<option value="${b.id}" ${b.id == d.barang_id ? 'selected' : ''}>${esc(b.nama_barang)} (stok ${b.stok_barang})</option>`).join('')}
        </select>
        <input type="number" min="1" value="${d.jumlah || 1}" class="d_jumlah w-20 px-3 py-2 rounded-lg border border-sand">
        <button onclick="this.parentElement.remove()" class="text-red-700 px-2">✕</button>
    </div>`;
}
const addDetail = d => details.insertAdjacentHTML('beforeend', detailRow(d));

function openForm(id = null) {
    editId = id;
    const x = items.find(i => i.id === id);
    mTitle.textContent = id ? 'Edit Reservasi' : 'Tambah Reservasi';
    pelangganBox.classList.toggle('hidden', !!id);
    f_nama.value = f_tlp.value = f_alamat.value = '';
    f_tgl.value = x ? String(x.tanggal_reservasi).slice(0, 10) : '';
    f_status.value = x ? x.status : 'Menunggu';
    details.innerHTML = '';
    (x ? x.detail_reservasis : [{}]).forEach(addDetail);
    openModal('modal');
}

async function save() {
    const det = [...document.querySelectorAll('.det')].map(r => ({
        barang_id: +r.querySelector('.d_barang').value, jumlah: +r.querySelector('.d_jumlah').value }));
    const body = { tanggal_reservasi: f_tgl.value, status: f_status.value, details: det };
    if (!editId) Object.assign(body, { nama: f_nama.value, no_tlp: f_tlp.value, alamat: f_alamat.value });
    try {
        await api(editId ? 'PUT' : 'POST', editId ? `/api/reservasi/${editId}` : '/api/reservasi', body);
        closeModal('modal'); toast('Reservasi tersimpan'); barang = []; load(page);
    } catch (e) { toast(e.message, false); }
}

async function hapus(id) {
    if (!confirm('Hapus reservasi ini? Stok akan dikembalikan.')) return;
    try { await api('DELETE', `/api/reservasi/${id}`); toast('Reservasi dihapus'); barang = []; load(page); }
    catch (e) { toast(e.message, false); }
}
load();
</script>
@endpush
