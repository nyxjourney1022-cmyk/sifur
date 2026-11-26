@extends('admin.layout')
@section('title', 'Kelola Barang')
@section('content')
<div class="flex flex-col lg:flex-row gap-3 mb-4">
    <input id="q" oninput="render()" placeholder="Cari nama barang..." class="px-3 py-2 rounded-lg border border-sand bg-white text-sm lg:w-64">
    <select id="fKategori" onchange="render()" class="px-3 py-2 rounded-lg border border-sand bg-white text-sm"><option value="">Semua kategori</option></select>
    <select id="fKayu" onchange="render()" class="px-3 py-2 rounded-lg border border-sand bg-white text-sm"><option value="">Semua jenis kayu</option></select>
    <select id="fStok" onchange="render()" class="px-3 py-2 rounded-lg border border-sand bg-white text-sm">
        <option value="">Semua stok</option><option value="menipis">Stok menipis (≤ 5)</option><option value="aman">Stok aman</option>
    </select>
    <button onclick="openForm()" class="lg:ml-auto px-4 py-2 rounded-lg bg-wood hover:bg-wood-dark text-white text-sm font-medium">+ Tambah Barang</button>
</div>

<div class="bg-white rounded-xl border border-sand overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-sand/60 text-left"><tr>
            <th class="p-3">Gambar</th><th class="p-3">Nama</th><th class="p-3 hidden md:table-cell">Kategori</th>
            <th class="p-3 hidden md:table-cell">Kayu</th><th class="p-3">Stok</th><th class="p-3">Harga</th><th class="p-3"></th>
        </tr></thead>
        <tbody id="rows"><tr><td colspan="7" class="p-6 text-center text-muted">Memuat...</td></tr></tbody>
    </table>
</div>

<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 id="mTitle" class="font-serif text-xl font-semibold mb-4"></h2>
        <div class="space-y-3 text-sm">
            <input id="f_nama" placeholder="Nama barang" class="w-full px-3 py-2 rounded-lg border border-sand">
            <input id="f_kategori" list="katList" placeholder="Kategori" class="w-full px-3 py-2 rounded-lg border border-sand">
            <datalist id="katList"></datalist>
            <input id="f_kayu" list="kayuList" placeholder="Jenis kayu (ketik baru bila belum ada)" class="w-full px-3 py-2 rounded-lg border border-sand">
            <datalist id="kayuList"></datalist>
            <div class="grid grid-cols-2 gap-3">
                <input id="f_stok" type="number" min="1" placeholder="Stok" class="px-3 py-2 rounded-lg border border-sand">
                <input id="f_harga" type="number" min="1" placeholder="Harga (Rp)" class="px-3 py-2 rounded-lg border border-sand">
            </div>
            <div>
                <img id="preview" class="hidden w-full h-40 object-cover rounded-lg mb-2" alt="Pratinjau">
                <input id="f_gambar" type="file" accept="image/png,image/jpeg,image/webp" onchange="previewImg()" class="w-full text-sm">
                <p class="text-xs text-muted mt-1">JPG/PNG/WEBP, maks 2MB. Saat edit, kosongkan bila gambar tidak diganti.</p>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button onclick="closeModal('modal')" class="px-4 py-2 rounded-lg border border-sand text-sm">Batal</button>
            <button onclick="save()" class="px-4 py-2 rounded-lg bg-wood text-white text-sm font-medium">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let data = [], kayuMap = {}, editId = null;

async function load() {
    try {
        const [b, k] = await Promise.all([api('GET', '/api/barang'), api('GET', '/api/kayu')]);
        data = b.data;
        kayuMap = Object.fromEntries(k.data.map(x => [x.id, x.jenis_kayu]));
        kayuList.innerHTML = k.data.map(x => `<option value="${esc(x.jenis_kayu)}">`).join('');
        fKayu.innerHTML = '<option value="">Semua jenis kayu</option>' + k.data.map(x => `<option value="${x.id}">${esc(x.jenis_kayu)}</option>`).join('');
        const kats = [...new Set(data.map(x => x.kategori))].sort();
        katList.innerHTML = kats.map(x => `<option value="${esc(x)}">`).join('');
        fKategori.innerHTML = '<option value="">Semua kategori</option>' + kats.map(x => `<option>${esc(x)}</option>`).join('');
        render();
    } catch (e) { toast(e.message, false); }
}

function render() {
    const q = f_q();
    const list = data.filter(b =>
        b.nama_barang.toLowerCase().includes(q) &&
        (!fKategori.value || b.kategori === fKategori.value) &&
        (!fKayu.value || String(b.kayu_id) === fKayu.value) &&
        (!fStok.value || (fStok.value === 'menipis' ? b.stok_barang <= 5 : b.stok_barang > 5)));
    rows.innerHTML = list.length ? list.map(b => `
        <tr class="border-t border-sand">
            <td class="p-3">${b.gambar_url ? `<img src="${esc(b.gambar_url)}" class="w-12 h-12 object-cover rounded-lg">` : '<div class="w-12 h-12 rounded-lg bg-sand"></div>'}</td>
            <td class="p-3 font-medium">${esc(b.nama_barang)}<div class="md:hidden text-xs text-muted">${esc(b.kategori)} · ${esc(kayuMap[b.kayu_id] || '-')}</div></td>
            <td class="p-3 hidden md:table-cell">${esc(b.kategori)}</td>
            <td class="p-3 hidden md:table-cell">${esc(kayuMap[b.kayu_id] || '-')}</td>
            <td class="p-3 ${b.stok_barang <= 5 ? 'text-red-700 font-semibold' : ''}">${b.stok_barang}</td>
            <td class="p-3 whitespace-nowrap">${rupiah(b.harga)}</td>
            <td class="p-3 text-right whitespace-nowrap">
                <button onclick="openForm(${b.id})" class="text-wood hover:underline mr-3">Edit</button>
                <button onclick="hapus(${b.id})" class="text-red-700 hover:underline">Hapus</button>
            </td>
        </tr>`).join('') : '<tr><td colspan="7" class="p-6 text-center text-muted">Tidak ada barang yang cocok.</td></tr>';
}
const f_q = () => document.getElementById('q').value.toLowerCase();

function previewImg() {
    const f = f_gambar.files[0];
    preview.classList.toggle('hidden', !f);
    if (f) preview.src = URL.createObjectURL(f);
}

function openForm(id = null) {
    editId = id;
    const b = data.find(x => x.id === id) || {};
    mTitle.textContent = id ? 'Edit Barang' : 'Tambah Barang';
    f_nama.value = b.nama_barang || ''; f_kategori.value = b.kategori || '';
    f_kayu.value = kayuMap[b.kayu_id] || ''; f_stok.value = b.stok_barang || '';
    f_harga.value = b.harga ? Math.round(b.harga) : ''; f_gambar.value = '';
    preview.classList.toggle('hidden', !b.gambar_url);
    if (b.gambar_url) preview.src = b.gambar_url;
    openModal('modal');
}

async function save() {
    const fd = new FormData();
    fd.append('nama_barang', f_nama.value); fd.append('kategori', f_kategori.value);
    fd.append('jenis_kayu', f_kayu.value); fd.append('stok_barang', f_stok.value);
    fd.append('harga', f_harga.value);
    if (f_gambar.files[0]) fd.append('gambar', f_gambar.files[0]);
    try {
        await api(editId ? 'PUT' : 'POST', editId ? `/api/barang/${editId}` : '/api/barang', fd);
        closeModal('modal'); toast('Barang tersimpan'); load();
    } catch (e) { toast(e.message, false); }
}

async function hapus(id) {
    if (!confirm('Hapus barang ini?')) return;
    try { await api('DELETE', `/api/barang/${id}`); toast('Barang dihapus'); load(); }
    catch (e) { toast(e.message, false); }
}
load();
</script>
@endpush
