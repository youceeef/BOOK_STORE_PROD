<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new or updated review for a book.
     */
    public function store(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'title'   => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $user = Auth::user();
        $isVerified = $user->hasPurchased($book);

        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => $validated['rating'],
                'title'  => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'is_verified_purchase' => $isVerified,
            ]
        );

        $message = $isVerified
            ? 'Thank you! Your verified purchase review has been published.'
            : 'Thank you! Your reader review has been published.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'review'  => $review->load('user'),
                'avgRating' => $book->fresh()->averageRating(),
                'reviewsCount' => $book->fresh()->reviewsCount(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Increment the helpful counter for a review.
     */
    public function helpful(Request $request, Review $review): JsonResponse|RedirectResponse
    {
        $votedKey = 'voted_review_' . $review->id;

        if (session()->has($votedKey)) {
            $msg = 'You have already marked this review as helpful.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('info', $msg);
        }

        $review->increment('helpful_count');
        session([$votedKey => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback.',
                'helpful_count' => $review->fresh()->helpful_count,
            ]);
        }

        return back()->with('success', 'Marked review as helpful.');
    }
}
