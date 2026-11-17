<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $admin;
    protected Order $order;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Jane Reader',
            'email' => 'jane@example.com',
            'is_admin' => false,
        ]);

        $this->otherUser = User::factory()->create([
            'name' => 'John Stranger',
            'email' => 'stranger@example.com',
            'is_admin' => false,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Store Admin',
            'email' => 'admin@bookstore.test',
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Philosophy',
            'slug' => 'philosophy',
            'is_active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $category->id,
            'title' => 'The Metamorphosis',
            'slug' => 'the-metamorphosis',
            'author' => 'Franz Kafka',
            'price' => 18.50,
            'stock' => 15,
        ]);

        $this->order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'paid',
            'total_amount' => 37.00,
            'stripe_payment_intent_id' => 'pi_test_sample_invoice_123',
            'billing_address' => '742 Evergreen Terrace, Springfield',
            'shipping_address' => '742 Evergreen Terrace, Springfield',
            'coupon_code' => 'STUDENT10',
            'discount_amount' => 4.00,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'book_id' => $this->book->id,
            'quantity' => 2,
            'price' => 18.50,
        ]);
    }

    public function test_guest_cannot_download_invoice(): void
    {
        $response = $this->get(route('orders.invoice', $this->order));

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_download_another_users_invoice(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get(route('orders.invoice', $this->order));

        $response->assertStatus(403);
    }

    public function test_owner_can_download_invoice_as_pdf(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.invoice', $this->order));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $expectedFilename = 'invoice-ORD-' . str_pad($this->order->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', $expectedFilename)
        );
    }

    public function test_admin_can_download_any_order_invoice(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.invoice', $this->order));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_order_show_view_displays_invoice_download_button(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.show', $this->order));

        $response->assertStatus(200);
        $response->assertSee(route('orders.invoice', $this->order));
        $response->assertSee('Download PDF Invoice');
    }
}
