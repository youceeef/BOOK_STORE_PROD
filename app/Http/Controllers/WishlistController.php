<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\WishlistService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlistService
    ) {}

    /**
     * Display the wishlist / saved volumes page.
     */
    public function index(Request $request): View
    {
        $books = $this->wishlistService->getWishlistBooks($request->user());
        $count = $this->wishlistService->getWishlistCount($request->user());

        return view('wishlist.index', compact('books', 'count'));
    }

    /**
     * Toggle saved status for a book (AJAX or form).
     */
    public function toggle(Request $request): JsonResponse | RedirectResponse
    {
        $request->validate([
            'book_id' => 'required|integer|exists:books,id'
        ]);

        $result = $this->wishlistService->toggle((int) $request->book_id, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'attached' => $result['attached'],
                'wishlistCount' => $result['count'],
                'message' => $result['message'],
                'bookTitle' => $result['book_title'],
            ]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Remove an item directly from the wishlist.
     */
    public function destroy(Request $request, int $bookId): JsonResponse | RedirectResponse
    {
        $newCount = $this->wishlistService->remove($bookId, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'wishlistCount' => $newCount,
                'message' => __('Volume removed from your saved items.'),
                'isEmpty' => $newCount === 0
            ]);
        }

        return redirect()->route('wishlist.index')->with('success', __('Volume removed from your saved items.'));
    }

    /**
     * Move an item from wishlist into the shopping bag.
     */
    public function moveToBag(Request $request, int $bookId): JsonResponse | RedirectResponse
    {
        $book = Book::findOrFail($bookId);

        if ($book->stock < 1) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This volume is currently out of stock.')
                ], 422);
            }
            return back()->with('error', __('This volume is currently out of stock.'));
        }

        // Add to bag
        Cart::add([
            'id' => $book->id,
            'name' => $book->title,
            'price' => $book->price,
            'quantity' => 1,
            'attributes' => [
                'image' => $book->cover_image ?: 'images/covers/default.png',
                'slug' => $book->slug,
                'stock' => $book->stock
            ]
        ]);

        // Remove from wishlist
        $newWishlistCount = $this->wishlistService->remove($book->id, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => Cart::getTotalQuantity(),
                'wishlistCount' => $newWishlistCount,
                'message' => __('Moved to your shopping bag.'),
                'actionUrl' => route('cart.index'),
                'actionLabel' => __('View Bag')
            ]);
        }

        return redirect()->route('cart.index')->with('success', __('Moved to your shopping bag.'));
    }
}
