<?php

namespace Tests\Feature;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Models\Book;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutStateMachineTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $category->id,
            'title' => 'Test Novel',
            'slug' => 'test-novel',
            'author' => 'Author A',
            'price' => 20.00,
            'stock' => 5,
            'is_featured' => true,
        ]);

        $this->customer = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);
    }

    public function test_checkout_requires_complete_delivery_address(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_card_visa',
                // missing name, address, city, postal_code
            ]);

        $response->assertSessionHasErrors(['name', 'address', 'city', 'postal_code']);
    }

    public function test_checkout_process_redirects_if_cart_empty(): void
    {
        Cart::clear();

        $response = $this->actingAs($this->customer)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_card_visa',
                'name' => 'John Doe',
                'address' => '123 High St',
                'city' => 'Springfield',
                'postal_code' => '12345',
            ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Your cart is empty.');
    }

    public function test_complete_order_payment_is_idempotent_and_claims_inventory_once(): void
    {
        Mail::fake();

        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'shipping_address' => json_encode([
                'name' => 'John Doe',
                'address' => '123 High St',
                'city' => 'Springfield',
                'postal_code' => '12345',
            ]),
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 2,
            'price' => 20.00,
        ]);

        $this->assertEquals(5, $this->book->fresh()->stock);

        $controller = app(CheckoutController::class);

        // First transition
        $controller->completeOrderPayment($order, 'pi_test_state_123');
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals(3, $this->book->fresh()->stock);
        $this->assertNotNull($order->fresh()->confirmation_sent_at);

        // Second transition (must be idempotent, not deduct inventory again)
        $controller->completeOrderPayment($order, 'pi_test_state_123');
        $this->assertEquals(3, $this->book->fresh()->stock);

        Mail::assertSent(\App\Mail\OrderConfirmation::class, 1);
    }

    public function test_webhook_handles_pending_order_transition_and_deduplicates(): void
    {
        Mail::fake();

        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'stripe_payment_intent_id' => 'pi_webhook_test_999',
            'shipping_address' => json_encode([
                'name' => 'Jane Doe',
                'address' => '456 Oak Lane',
                'city' => 'Metropolis',
                'postal_code' => '54321',
            ]),
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 20.00,
        ]);

        $webhookController = app(WebhookController::class);

        $payload = [
            'data' => [
                'object' => [
                    'id' => 'pi_webhook_test_999',
                    'customer' => $this->customer->stripe_id ?? 'cus_mock_test',
                    'amount_received' => 2000,
                    'metadata' => [
                        'order_id' => (string) $order->id,
                        'user_id' => (string) $this->customer->id,
                    ],
                ],
            ],
        ];

        // Ensure user has stripe_id for webhook customer lookup
        $this->customer->update(['stripe_id' => 'cus_mock_test']);

        // First webhook delivery
        $response1 = $webhookController->handlePaymentIntentSucceeded($payload);
        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals(4, $this->book->fresh()->stock);

        // Second duplicate webhook delivery
        $response2 = $webhookController->handlePaymentIntentSucceeded($payload);
        $this->assertEquals(200, $response2->getStatusCode());
        $this->assertEquals(4, $this->book->fresh()->stock);

        Mail::assertSent(\App\Mail\OrderConfirmation::class, 1);
    }

    public function test_get_safe_checkout_return_endpoint_behavior(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'stripe_payment_intent_id' => 'pi_paid_test',
        ]);

        // Unauthorized user gets 403
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser)
            ->get(route('checkout.return', $order->id))
            ->assertForbidden();

        // Authorized user with paid order gets redirected to checkout success
        $this->actingAs($this->customer)
            ->get(route('checkout.return', $order->id))
            ->assertRedirect(route('checkout.success'));
    }

    public function test_shipped_order_preserves_revenue_and_verified_buyer_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'stripe_payment_intent_id' => 'pi_shipped_test',
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 20.00,
        ]);

        // Customer is verified buyer
        $this->assertTrue($this->customer->hasPurchased($this->book));

        // Admin ships the order
        $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'shipped',
            'carrier' => 'FedEx Express',
            'tracking_number' => 'FEDEX12345678',
        ])->assertRedirect();

        $freshOrder = $order->fresh();
        $this->assertEquals('shipped', $freshOrder->status);
        $this->assertEquals('shipped', $freshOrder->fulfillment_status);
        $this->assertEquals('paid', $freshOrder->payment_status);

        // Revenue must NOT disappear from admin dashboard
        $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('20.00');

        // Customer must STILL be recognized as verified buyer
        $this->assertTrue($this->customer->hasPurchased($this->book));
    }

    public function test_formatted_shipping_address_does_not_render_raw_json(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'shipping_address' => json_encode([
                'name' => 'Alice Reader',
                'address' => '742 Evergreen Terrace',
                'city' => 'Springfield',
                'postal_code' => '97477',
            ]),
        ]);

        $formatted = $order->formatted_shipping_address;
        $this->assertStringContainsString('Alice Reader', $formatted);
        $this->assertStringContainsString('742 Evergreen Terrace', $formatted);
        $this->assertStringContainsString('Springfield 97477', $formatted);
        $this->assertStringNotContainsString('{', $formatted);
        $this->assertStringNotContainsString('"', $formatted);
    }
}
