@extends('admin.layout')
@section('title', 'Pelanggan')
@section('content')
<p class="text-xs text-muted mb-4">Data pada halaman ini masih dummy.</p>
<input id="q" oninput="render()" placeholder="Cari nama / telepon..." class="mb-4 px-3 py-2 rounded-lg border border-sand bg-white text-sm w-full sm:w-64">
<div class="bg-white rounded-xl border border-sand overflow-x-auto">
    <table class="w-full text-sm min-w-[480px]">
        <thead class="bg-sand/60 text-left"><tr>
            <th class="p-3">Nama</th><th class="p-3 hidden sm:table-cell">Telepon</th><th class="p-3 hidden md:table-cell">Alamat</th>
            <th class="p-3">Transaksi</th><th class="p-3"></th>
        </tr></thead>
        <tbody id="rows"></tbody>
    </table>
</div>
<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto text-sm" id="body"></div>
</div>
@endsection

@push('scripts')
<script>
const data = @json($pelanggan);

function render() {
    const q = document.getElementById('q').value.toLowerCase();
    const list = data.filter(p => (p.nama + p.no_tlp).toLowerCase().includes(q));
    rows.innerHTML = list.length ? list.map(p => `
        <tr class="border-t border-sand">
            <td class="p-3"><div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-sand flex items-center justify-center font-semibold shrink-0">${esc(p.nama[0])}</span>
                <div><p class="font-medium">${esc(p.nama)}</p><p class="sm:hidden text-xs text-muted">${esc(p.no_tlp)}</p></div></div></td>
            <td class="p-3 hidden sm:table-cell whitespace-nowrap">${esc(p.no_tlp)}</td>
            <td class="p-3 hidden md:table-cell">${esc(p.alamat)}</td>
            <td class="p-3">${p.riwayat.length}</td>
            <td class="p-3 text-right"><button onclick="detail(${p.id})" class="text-wood hover:underline">Detail</button></td>
        </tr>`).join('') : '<tr><td colspan="5" class="p-6 text-center text-muted">Tidak ada pelanggan.</td></tr>';
}

function detail(id) {
    const p = data.find(x => x.id === id);
    const belanja = p.riwayat.filter(r => r.status === 'Lunas').reduce((s, r) => s + r.total, 0);
    body.innerHTML = `
        <h2 class="font-serif text-xl font-semibold">${esc(p.nama)}</h2>
        <p class="text-muted">${esc(p.no_tlp)}</p><p class="text-muted">${esc(p.alamat)}</p>
        <p class="text-xs text-muted mt-1">Bergabung ${esc(p.bergabung)} · Total belanja lunas ${rupiah(belanja)}</p>
        <h3 class="font-semibold mt-4 mb-2">Riwayat Transaksi</h3>
        <div class="border-t border-sand">${p.riwayat.map(r => `
            <div class="flex justify-between items-center py-2.5 border-b border-sand gap-3">
                <div><p class="font-medium">${esc(r.kode)}</p><p class="text-xs text-muted">${esc(r.tanggal)}</p></div>
                <div class="text-right"><p class="whitespace-nowrap">${rupiah(r.total)}</p>${badge(r.status)}</div>
            </div>`).join('')}</div>
        <button onclick="closeModal('modal')" class="mt-5 w-full px-4 py-2 rounded-lg border border-sand">Tutup</button>`;
    openModal('modal');
}
render();
</script>
@endpush
