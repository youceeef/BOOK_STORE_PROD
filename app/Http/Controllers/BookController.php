<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display a listing of books with search, category filtering, and sorting.
     */
    public function index(Request $request): View
    {
        $query = Book::query()
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        $searchTerm = $request->input('search');
        $selectedCategory = $request->input('category');
        $sort = $request->input('sort', 'newest');
        $inStockOnly = $request->boolean('in_stock');

        // Search Filter
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('isbn', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Category Filter
        if ($selectedCategory) {
            $query->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory);
            });
        }

        // In-stock Filter
        if ($inStockOnly) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title_asc'  => $query->orderBy('title', 'asc'),
            default      => $query->latest(),
        };

        $books = $query->paginate(12)->withQueryString();

        $categories = cache()->remember('active_categories', 3600, fn() =>
            Category::where('active', 1)->withCount('books')->orderBy('name')->get()
        );

        return view('books.index', compact(
            'books',
            'categories',
            'searchTerm',
            'selectedCategory',
            'sort',
            'inStockOnly'
        ));
    }

    /**
     * Display the specified book with related recommendations and verified reader reviews.
     */
    public function show(Book $book): View
    {
        $book->load(['category', 'reviews.user']);

        $userReview = auth()->check() ? $book->reviews->where('user_id', auth()->id())->first() : null;
        $isVerifiedBuyer = auth()->check() ? auth()->user()->hasPurchased($book) : false;
        $ratingBreakdown = $book->ratingBreakdown();
        $averageRating = $book->averageRating();
        $reviewsCount = $book->reviewsCount();

        $relatedBooks = Book::with('category')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        return view('books.show', compact(
            'book',
            'relatedBooks',
            'userReview',
            'isVerifiedBuyer',
            'ratingBreakdown',
            'averageRating',
            'reviewsCount'
        ));
    }
}
