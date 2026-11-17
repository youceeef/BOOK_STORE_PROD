<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Book;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with real stats.
     */
    public function index(): View
    {
        $bookCount  = Book::count();
        $orderCount = Order::count();
        $userCount  = User::count();
        $revenue    = Order::where('status', 'paid')->sum('total_amount');

        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        $lowStockBooks = Book::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'bookCount',
            'orderCount',
            'userCount',
            'revenue',
            'recentOrders',
            'lowStockBooks'
        ));
    }
}
