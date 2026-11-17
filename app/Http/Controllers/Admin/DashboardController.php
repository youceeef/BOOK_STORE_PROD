<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Book;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with real stats, time-series velocity, and category analytics.
     */
    public function index(): View
    {
        $bookCount  = Book::count();
        $orderCount = Order::count();
        $userCount  = User::count();
        $revenue    = (float) Order::where('status', 'paid')->sum('total_amount');

        $paidOrderCount = Order::where('status', 'paid')->count();
        $avgOrderValue = $paidOrderCount > 0 ? $revenue / $paidOrderCount : 0;
        $outOfStockCount = Book::where('stock', 0)->count();

        // 14-day velocity time series (last 14 days)
        $chartLabels = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('M j');

            $chartLabels[] = $label;

            $dayOrders = Order::whereDate('created_at', $dateStr)->get();
            $dayRevenue = $dayOrders->where('status', 'paid')->sum('total_amount');

            $revenueData[] = (float) number_format($dayRevenue, 2, '.', '');
            $ordersData[] = $dayOrders->count();
        }

        // Top 5 categories volume distribution
        $categoriesBreakdown = Category::withCount('books')
            ->orderByDesc('books_count')
            ->take(5)
            ->get();

        $categoryLabels = $categoriesBreakdown->pluck('name')->toArray();
        $categoryCounts = $categoriesBreakdown->pluck('books_count')->toArray();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $lowStockBooks = Book::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'bookCount',
            'orderCount',
            'userCount',
            'revenue',
            'avgOrderValue',
            'outOfStockCount',
            'chartLabels',
            'revenueData',
            'ordersData',
            'categoryLabels',
            'categoryCounts',
            'recentOrders',
            'lowStockBooks'
        ));
    }
}
