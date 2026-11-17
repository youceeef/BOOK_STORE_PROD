<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderModalTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Book $bookWithSample;
    protected Book $bookWithoutSample;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Classic Literature',
            'slug' => 'classic-literature',
            'is_active' => true,
        ]);

        $this->bookWithSample = Book::create([
            'category_id' => $this->category->id,
            'title' => 'The Great Gatsby',
            'slug' => 'the-great-gatsby',
            'author' => 'F. Scott Fitzgerald',
            'price' => 19.99,
            'stock' => 10,
            'is_featured' => true,
            'sample_excerpt' => "Chapter 1\n\nIn my younger and more vulnerable years my father gave me some advice.",
        ]);

        $this->bookWithoutSample = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Book Without Sample',
            'slug' => 'book-without-sample',
            'author' => 'Anonymous',
            'price' => 15.00,
            'stock' => 5,
            'sample_excerpt' => null,
        ]);
    }

    public function test_book_model_methods(): void
    {
        $this->assertTrue($this->bookWithSample->hasSample());
        $this->assertFalse($this->bookWithoutSample->hasSample());

        $this->assertGreaterThanOrEqual(1, $this->bookWithSample->readingTime());
    }

    public function test_book_show_renders_look_inside_button_when_sample_exists(): void
    {
        $response = $this->get(route('books.show', $this->bookWithSample->slug));

        $response->assertOk();
        $response->assertSee('Look Inside (Sample Excerpt)');
        $response->assertSee('In my younger and more vulnerable years');
    }

    public function test_book_show_does_not_render_look_inside_button_when_sample_missing(): void
    {
        $response = $this->get(route('books.show', $this->bookWithoutSample->slug));

        $response->assertOk();
        $response->assertDontSee('Look Inside (Sample Excerpt)');
    }

    public function test_admin_can_update_sample_excerpt_via_crud(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->put(route('admin.books.update', $this->bookWithSample), [
            'title' => $this->bookWithSample->title,
            'category_id' => $this->category->id,
            'author' => $this->bookWithSample->author,
            'price' => 19.99,
            'stock' => 10,
            'sample_excerpt' => "Updated Chapter 1 Excerpt Content\n\nNew paragraph of literary prose.",
        ]);

        $response->assertRedirect(route('admin.books.index'));
        $this->assertEquals("Updated Chapter 1 Excerpt Content\n\nNew paragraph of literary prose.", $this->bookWithSample->fresh()->sample_excerpt);
    }
}
