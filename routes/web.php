<?php

use Illuminate\Support\Facades\Route;

// --- Public Controllers ---
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController; // <-- Ensure this import points to your controller
// --- Admin Controllers ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController; // Import
use App\Http\Controllers\CouponController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Public Routes ---

Route::get('/', function () {
    return redirect()->route('books.index');
})->name('home');

// Public Book Listing & Detail (Using Slug for Detail)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/api/search/palette', [BookController::class, 'paletteSearch'])->name('api.search.palette');
// --- Public Category Browsing ---
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show'); // <-- Add this route

// --- XML Sitemap for SEO ---
Route::get('/sitemap.xml', function () {
    $books = \App\Models\Book::whereHas('category', fn($q) => $q->where('active', 1))
        ->select('slug', 'updated_at')
        ->latest('updated_at')
        ->get();
    $categories = \App\Models\Category::where('active', 1)
        ->select('slug', 'updated_at')
        ->get();

    return response()
        ->view('sitemap', compact('books', 'categories'))
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

// Shopping Cart Routes (Generally accessible)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');         // Show Cart
    Route::post('/add', [CartController::class, 'store'])->name('store');       // Add item to cart
    Route::patch('/update/{rowId}', [CartController::class, 'update'])->name('update');   // Update item quantity
    Route::delete('/remove/{rowId}', [CartController::class, 'destroy'])->name('destroy'); // Remove item
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');       // Clear entire cart
});

// Saved Volumes / Wishlist Routes (Guest session supported, auto-syncs on login)
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::delete('/{book}', [WishlistController::class, 'destroy'])->name('destroy');
    Route::post('/{book}/move-to-bag', [WishlistController::class, 'moveToBag'])->name('move-to-bag');
});

// Reviews & Social Proof Routes
Route::middleware('auth')->group(function () {
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reviews.store');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])
        ->name('reviews.helpful');
});

Route::post('/coupon', [CouponController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('coupon.store');
Route::delete('/coupon', [CouponController::class, 'destroy'])->name('coupon.destroy');
// --- Authenticated User Routes ---

Route::middleware(['auth', 'verified'])->group(function () { // 'verified' is optional, depends if you require email verification

    // User Dashboard (Default Breeze)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Profile (Default Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/logout-other-devices', [ProfileController::class, 'logoutOtherDevices'])->name('profile.logout-other-devices');

    // Checkout Process
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])
        ->middleware('throttle:15,1')
        ->name('checkout.process');
    Route::get('/checkout/return/{order}', [CheckoutController::class, 'return'])->name('checkout.return');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel'); // Optional cancel page

    // User Order History
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');       // List user's orders
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('orders.show'); // Show specific order details
    Route::get('/my-orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice'); // Download PDF Tax Invoice

});


// --- Admin Routes ---

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Book Management
    Route::resource('books', AdminBookController::class);

    // Admin Category Management (Excluding show route)
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // --- Admin Order Management ---
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index'); // List all orders
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show'); // Show specific order
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice'); // Download PDF Tax Invoice

    // Coupon management
    Route::resource('coupons', AdminCouponController::class)->except(['show']);

    // Transactional Mailbox Inspector (Dev & Sandbox)
    Route::get('/mailbox', [\App\Http\Controllers\Admin\AdminMailboxController::class, 'index'])->name('mailbox.index');
    Route::delete('/mailbox', [\App\Http\Controllers\Admin\AdminMailboxController::class, 'clear'])->name('mailbox.clear');


});


// Locale Switcher
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.switch');

// --- Stripe Webhook ---

// Note: Needs CSRF exemption in app/Http/Middleware/VerifyCsrfToken.php -> $except array
Route::post('/stripe/webhook', [App\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->middleware(\App\Http\Middleware\VerifyStripeWebhookSignature::class)
    ->name('cashier.webhook');

// --- Breeze Authentication Routes ---

require __DIR__ . '/auth.php';
