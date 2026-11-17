<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Book $book;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->category = Category::create([
            'name' => 'Classic Literature',
            'slug' => 'classic-literature',
            'is_active' => true,
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'The Great Gatsby',
            'slug' => 'the-great-gatsby',
            'author' => 'F. Scott Fitzgerald',
            'price' => 19.99,
            'stock' => 10,
            'is_featured' => true,
        ]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->post(route('reviews.store', $this->book->slug), [
            'rating' => 5,
            'title' => 'Amazing book',
            'comment' => 'Truly a masterpiece of twentieth-century American fiction.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_authenticated_user_can_submit_unverified_review(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('reviews.store', $this->book->slug), [
            'rating' => 5,
            'title' => 'Masterful prose',
            'comment' => 'One of the best books I have ever read in this format.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'title' => 'Masterful prose',
            'is_verified_purchase' => false,
        ]);
    }

    public function test_verified_buyer_flag_is_automatically_set_when_user_purchased_book(): void
    {
        $this->actingAs($this->user);

        // Create completed order containing this book
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'paid',
            'total_amount' => 19.99,
            'stripe_payment_intent_id' => 'pi_test_verified_123',
        ]);

        $order->items()->create([
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 19.99,
        ]);

        $response = $this->post(route('reviews.store', $this->book->slug), [
            'rating' => 5,
            'title' => 'Exquisite edition',
            'comment' => 'Arrived quickly with tracked packaging and gorgeous dust jacket.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'is_verified_purchase' => true,
        ]);
    }

    public function test_review_validation_rules(): void
    {
        $this->actingAs($this->user);

        // Invalid rating (0 or 6) and too short comment
        $response = $this->post(route('reviews.store', $this->book->slug), [
            'rating' => 6,
            'comment' => 'bad',
        ]);
        $response->assertSessionHasErrors(['rating', 'comment']);
    }

    public function test_submitting_again_updates_existing_review_without_duplicate(): void
    {
        $this->actingAs($this->user);

        Review::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'title' => 'Good initial read',
            'comment' => 'Enjoyed the first few chapters tremendously.',
        ]);

        $response = $this->post(route('reviews.store', $this->book->slug), [
            'rating' => 5,
            'title' => 'Updated: Absolute masterpiece',
            'comment' => 'Finished the book and it deserves the full five stars.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'title' => 'Updated: Absolute masterpiece',
        ]);
    }

    public function test_user_can_vote_review_helpful(): void
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'title' => 'Incredible',
            'comment' => 'Superb binding and typesetting.',
            'helpful_count' => 3,
        ]);

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->post(route('reviews.helpful', $review->id));
        $response->assertRedirect();

        $this->assertEquals(4, $review->fresh()->helpful_count);
    }

    public function test_book_show_renders_reviews_and_ratings(): void
    {
        Review::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'title' => 'Flawless literary work',
            'comment' => 'The definitive edition to own for literature lovers.',
            'is_verified_purchase' => true,
        ]);

        $response = $this->get(route('books.show', $this->book->slug));

        $response->assertOk();
        $response->assertSee('Reader Reviews & Critique', false);
        $response->assertSee('Flawless literary work');
        $response->assertSee('Verified Buyer');
        $response->assertSee('5.0');
    }
}
