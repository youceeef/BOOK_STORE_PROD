<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandPaletteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Book $book1;
    protected Book $book2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Classic Fiction',
            'slug' => 'classic-fiction',
            'is_active' => true,
        ]);

        $this->book1 = Book::create([
            'category_id' => $this->category->id,
            'title' => 'The Great Gatsby',
            'slug' => 'the-great-gatsby',
            'author' => 'F. Scott Fitzgerald',
            'price' => 19.99,
            'stock' => 10,
            'isbn' => '978-0743273565',
        ]);

        $this->book2 = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Tender Is the Night',
            'slug' => 'tender-is-the-night',
            'author' => 'F. Scott Fitzgerald',
            'price' => 16.50,
            'stock' => 5,
            'isbn' => '978-0684801544',
        ]);
    }

    public function test_palette_search_returns_matching_books_by_title(): void
    {
        $response = $this->getJson(route('api.search.palette', ['q' => 'Gatsby']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'price' => '$19.99',
        ]);
    }

    public function test_palette_search_returns_matching_books_by_author(): void
    {
        $response = $this->getJson(route('api.search.palette', ['q' => 'Fitzgerald']));

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    public function test_palette_search_returns_empty_array_on_blank_query(): void
    {
        $response = $this->getJson(route('api.search.palette', ['q' => '']));

        $response->assertOk();
        $response->assertExactJson([]);
    }

    public function test_command_palette_is_rendered_in_storefront_layout(): void
    {
        $response = $this->get(route('books.index'));

        $response->assertOk();
        $response->assertSee('commandPalette()', false);
        $response->assertSee('⌘K');
        $response->assertSee('open-command-palette', false);
    }
}
