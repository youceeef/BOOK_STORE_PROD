<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_status_to_shipped_with_tracking(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'paid',
            'total_amount' => 59.90,
            'billing_address' => '123 Baker St, London',
            'shipping_address' => '123 Baker St, London',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'shipped',
                'carrier' => 'Royal Mail Special Delivery',
                'tracking_number' => 'RM-987654321GB',
                'estimated_delivery_at' => now()->addDays(2)->toDateString(),
                'fulfillment_notes' => 'Dispatched in reinforced cardboard mailer.',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect("/admin/orders/{$order->id}");

        $order->refresh();

        $this->assertSame('shipped', $order->status);
        $this->assertSame('Royal Mail Special Delivery', $order->carrier);
        $this->assertSame('RM-987654321GB', $order->tracking_number);
        $this->assertNotNull($order->shipped_at);
        $this->assertNotNull($order->estimated_delivery_at);
        $this->assertSame('Dispatched in reinforced cardboard mailer.', $order->fulfillment_notes);
        $this->assertSame(3, $order->getStepIndex());
    }

    public function test_admin_marking_shipped_without_explicit_tracking_auto_generates_tracking(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'processing',
            'total_amount' => 42.00,
            'billing_address' => '456 Oxford St, London',
            'shipping_address' => '456 Oxford St, London',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'shipped',
            ]);

        $response->assertSessionHasNoErrors();

        $order->refresh();

        $this->assertSame('shipped', $order->status);
        $this->assertSame('DHL Express', $order->carrier);
        $this->assertStringStartsWith('TRK-', $order->tracking_number);
        $this->assertNotNull($order->shipped_at);
        $this->assertNotNull($order->estimated_delivery_at);
    }

    public function test_admin_can_mark_order_as_delivered(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'shipped',
            'carrier' => 'FedEx',
            'tracking_number' => 'FX-112233',
            'total_amount' => 88.00,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'delivered',
            ]);

        $response->assertSessionHasNoErrors();

        $order->refresh();

        $this->assertSame('delivered', $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertSame(4, $order->getStepIndex());
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'paid',
            'total_amount' => 30.00,
        ]);

        $response = $this
            ->actingAs($customer)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'shipped',
            ]);

        $this->assertTrue(in_array($response->status(), [302, 403]));

        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_customer_can_view_fulfillment_tracker_on_order_show_view(): void
    {
        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'shipped',
            'carrier' => 'DHL Express',
            'tracking_number' => 'TRK-ALPHA-01',
            'estimated_delivery_at' => now()->addDays(3)->toDateString(),
            'total_amount' => 75.00,
            'billing_address' => '742 Evergreen Terrace',
            'shipping_address' => '742 Evergreen Terrace',
        ]);

        $response = $this
            ->actingAs($customer)
            ->get("/my-orders/{$order->id}");

        $response->assertOk();
        $response->assertSee('Fulfillment Tracker');
        $response->assertSee('In Transit with DHL Express');
        $response->assertSee('TRK-ALPHA-01');
        $response->assertSee('Track Package');
    }

    public function test_cancelled_order_displays_voided_notice(): void
    {
        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'cancelled',
            'total_amount' => 50.00,
            'fulfillment_notes' => 'Customer requested cancellation before shipment.',
        ]);

        $response = $this
            ->actingAs($customer)
            ->get("/my-orders/{$order->id}");

        $response->assertOk();
        $response->assertSee('Order Voided / Cancelled');
        $response->assertSee('Customer requested cancellation before shipment.');
    }
}
