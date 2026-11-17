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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConcurrencyCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Book $book;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create([
            'name' => 'Shopper A',
            'email' => 'shopper.a@example.com',
        ]);

        $this->userB = User::factory()->create([
            'name' => 'Shopper B',
            'email' => 'shopper.b@example.com',
        ]);

        $this->category = Category::create([
            'name' => 'Rare Antiquarian',
            'slug' => 'rare-antiquarian',
            'is_active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'First Edition Ulysses',
            'slug' => 'first-edition-ulysses',
            'author' => 'James Joyce',
            'price' => 120.00,
            'stock' => 1, // Only 1 precious copy available
        ]);
    }

    public function test_stock_reservation_holds_inventory_and_blocks_concurrent_shopper(): void
    {
        $sessionA = 'sess_shoppera_11111';
        $sessionB = 'sess_shopperb_22222';

        $cartA = [
            ['id' => $this->book->id, 'quantity' => 1],
        ];

        // Shopper A initiates checkout and reserves the only copy
        $resultA = StockReservationService::reserveCart($this->userA, $cartA, $sessionA, 15);
        $this->assertTrue($resultA['success']);
        $this->assertNull($resultA['error']);

        // Assert reservation exists in database
        $this->assertDatabaseHas('stock_reservations', [
            'book_id' => $this->book->id,
            'session_id' => $sessionA,
            'user_id' => $this->userA->id,
            'quantity' => 1,
        ]);

        // Shopper B checks availability: effective stock should be 0 for Shopper B
        $availableForB = StockReservationService::getAvailableStock($this->book, $sessionB, $this->userB->id);
        $this->assertEquals(0, $availableForB);

        // Shopper B attempts to reserve the same copy
        $cartB = [
            ['id' => $this->book->id, 'quantity' => 1],
        ];
        $resultB = StockReservationService::reserveCart($this->userB, $cartB, $sessionB, 15);

        // Shopper B should be blocked
        $this->assertFalse($resultB['success']);
        $this->assertStringContainsString('reserved by other readers or out of stock', $resultB['error']);
    }

    public function test_expired_stock_reservations_are_automatically_released(): void
    {
        $sessionA = 'sess_expired_user_000';
        $sessionB = 'sess_active_buyer_999';

        // Create expired reservation held by Shopper A
        StockReservation::create([
            'book_id' => $this->book->id,
            'user_id' => $this->userA->id,
            'session_id' => $sessionA,
            'quantity' => 1,
            'expires_at' => now()->subMinutes(20), // Expired 20 minutes ago
        ]);

        // Shopper B reserves the book
        $cartB = [
            ['id' => $this->book->id, 'quantity' => 1],
        ];

        $resultB = StockReservationService::reserveCart($this->userB, $cartB, $sessionB, 15);

        // Must succeed because expired hold is automatically pruned
        $this->assertTrue($resultB['success']);
        $this->assertDatabaseMissing('stock_reservations', ['session_id' => $sessionA]);
        $this->assertDatabaseHas('stock_reservations', ['session_id' => $sessionB]);
    }

    public function test_pessimistic_lock_blocks_pre_charge_checkout_when_inventory_depleted(): void
    {
        // Add book to Cart for User A
        Cart::add([
            'id' => $this->book->id,
            'name' => $this->book->title,
            'price' => $this->book->price,
            'quantity' => 1,
            'attributes' => [],
        ]);

        // Simulate another buyer purchasing the last copy right before User A clicks Pay
        $this->book->update(['stock' => 0]);

        $response = $this->actingAs($this->userA)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_card_visa',
                'name' => 'Shopper A',
                'address' => '123 Fiction Lane',
                'city' => 'Dublin',
                'postal_code' => 'D01',
            ]);

        // Should be caught by the pre-charge pessimistic lock validation and redirected to cart
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_releasing_holds_clears_user_reservations(): void
    {
        $session = 'sess_test_release_hold';
        StockReservation::create([
            'book_id' => $this->book->id,
            'user_id' => $this->userA->id,
            'session_id' => $session,
            'quantity' => 1,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->assertEquals(1, StockReservation::count());

        StockReservationService::releaseHolds($this->userA->id, $session);

        $this->assertEquals(0, StockReservation::count());
    }

    public function test_coupon_pessimistic_lock_and_usage_count_integrity(): void
    {
        $coupon = Coupon::create([
            'code' => 'FLASH50',
            'type' => 'fixed',
            'value' => 50.00,
            'is_active' => true,
            'usage_limit' => 1,
            'usage_count' => 0,
        ]);

        $this->assertTrue($coupon->isValid());

        // Simulate first checkout increment
        DB::transaction(function () use ($coupon) {
            $locked = Coupon::where('id', $coupon->id)->lockForUpdate()->first();
            $locked->increment('usage_count');
        });

        $coupon->refresh();
        $this->assertEquals(1, $coupon->usage_count);
        $this->assertFalse($coupon->isValid()); // Limit was 1, now reached
    }
}
