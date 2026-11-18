<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDataRetentionTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected Book $book;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Literature',
            'slug' => 'literature',
            'active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Historical Masterpiece',
            'slug' => 'historical-masterpiece',
            'author' => 'Classic Author',
            'price' => 30.00,
            'stock' => 10,
            'is_featured' => true,
        ]);

        $this->customer = User::factory()->create([
            'name' => 'John Reader',
            'email' => 'john.reader@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_user_deletion_anonymizes_account_and_preserves_orders(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'customer_name_snapshot' => $this->customer->name,
            'customer_email_snapshot' => $this->customer->email,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 30.00,
            'stripe_payment_intent_id' => 'pi_retention_user_1',
            'shipping_address' => json_encode(['name' => 'John Reader', 'address' => '123 Test St']),
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'title_snapshot' => $this->book->title,
            'author_snapshot' => $this->book->author,
            'quantity' => 1,
            'price' => 30.00,
        ]);

        // Customer deletes their account
        $response = $this->actingAs($this->customer)->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');
        $this->assertGuest();

        // Order MUST still exist in database
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $freshOrder = $order->fresh();
        $this->assertNotNull($freshOrder);
        $this->assertEquals('John Reader', $freshOrder->customer_name);

        // User PII must be anonymized
        $anonymizedUser = User::find($this->customer->id);
        $this->assertNotNull($anonymizedUser);
        $this->assertStringContainsString('Former Customer', $anonymizedUser->name);
        $this->assertStringContainsString('anonymized_', $anonymizedUser->email);
    }

    public function test_deleting_book_with_orders_archives_inventory_and_preserves_order_items(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 30.00,
            'stripe_payment_intent_id' => 'pi_retention_book_1',
        ]);

        $orderItem = $order->items()->create([
            'book_id' => $this->book->id,
            'title_snapshot' => 'Historical Masterpiece',
            'author_snapshot' => 'Classic Author',
            'quantity' => 1,
            'price' => 30.00,
        ]);

        // Admin attempts to delete the sold book
        $response = $this->actingAs($this->admin)->delete(route('admin.books.destroy', $this->book->slug));
        $response->assertRedirect(route('admin.books.index'));

        // Book must not have deleted the order item
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id]);
        $this->assertEquals('Historical Masterpiece', $orderItem->fresh()->title);

        // Book row preserved with inventory archived
        $this->assertDatabaseHas('books', ['id' => $this->book->id, 'stock' => 0]);
    }

    public function test_cancelling_paid_order_restores_inventory_and_coupon(): void
    {
        $coupon = Coupon::create([
            'code' => 'RETENTION10',
            'type' => 'fixed',
            'value' => 10.00,
            'usage_count' => 1,
            'usage_limit' => 100,
        ]);

        $this->book->update(['stock' => 5]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 20.00,
            'stripe_payment_intent_id' => 'pi_cancel_test_555',
            'coupon_code' => $coupon->code,
            'discount_amount' => 10.00,
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'title_snapshot' => $this->book->title,
            'author_snapshot' => $this->book->author,
            'quantity' => 2,
            'price' => 15.00,
        ]);

        // Admin cancels the order
        $response = $this->actingAs($this->admin)->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'cancelled',
            'fulfillment_notes' => 'Customer requested cancellation prior to dispatch',
        ]);

        $response->assertRedirect();

        $freshOrder = $order->fresh();
        $this->assertEquals('cancelled', $freshOrder->status);
        $this->assertEquals('cancelled', $freshOrder->fulfillment_status);
        $this->assertEquals('refunded', $freshOrder->payment_status);
        $this->assertNotNull($freshOrder->cancelled_at);

        // Inventory must be restored (+2 copies)
        $this->assertEquals(7, $this->book->fresh()->stock);

        // Coupon usage must be decremented
        $this->assertEquals(0, $coupon->fresh()->usage_count);
    }

    public function test_invoice_renders_historical_snapshot_even_after_book_title_edit(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'customer_name_snapshot' => 'John Reader',
            'customer_email_snapshot' => 'john.reader@example.com',
            'status' => 'paid',
            'payment_status' => 'paid',
            'fulfillment_status' => 'unfulfilled',
            'total_amount' => 30.00,
            'stripe_payment_intent_id' => 'pi_snapshot_test',
            'shipping_address' => json_encode([
                'name' => 'John Reader',
                'address' => '999 Archival Way',
                'city' => 'Alexandria',
                'postal_code' => '10001',
            ]),
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'title_snapshot' => 'First Edition Title',
            'author_snapshot' => 'First Author',
            'quantity' => 1,
            'price' => 30.00,
        ]);

        // Now catalog editor updates the book's title
        $this->book->update(['title' => 'Updated Second Edition Title']);

        // Download or view invoice
        $response = $this->actingAs($this->customer)->get(route('orders.show', $order->id));
        $response->assertOk();

        // PDF download
        $pdfResponse = $this->actingAs($this->customer)->get(route('orders.invoice', $order->id));
        $pdfResponse->assertOk();
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }
}
