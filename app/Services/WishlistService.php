<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Collection;

class WishlistService
{
    /**
     * Retrieve all wishlisted books for current user or guest session.
     */
    public function getWishlistBooks(?User $user): Collection
    {
        if ($user) {
            return $user->wishlistBooks()
                ->with('category')
                ->latest('wishlists.created_at')
                ->get();
        }

        $guestIds = session('guest_wishlist', []);
        if (empty($guestIds)) {
            return collect();
        }

        return Book::with('category')
            ->whereIn('id', $guestIds)
            ->get();
    }

    /**
     * Get the count of saved books.
     */
    public function getWishlistCount(?User $user): int
    {
        if ($user) {
            return $user->wishlists()->count();
        }

        return count(session('guest_wishlist', []));
    }

    /**
     * Check if a book is wishlisted.
     */
    public function isWishlisted(int $bookId, ?User $user): bool
    {
        if ($user) {
            return $user->hasInWishlist($bookId);
        }

        return in_array($bookId, session('guest_wishlist', []));
    }

    /**
     * Toggle saved status for a book.
     */
    public function toggle(int $bookId, ?User $user): array
    {
        $book = Book::findOrFail($bookId);

        if ($user) {
            $existing = Wishlist::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $attached = false;
                $message = __('Removed from your saved volumes.');
            } else {
                Wishlist::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                ]);
                $attached = true;
                $message = __('Added to your saved volumes.');
            }

            $count = $user->wishlists()->count();
        } else {
            $guestWishlist = session('guest_wishlist', []);

            if (in_array($book->id, $guestWishlist)) {
                $guestWishlist = array_values(array_diff($guestWishlist, [$book->id]));
                $attached = false;
                $message = __('Removed from your saved volumes.');
            } else {
                $guestWishlist[] = $book->id;
                $attached = true;
                $message = __('Added to your saved volumes.');
            }

            session(['guest_wishlist' => $guestWishlist]);
            $count = count($guestWishlist);
        }

        return [
            'attached' => $attached,
            'count' => $count,
            'message' => $message,
            'book_title' => $book->title,
        ];
    }

    /**
     * Remove an item from wishlist.
     */
    public function remove(int $bookId, ?User $user): int
    {
        if ($user) {
            Wishlist::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->delete();
            return $user->wishlists()->count();
        }

        $guestWishlist = session('guest_wishlist', []);
        $guestWishlist = array_values(array_diff($guestWishlist, [$bookId]));
        session(['guest_wishlist' => $guestWishlist]);
        return count($guestWishlist);
    }

    /**
     * Synchronize guest session wishlist items to user account upon authentication.
     */
    public function syncGuestWishlist(User $user): void
    {
        $guestIds = session('guest_wishlist', []);
        if (empty($guestIds)) {
            return;
        }

        foreach ($guestIds as $bookId) {
            if (Book::where('id', $bookId)->exists()) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'book_id' => $bookId,
                ]);
            }
        }

        session()->forget('guest_wishlist');
    }
}
