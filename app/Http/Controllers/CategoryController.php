<?php

namespace App\Http\Controllers; // Correct namespace for public controller

use App\Models\Category; // Import Category model
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display the specified category and its associated books.
     *
     * @param Category $category // Automatically resolved by slug via route model binding
     * @return View
     */
    public function show(Category $category): View
    {
        abort_unless($category->active, 404);

        // Eager load category relationship for books (optional, but good practice)
        // Paginate the books belonging to this category
        $books = $category->books()
            ->with('category')
            ->latest()
            ->paginate(12);

        // Pass the category and its paginated books to the view
        return view('categories.show', compact('category', 'books'));
    }
}
