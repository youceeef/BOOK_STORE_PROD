<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartInteractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed a category and book
        $category = Category::create([
            'name' => 'Classic Literature',
            'slug' => 'classic-literature',
            'is_active' => true
        ]);

        Book::create([
            'category_id' => $category->id,
            'title' => 'The Great Gatsby',
            'slug' => 'the-great-gatsby',
            'author' => 'F. Scott Fitzgerald',
            'price' => 19.99,
            'stock' => 10,
            'is_featured' => true
        ]);
    }

    public function test_can_add_book_to_cart_via_ajax_with_toast_payload(): void
    {
        $book = Book::first();

        $response = $this->postJson(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 1
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'title' => 'Added to Bag',
                'cartCount' => 1
            ]);
    }

    public function test_can_remove_book_from_cart_via_ajax_with_toast_payload(): void
    {
        $book = Book::first();

        // Add to cart first
        $this->postJson(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 2
        ]);

        // Remove from cart
        $response = $this->deleteJson(route('cart.destroy', $book->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cartCount' => 0,
                'isEmpty' => true
            ]);
    }

    public function test_can_clear_cart_via_ajax(): void
    {
        $book = Book::first();

        $this->postJson(route('cart.store'), [
            'book_id' => $book->id,
            'quantity' => 1
        ]);

        $response = $this->postJson(route('cart.clear'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cartCount' => 0,
                'isEmpty' => true
            ]);
    }
}
