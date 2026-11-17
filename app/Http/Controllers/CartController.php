<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart; // Use the Facade alias
use App\Models\Book; // Import Book model
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        $cartCount = Cart::getTotalQuantity(); // Get total number of items

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request): RedirectResponse | JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Book::findOrFail($request->book_id);

        // Check if enough stock is available
        if ($book->stock < $request->quantity) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available for "' . $book->title . '". Only ' . $book->stock . ' left.'
                ], 422);
            }
            return back()->with('error', 'Not enough stock available for "' . $book->title . '". Only ' . $book->stock . ' left.');
        }

        Cart::add([
            'id' => $book->id, // unique row ID
            'name' => $book->title,
            'price' => $book->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $book->cover_image ?: 'images/covers/default.png',
                'slug' => $book->slug,
                'stock' => $book->stock // Store original stock for reference if needed
            ]
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'title' => 'Added to Bag',
                'message' => '"' . $book->title . '" has been added to your shopping bag.',
                'actionUrl' => route('cart.index'),
                'actionLabel' => 'View Bag',
                'cartCount' => Cart::getTotalQuantity(),
                'cartTotal' => Cart::getTotal()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Book added to cart successfully!');
    }

    /**
     * Update the specified item quantity in storage.
     */
    public function update(Request $request, string $rowId): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Get item details before update to check stock
        $item = Cart::get($rowId);
        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Cart item not found.');
        }

        $book = Book::find($item->id); // Find the corresponding book
        if (!$book) {
            // If book deleted while in cart, remove it
            Cart::remove($rowId);
            return redirect()->route('cart.index')->with('error', 'The book for this cart item no longer exists and was removed.');
        }

        // Check stock before updating
        if ($book->stock < $request->quantity) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock for "' . $book->title . '". Only ' . $book->stock . ' available.'
                ], 422);
            }
            return redirect()->route('cart.index')
                ->with('error', 'Not enough stock for "' . $book->title . '". Only ' . $book->stock . ' available.');
        }

        Cart::update($rowId, [
            'quantity' => [
                'relative' => false, // set to false to replace current quantity
                'value' => $request->quantity
            ],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shopping bag updated.',
                'cartCount' => Cart::getTotalQuantity(),
                'cartTotal' => Cart::getTotal()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Request $request, string $rowId): RedirectResponse | JsonResponse
    {
        Cart::remove($rowId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Volume removed from shopping bag.',
                'cartCount' => Cart::getTotalQuantity(),
                'cartTotal' => Cart::getTotal(),
                'isEmpty' => Cart::isEmpty()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Volume removed from shopping bag.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request): RedirectResponse | JsonResponse
    {
        Cart::clear();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shopping bag cleared.',
                'cartCount' => 0,
                'cartTotal' => 0,
                'isEmpty' => true
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Shopping bag cleared.');
    }
}
