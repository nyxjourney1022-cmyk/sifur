<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Ambil semua review untuk satu produk
    public function index(int $barangId)
    {
        $barang  = Barang::findOrFail($barangId);
        $reviews = Review::with('user')
            ->where('barang_id', $barangId)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'komentar'   => $r->komentar,
                'user_name'  => $r->user->name,
                'avatar'     => $r->user->avatar
                    ? (str_starts_with($r->user->avatar, 'http')
                        ? $r->user->avatar
                        : asset('storage/' . $r->user->avatar))
                    : null,
                'created_at' => $r->created_at->diffForHumans(),
                'is_mine'    => Auth::check() && $r->user_id === Auth::id(),
            ]);

        $avgRating = $reviews->avg('rating');
        $myReview  = Auth::check()
            ? Review::where('barang_id', $barangId)->where('user_id', Auth::id())->first()
            : null;

        return response()->json([
            'success'    => true,
            'reviews'    => $reviews,
            'avg_rating' => round($avgRating, 1),
            'total'      => $reviews->count(),
            'my_review'  => $myReview ? [
                'id'       => $myReview->id,
                'rating'   => $myReview->rating,
                'komentar' => $myReview->komentar,
            ] : null,
        ]);
    }

    // Simpan atau update review
    public function store(Request $request, int $barangId)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        Barang::findOrFail($barangId);

        $review = Review::updateOrCreate(
            ['user_id' => Auth::id(), 'barang_id' => $barangId],
            ['rating' => $request->rating, 'komentar' => $request->komentar]
        );

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil disimpan.',
            'review'  => [
                'id'       => $review->id,
                'rating'   => $review->rating,
                'komentar' => $review->komentar,
            ],
        ]);
    }

    // Hapus review milik sendiri
    public function destroy(int $barangId)
    {
        $review = Review::where('barang_id', $barangId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review dihapus.']);
    }
}
