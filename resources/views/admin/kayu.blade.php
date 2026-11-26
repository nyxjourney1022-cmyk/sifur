@extends('admin.layout')
@section('title', 'Jenis Kayu')
@section('content')
<div class="flex flex-col sm:flex-row gap-3 mb-4 justify-between">
    <input id="q" oninput="render()" placeholder="Cari jenis kayu..." class="px-3 py-2 rounded-lg border border-sand bg-white text-sm sm:w-64">
    <button onclick="openForm()" class="px-4 py-2 rounded-lg bg-wood hover:bg-wood-dark text-white text-sm font-medium">+ Tambah Jenis Kayu</button>
</div>
<div class="bg-white rounded-xl border border-sand overflow-x-auto max-w-2xl">
    <table class="w-full text-sm">
        <thead class="bg-sand/60 text-left"><tr><th class="p-3">Jenis Kayu</th><th class="p-3">Jumlah Barang</th><th class="p-3"></th></tr></thead>
        <tbody id="rows"><tr><td colspan="3" class="p-6 text-center text-muted">Memuat...</td></tr></tbody>
    </table>
</div>

<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 sm:p-6 w-full max-w-sm">
        <h2 id="mTitle" class="font-serif text-xl font-semibold mb-4"></h2>
        <input id="f_nama" placeholder="Contoh: Jati" class="w-full px-3 py-2 rounded-lg border border-sand text-sm">
        <div class="flex justify-end gap-2 mt-5">
            <button onclick="closeModal('modal')" class="px-4 py-2 rounded-lg border border-sand text-sm">Batal</button>
            <button onclick="save()" class="px-4 py-2 rounded-lg bg-wood text-white text-sm font-medium">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let kayu = [], jumlah = {}, editId = null;

async function load() {
    try {
        const [k, b] = await Promise.all([api('GET', '/api/kayu'), api('GET', '/api/barang')]);
        kayu = k.data;
        jumlah = {};
        b.data.forEach(x => jumlah[x.kayu_id] = (jumlah[x.kayu_id] || 0) + 1);
        render();
    } catch (e) { toast(e.message, false); }
}
function render() {
    const q = document.getElementById('q').value.toLowerCase();
    const list = kayu.filter(k => k.jenis_kayu.toLowerCase().includes(q));
    rows.innerHTML = list.length ? list.map(k => `
        <tr class="border-t border-sand">
            <td class="p-3 font-medium">${esc(k.jenis_kayu)}</td>
            <td class="p-3"><span class="px-2 py-0.5 rounded-full text-xs bg-sand">${jumlah[k.id] || 0} barang</span></td>
            <td class="p-3 text-right whitespace-nowrap">
                <button onclick="openForm(${k.id})" class="text-wood hover:underline mr-3">Edit</button>
                <button onclick="hapus(${k.id})" class="text-red-700 hover:underline">Hapus</button>
            </td>
        </tr>`).join('') : '<tr><td colspan="3" class="p-6 text-center text-muted">Belum ada data.</td></tr>';
}
function openForm(id = null) {
    editId = id;
    mTitle.textContent = id ? 'Edit Jenis Kayu' : 'Tambah Jenis Kayu';
    f_nama.value = (kayu.find(k => k.id === id) || {}).jenis_kayu || '';
    openModal('modal');
}
async function save() {
    try {
        await api(editId ? 'PUT' : 'POST', editId ? `/api/kayu/${editId}` : '/api/kayu', { jenis_kayu: f_nama.value });
        closeModal('modal'); toast('Tersimpan'); load();
    } catch (e) { toast(e.message, false); }
}
async function hapus(id) {
    if ((jumlah[id] || 0) > 0) return toast('Tidak bisa dihapus: masih dipakai ' + jumlah[id] + ' barang', false);
    if (!confirm('Hapus jenis kayu ini?')) return;
    try { await api('DELETE', `/api/kayu/${id}`); toast('Dihapus'); load(); }
    catch (e) { toast(e.message, false); }
}
load();
</script>
@endpush
