@extends('admin.layout')
@section('title', 'Transaksi')
@section('content')
<p class="text-xs text-muted mb-4">Data pada halaman ini masih dummy.</p>
<div class="flex flex-col sm:flex-row gap-3 mb-4">
    <input id="q" oninput="render()" placeholder="Cari kode / pelanggan..." class="px-3 py-2 rounded-lg border border-sand bg-white text-sm sm:w-64">
    <select id="fStatus" onchange="render()" class="px-3 py-2 rounded-lg border border-sand bg-white text-sm">
        <option value="">Semua status pembayaran</option><option>Lunas</option><option>Menunggu Pembayaran</option><option>Dibatalkan</option>
    </select>
</div>
<div class="bg-white rounded-xl border border-sand overflow-x-auto">
    <table class="w-full text-sm min-w-[520px]">
        <thead class="bg-sand/60 text-left"><tr>
            <th class="p-3">Kode</th><th class="p-3">Pelanggan</th><th class="p-3 hidden sm:table-cell">Tanggal</th>
            <th class="p-3">Total</th><th class="p-3">Status</th><th class="p-3"></th>
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
const data = @json($transaksi);

function render() {
    const q = document.getElementById('q').value.toLowerCase();
    const list = data.filter(t => (t.id + t.pelanggan).toLowerCase().includes(q) && (!fStatus.value || t.status === fStatus.value));
    rows.innerHTML = list.length ? list.map(t => `
        <tr class="border-t border-sand">
            <td class="p-3 font-medium whitespace-nowrap">${esc(t.id)}</td>
            <td class="p-3">${esc(t.pelanggan)}<div class="sm:hidden text-xs text-muted">${esc(t.tanggal)}</div></td>
            <td class="p-3 hidden sm:table-cell whitespace-nowrap">${esc(t.tanggal)}</td>
            <td class="p-3 whitespace-nowrap">${rupiah(t.total)}</td>
            <td class="p-3">${badge(t.status)}</td>
            <td class="p-3 text-right"><button onclick="detail('${t.id}')" class="text-wood hover:underline">Detail</button></td>
        </tr>`).join('') : '<tr><td colspan="6" class="p-6 text-center text-muted">Tidak ada transaksi.</td></tr>';
}

function detail(id) {
    const t = data.find(x => x.id === id);
    body.innerHTML = `
        <div class="flex justify-between items-start mb-3"><h2 class="font-serif text-xl font-semibold">${esc(t.id)}</h2>${badge(t.status)}</div>
        <p class="font-medium">${esc(t.pelanggan)}</p><p class="text-xs text-muted mb-3">${esc(t.tanggal)}</p>
        <div class="border-t border-sand">${t.items.map(i => `
            <div class="flex justify-between py-2 border-b border-sand gap-3">
                <span>${esc(i.nama)} <span class="text-muted">×${i.qty}</span></span><span class="whitespace-nowrap">${rupiah(i.qty * i.harga)}</span>
            </div>`).join('')}</div>
        <div class="flex justify-between font-semibold mt-3 mb-4"><span>Total</span><span>${rupiah(t.total)}</span></div>
        <div class="bg-cream rounded-lg p-3">
            <p class="font-semibold mb-1">Pembayaran</p>
            <p>Metode: ${esc(t.metode)}</p>
            <p>Status: ${esc(t.status)}</p>
            <p>Waktu bayar: ${esc(t.dibayar || '–')}</p>
        </div>
        <button onclick="closeModal('modal')" class="mt-5 w-full px-4 py-2 rounded-lg border border-sand">Tutup</button>`;
    openModal('modal');
}
render();
</script>
@endpush
