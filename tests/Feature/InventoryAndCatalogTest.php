<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\StockReservation;
use App\Models\User;
use App\Services\StockReservationService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryAndCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::clear();
        session()->forget('coupon');
    }

    protected function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Fiction ' . uniqid(),
            'slug' => 'fiction-' . uniqid(),
            'active' => true,
        ], $overrides));
    }

    protected function createBook(array $overrides = []): Book
    {
        $catId = $overrides['category_id'] ?? $this->createCategory()->id;
        return Book::create(array_merge([
            'title' => 'Test Book ' . uniqid(),
            'slug' => 'test-book-' . uniqid(),
            'category_id' => $catId,
            'author' => 'Test Author',
            'description' => 'Test Description',
            'price' => 19.99,
            'stock' => 5,
        ], $overrides));
    }

    public function test_multi_item_reservation_rollback_when_one_item_exceeds_stock(): void
    {
        $category = $this->createCategory(['active' => true]);
        $bookA = $this->createBook(['category_id' => $category->id, 'stock' => 5, 'title' => 'Book A']);
        $bookB = $this->createBook(['category_id' => $category->id, 'stock' => 1, 'title' => 'Book B']);

        $cartItems = [
            ['id' => $bookA->id, 'quantity' => 2],
            ['id' => $bookB->id, 'quantity' => 3], // Exceeds stock of 1
        ];

        $result = StockReservationService::reserveCart(null, $cartItems, 'test_session_1');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Book B', $result['error']);

        // Assert all-or-nothing rollback: neither Book A nor Book B is reserved
        $this->assertDatabaseMissing('stock_reservations', [
            'book_id' => $bookA->id,
            'session_id' => 'test_session_1',
        ]);
        $this->assertDatabaseMissing('stock_reservations', [
            'book_id' => $bookB->id,
            'session_id' => 'test_session_1',
        ]);
    }

    public function test_stock_reservation_synchronizes_and_removes_unlisted_cart_lines(): void
    {
        $category = $this->createCategory(['active' => true]);
        $bookA = $this->createBook(['category_id' => $category->id, 'stock' => 5]);
        $bookB = $this->createBook(['category_id' => $category->id, 'stock' => 5]);

        // Initial hold for both books
        $cartInitial = [
            ['id' => $bookA->id, 'quantity' => 2],
            ['id' => $bookB->id, 'quantity' => 1],
        ];
        $res1 = StockReservationService::reserveCart(null, $cartInitial, 'sync_session');
        $this->assertTrue($res1['success']);
        $this->assertEquals(2, StockReservation::where('session_id', 'sync_session')->count());

        // Update cart: only Book A remains
        $cartUpdated = [
            ['id' => $bookA->id, 'quantity' => 2],
        ];
        $res2 = StockReservationService::reserveCart(null, $cartUpdated, 'sync_session');
        $this->assertTrue($res2['success']);

        // Book B hold should have been synchronized and removed
        $this->assertDatabaseHas('stock_reservations', [
            'book_id' => $bookA->id,
            'session_id' => 'sync_session',
        ]);
        $this->assertDatabaseMissing('stock_reservations', [
            'book_id' => $bookB->id,
            'session_id' => 'sync_session',
        ]);
    }

    public function test_expired_hold_cleanup_prunes_stale_reservations(): void
    {
        $book = $this->createBook(['stock' => 5]);

        // Create expired hold
        StockReservation::create([
            'session_id' => 'expired_sess',
            'book_id' => $book->id,
            'quantity' => 2,
            'expires_at' => now()->subMinutes(10),
        ]);

        // Create active hold
        StockReservation::create([
            'session_id' => 'active_sess',
            'book_id' => $book->id,
            'quantity' => 1,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->assertEquals(2, StockReservation::count());

        $pruned = StockReservationService::pruneExpired();
        $this->assertEquals(1, $pruned);

        $this->assertDatabaseMissing('stock_reservations', ['session_id' => 'expired_sess']);
        $this->assertDatabaseHas('stock_reservations', ['session_id' => 'active_sess']);
    }

    public function test_cumulative_cart_quantity_prevents_exceeding_stock_across_multiple_adds(): void
    {
        $book = $this->createBook(['stock' => 2, 'title' => 'Limited Edition']);

        // First add: 2 copies (allowed)
        $response1 = $this->post(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 2,
        ]);
        $response1->assertRedirect(route('cart.index'));
        $this->assertEquals(2, Cart::get($book->id)->quantity);

        // Second add: 1 more copy (exceeds stock of 2)
        $response2 = $this->post(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);
        $response2->assertSessionHas('error');
        // Cart quantity remains 2
        $this->assertEquals(2, Cart::get($book->id)->quantity);

        // AJAX request returns 422
        $responseJson = $this->postJson(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);
        $responseJson->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_wishlist_move_to_bag_respects_cumulative_cart_quantity(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook(['stock' => 1, 'title' => 'Single Copy']);

        // Add 1 copy to cart
        Cart::add([
            'id' => $book->id,
            'name' => $book->title,
            'price' => $book->price,
            'quantity' => 1,
        ]);

        // User saves book to wishlist
        $user->wishlistBooks()->attach($book->id);

        // Attempting to move from wishlist to bag when stock is already reached
        $response = $this->actingAs($user)->post(route('wishlist.move-to-bag', $book->id));
        $response->assertSessionHas('error');

        // Cart quantity remains 1
        $this->assertEquals(1, Cart::get($book->id)->quantity);
    }

    public function test_coupon_boundary_rules_end_of_day_and_percent_cap(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Attempt to create a 120% coupon fails validation
        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'code' => 'SUPER120',
            'type' => 'percent',
            'value' => 120,
        ]);
        $response->assertSessionHasErrors(['value']);

        // Create coupon expiring today (date-only)
        $todayStr = now()->format('Y-m-d');
        $coupon = Coupon::create([
            'code' => 'TODAYONLY',
            'type' => 'percent',
            'value' => 20,
            'expires_at' => $todayStr,
        ]);

        // Must still be valid on the expiration day (until end of day)
        $this->assertTrue($coupon->isValid());

        // Percent discount calculation caps at subtotal
        $coupon100 = Coupon::create([
            'code' => 'ALLFREE',
            'type' => 'percent',
            'value' => 100,
        ]);
        $this->assertEquals(50.0, $coupon100->calculateDiscount(50.0));
    }

    public function test_free_order_with_100_percent_coupon_completes_without_stripe_charge(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook(['stock' => 5, 'price' => 25.00]);

        Cart::add([
            'id' => $book->id,
            'name' => $book->title,
            'price' => $book->price,
            'quantity' => 1,
        ]);

        $coupon = Coupon::create([
            'code' => 'FREE100',
            'type' => 'percent',
            'value' => 100,
        ]);

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'name' => 'Jane Doe',
            'address' => '123 Free St',
            'city' => 'Booktown',
            'postal_code' => '12345',
        ]);

        $response->assertRedirect(route('checkout.success'));

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(0, $order->total_amount);
        $this->assertStringStartsWith('free_', $order->stripe_payment_intent_id);

        // Stock was decremented
        $this->assertEquals(4, $book->fresh()->stock);

        // Coupon usage was incremented
        $this->assertEquals(1, $coupon->fresh()->usage_count);

        // Cart and session coupon were cleared
        $this->assertTrue(Cart::isEmpty());
        $this->assertFalse(session()->has('coupon'));
    }

    public function test_inactive_category_direct_url_aborts_with_404(): void
    {
        $inactiveCat = $this->createCategory(['active' => false]);
        $book = $this->createBook(['category_id' => $inactiveCat->id]);

        $response = $this->get(route('categories.show', $inactiveCat->slug));
        $response->assertStatus(404);

        $bookResponse = $this->get(route('books.show', $book->slug));
        $bookResponse->assertStatus(404);
    }

    public function test_media_disk_stores_relative_paths_and_resolves_urls(): void
    {
        Storage::fake('public');
        config(['filesystems.media_disk' => 'public']);

        $admin = User::factory()->create(['is_admin' => true]);
        $category = $this->createCategory(['active' => true]);

        $file = UploadedFile::fake()->image('test_cover.jpg', 400, 600);

        // Store book with cover
        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Cover Test Book',
            'category_id' => $category->id,
            'author' => 'Test Author',
            'description' => 'A test book',
            'price' => 15.00,
            'stock' => 10,
            'cover_image' => $file,
        ]);
        $response->assertRedirect(route('admin.books.index'));

        $book = Book::where('title', 'Cover Test Book')->first();
        $this->assertNotNull($book);
        $this->assertStringStartsWith('covers/', $book->cover_image);
        Storage::disk('public')->assertExists($book->cover_image);

        // URL resolves correctly
        $this->assertStringContainsString('storage/' . $book->cover_image, $book->cover_url);

        // Deleting book cleans up cover file
        $delResponse = $this->actingAs($admin)->delete(route('admin.books.destroy', $book->id));
        $delResponse->assertRedirect(route('admin.books.index'));
        Storage::disk('public')->assertMissing($book->cover_image);
    }
}
