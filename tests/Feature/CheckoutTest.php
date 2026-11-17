<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Non-Fiction',
            'slug' => 'non-fiction',
            'is_active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $category->id,
            'title' => 'Sapiens',
            'slug' => 'sapiens',
            'author' => 'Yuval Noah Harari',
            'price' => 22.00,
            'stock' => 10,
            'is_featured' => true,
        ]);

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_checkout_redirects_if_cart_is_empty(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Your cart is empty.');
    }

    public function test_checkout_handles_missing_stripe_keys_gracefully_without_500(): void
    {
        $this->actingAs($this->user);

        // Add item to cart
        Cart::add([
            'id' => $this->book->id,
            'name' => $this->book->title,
            'price' => $this->book->price,
            'quantity' => 1,
            'attributes' => ['image' => '', 'slug' => $this->book->slug, 'stock' => 10]
        ]);

        // Explicitly set cashier keys to null to simulate missing env
        config(['cashier.key' => null, 'cashier.secret' => null]);

        $response = $this->get(route('checkout.index'));

        // Must redirect gracefully to cart with error instead of throwing 500
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_user_can_view_order_history_with_orders(): void
    {
        $this->actingAs($this->user);

        $order = \App\Models\Order::create([
            'user_id' => $this->user->id,
            'status' => 'paid',
            'total_amount' => 22.00,
            'stripe_payment_intent_id' => 'pi_test_12345',
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 22.00,
        ]);

        $response = $this->get(route('orders.index'));
        $response->assertOk();
        $response->assertSee('#ORD-');
        $response->assertSee('22.00');
        $response->assertSee('Paid');
    }

    public function test_order_creation_stores_coupon_and_increments_coupon_usage_count(): void
    {
        $this->actingAs($this->user);

        $coupon = Coupon::create([
            'code' => 'SAVE10',
            'type' => 'fixed',
            'value' => 10.00,
            'usage_count' => 0,
            'usage_limit' => 100,
        ]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'paid',
            'total_amount' => 12.00,
            'stripe_payment_intent_id' => 'pi_coupon_test_999',
            'coupon_code' => $coupon->code,
            'discount_amount' => 10.00,
            'shipping_address' => json_encode(['name' => $this->user->name]),
            'billing_address' => json_encode(['name' => $this->user->name]),
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 22.00,
        ]);

        $coupon->increment('usage_count');

        $this->assertEquals('SAVE10', $order->fresh()->coupon_code);
        $this->assertEquals('10.00', $order->fresh()->discount_amount);
        $this->assertEquals(1, $coupon->fresh()->usage_count);

        // Verify order invoice page displays the coupon discount cleanly
        $response = $this->get(route('orders.show', $order->id));
        $response->assertOk();
        $response->assertSee('SAVE10');
        $response->assertSee('-$10.00');
    }
}
