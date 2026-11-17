<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
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
}
