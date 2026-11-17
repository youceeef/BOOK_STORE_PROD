<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    protected Book $book;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'is_active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $category->id,
            'title' => 'To Kill a Mockingbird',
            'slug' => 'to-kill-a-mockingbird',
            'author' => 'Harper Lee',
            'price' => 14.50,
            'stock' => 5,
            'is_featured' => true,
        ]);

        $this->user = User::factory()->create();
    }

    public function test_guest_can_toggle_wishlist_in_session(): void
    {
        // Toggle ON
        $response = $this->postJson(route('wishlist.toggle'), [
            'book_id' => $this->book->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'attached' => true,
                'wishlistCount' => 1,
            ]);

        $this->assertEquals([$this->book->id], session('guest_wishlist'));

        // Toggle OFF
        $response2 = $this->postJson(route('wishlist.toggle'), [
            'book_id' => $this->book->id,
        ]);

        $response2->assertOk()
            ->assertJson([
                'success' => true,
                'attached' => false,
                'wishlistCount' => 0,
            ]);

        $this->assertEquals([], session('guest_wishlist'));
    }

    public function test_authenticated_user_can_toggle_wishlist_in_database(): void
    {
        $this->actingAs($this->user);

        // Toggle ON
        $response = $this->postJson(route('wishlist.toggle'), [
            'book_id' => $this->book->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'attached' => true,
                'wishlistCount' => 1,
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        // Toggle OFF
        $response2 = $this->postJson(route('wishlist.toggle'), [
            'book_id' => $this->book->id,
        ]);

        $response2->assertOk()
            ->assertJson([
                'success' => true,
                'attached' => false,
                'wishlistCount' => 0,
            ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);
    }

    public function test_guest_wishlist_migrates_to_user_upon_login(): void
    {
        // Guest saves a book in session
        $this->withSession(['guest_wishlist' => [$this->book->id]]);

        // User logs in
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($this->user);

        // Verify book is migrated to database
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        // Verify guest session is cleaned up
        $this->assertNull(session('guest_wishlist'));
    }

    public function test_can_move_wishlist_item_to_shopping_bag(): void
    {
        $this->actingAs($this->user);

        // Add to wishlist
        Wishlist::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        // Move to bag via AJAX
        $response = $this->postJson(route('wishlist.move-to-bag', $this->book->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cartCount' => 1,
                'wishlistCount' => 0,
            ]);

        // Verify removed from wishlist
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);
    }

    public function test_can_remove_item_from_wishlist(): void
    {
        $this->actingAs($this->user);

        Wishlist::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        $response = $this->deleteJson(route('wishlist.destroy', $this->book->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'wishlistCount' => 0,
                'isEmpty' => true,
            ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);
    }

    public function test_wishlist_page_renders_cleanly(): void
    {
        $response = $this->get(route('wishlist.index'));
        $response->assertOk()
            ->assertSee('Saved Volumes');
    }
}
