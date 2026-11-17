<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->regularUser = User::factory()->create([
            'is_admin' => false,
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.dashboard'));
        // Middleware redirects non-admins or returns 403
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_admin_can_access_dashboard_with_analytics_and_metrics(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'name' => 'Philosophy',
            'slug' => 'philosophy',
            'is_active' => true,
        ]);

        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Meditations',
            'slug' => 'meditations',
            'author' => 'Marcus Aurelius',
            'price' => 18.00,
            'stock' => 3, // Low stock (≤ 5)
            'is_featured' => true,
        ]);

        $order = Order::create([
            'user_id' => $this->regularUser->id,
            'status' => 'paid',
            'total_amount' => 18.00,
            'stripe_payment_intent_id' => 'pi_admin_test_123',
        ]);

        $order->items()->create([
            'book_id' => $book->id,
            'quantity' => 1,
            'price' => 18.00,
        ]);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Store Analytics & Operations', false);
        $response->assertSee('Gross Revenue');
        $response->assertSee('18.00');
        $response->assertSee('Sales & Demand Trajectory', false);
        $response->assertSee('Genre Distribution');
        $response->assertSee('Recent Orders Stream');
        $response->assertSee('Inventory Health');
        $response->assertSee('Meditations');
        $response->assertSee('3 left');
    }
}
