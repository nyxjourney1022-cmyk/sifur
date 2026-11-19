{{-- ══ Review Section ══════════════════════════════════════════
     Usage: @include('partials.reviews', ['barangId' => $product['id']])
════════════════════════════════════════════════════════════ --}}
<div class="reviews-section mt-8" data-barang="{{ $barangId }}">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h3 class="font-semibold text-ink text-lg">Ulasan Produk</h3>
            <div id="avg-stars-{{ $barangId }}" class="flex items-center gap-1 text-amber-400 text-sm"></div>
            <span id="avg-label-{{ $barangId }}" class="text-sm text-muted"></span>
        </div>
        @auth
        <button onclick="toggleReviewForm({{ $barangId }})"
                id="btn-tulis-{{ $barangId }}"
                class="text-sm font-semibold text-wood hover:text-wood-dark transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span id="btn-tulis-label-{{ $barangId }}">Tulis Ulasan</span>
        </button>
        @endauth
    </div>

    {{-- Form tulis review --}}
    @auth
    <div id="review-form-{{ $barangId }}" class="hidden bg-sand rounded-2xl p-5 mb-5">
        <p class="text-sm font-semibold text-ink mb-3">Beri Rating</p>
        <div class="flex gap-1 mb-4" id="star-input-{{ $barangId }}">
            @for($i = 1; $i <= 5; $i++)
            <button type="button" onclick="setRating({{ $barangId }}, {{ $i }})"
                    data-star="{{ $i }}"
                    class="star-btn text-3xl text-sand-400 hover:text-amber-400 transition-colors">★</button>
            @endfor
        </div>
        <input type="hidden" id="rating-val-{{ $barangId }}" value="0">
        <textarea id="komentar-{{ $barangId }}" rows="3"
                  placeholder="Ceritakan pengalamanmu dengan produk ini... (opsional)"
                  class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-wood focus:outline-none resize-none mb-3"></textarea>
        <div id="review-form-error-{{ $barangId }}" class="hidden text-sm text-red-600 mb-2"></div>
        <div class="flex gap-2">
            <button onclick="toggleReviewForm({{ $barangId }})"
                    class="flex-1 border-2 border-sand bg-white text-muted font-medium py-2.5 rounded-full hover:border-ink/20 transition-colors text-sm">
                Batal
            </button>
            <button onclick="submitReview({{ $barangId }})"
                    id="btn-submit-{{ $barangId }}"
                    class="flex-1 bg-wood text-white font-semibold py-2.5 rounded-full hover:bg-wood-dark transition-colors text-sm">
                Kirim Ulasan
            </button>
        </div>
    </div>
    @endauth

    {{-- List review --}}
    <div id="review-list-{{ $barangId }}" class="space-y-4">
        <div class="flex items-center justify-center py-8 text-muted">
            <svg class="w-5 h-5 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Memuat ulasan...
        </div>
    </div>
</div>

<script>
(function() {
    const bid = {{ $barangId }};

    // Load reviews saat halaman siap
    loadReviews(bid);

    window.loadReviews = async function(barangId) {
        try {
            const res  = await fetch(`/reviews/${barangId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            renderReviews(barangId, data);
        } catch(e) {
            document.getElementById(`review-list-${barangId}`).innerHTML =
                `<p class="text-sm text-muted text-center py-4">Gagal memuat ulasan.</p>`;
        }
    };

    window.renderReviews = function(barangId, data) {
        // Update rata-rata
        const avgStars = document.getElementById(`avg-stars-${barangId}`);
        const avgLabel = document.getElementById(`avg-label-${barangId}`);
        if (avgStars && data.total > 0) {
            avgStars.innerHTML = renderStars(data.avg_rating);
            avgLabel.textContent = `${data.avg_rating} (${data.total} ulasan)`;
        } else if (avgLabel) {
            avgLabel.textContent = 'Belum ada ulasan';
        }

        // Pre-fill form jika sudah pernah review
        if (data.my_review) {
            setRating(barangId, data.my_review.rating);
            const komentar = document.getElementById(`komentar-${barangId}`);
            if (komentar) komentar.value = data.my_review.komentar ?? '';
            const btnLabel = document.getElementById(`btn-tulis-label-${barangId}`);
            if (btnLabel) btnLabel.textContent = 'Edit Ulasan';
        }

        // Render list
        const list = document.getElementById(`review-list-${barangId}`);
        if (!list) return;

        if (data.reviews.length === 0) {
            list.innerHTML = `<p class="text-sm text-muted text-center py-6">Belum ada ulasan untuk produk ini. Jadilah yang pertama!</p>`;
            return;
        }

        list.innerHTML = data.reviews.map(r => `
            <div class="flex gap-3" id="review-item-${r.id}">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-wood flex items-center justify-center shrink-0">
                    ${r.avatar
                        ? `<img src="${r.avatar}" class="w-full h-full object-cover" referrerpolicy="no-referrer">`
                        : `<span class="text-white text-sm font-bold">${r.user_name[0].toUpperCase()}</span>`
                    }
                </div>
                <div class="flex-1 bg-sand rounded-2xl px-4 py-3">
                    <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-ink">${r.user_name}</span>
                            <span class="flex text-amber-400 text-xs">${renderStars(r.rating)}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted">${r.created_at}</span>
                            ${r.is_mine ? `
                            <button onclick="deleteReview(${barangId})"
                                    class="text-xs text-red-400 hover:text-red-600 transition-colors">Hapus</button>
                            ` : ''}
                        </div>
                    </div>
                    ${r.komentar ? `<p class="text-sm text-ink mt-1">${r.komentar}</p>` : ''}
                </div>
            </div>
        `).join('');
    };

    window.renderStars = function(rating) {
        return Array.from({length: 5}, (_, i) =>
            `<span class="${i < Math.round(rating) ? 'text-amber-400' : 'text-gray-300'}">★</span>`
        ).join('');
    };

    window.setRating = function(barangId, val) {
        const input = document.getElementById(`rating-val-${barangId}`);
        if (input) input.value = val;
        document.querySelectorAll(`#star-input-${barangId} .star-btn`).forEach(btn => {
            btn.classList.toggle('text-amber-400', btn.dataset.star <= val);
            btn.classList.toggle('text-gray-300', btn.dataset.star > val);
        });
    };

    window.toggleReviewForm = function(barangId) {
        const form = document.getElementById(`review-form-${barangId}`);
        form?.classList.toggle('hidden');
    };

    window.submitReview = async function(barangId) {
        const rating   = parseInt(document.getElementById(`rating-val-${barangId}`)?.value ?? 0);
        const komentar = document.getElementById(`komentar-${barangId}`)?.value.trim();
        const errDiv   = document.getElementById(`review-form-error-${barangId}`);
        const btn      = document.getElementById(`btn-submit-${barangId}`);

        if (errDiv) errDiv.classList.add('hidden');

        if (!rating || rating < 1) {
            if (errDiv) { errDiv.textContent = 'Pilih rating bintang dulu.'; errDiv.classList.remove('hidden'); }
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Menyimpan...';

        try {
            const res  = await fetch(`/reviews/${barangId}`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ rating, komentar }),
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById(`review-form-${barangId}`)?.classList.add('hidden');
                showToast('Ulasan berhasil disimpan!');
                loadReviews(barangId);
            } else {
                if (errDiv) { errDiv.textContent = data.message ?? 'Gagal menyimpan.'; errDiv.classList.remove('hidden'); }
            }
        } catch(e) {
            if (errDiv) { errDiv.textContent = 'Gagal terhubung ke server.'; errDiv.classList.remove('hidden'); }
        } finally {
            btn.disabled    = false;
            btn.textContent = 'Kirim Ulasan';
        }
    };

    window.deleteReview = async function(barangId) {
        if (!confirm('Hapus ulasan ini?')) return;
        try {
            const res  = await fetch(`/reviews/${barangId}`, {
                method:  'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            const data = await res.json();
            if (data.success) {
                showToast('Ulasan dihapus.');
                loadReviews(barangId);
                const btnLabel = document.getElementById(`btn-tulis-label-${barangId}`);
                if (btnLabel) btnLabel.textContent = 'Tulis Ulasan';
            }
        } catch(e) {}
    };
})();
</script>
