<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller;
use App\Models\Order; // Import Order model
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(): View
    {
        // Fetch all orders, newest first, with user relationship and item count
        $orders = Order::with('user') // Eager load the user who placed the order
            ->withCount('items') // Eager load the count of items
            ->latest() // Order by creation date descending
            ->paginate(20); // Show more per page for admin

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): View // Route model binding using ID
    {
        // Eager load relationships needed for the detail view
        $order->load([
            'user',          // Load the user details
            'items',         // Load the order items collection
            'items.book'     // For each item, load the associated book
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the fulfillment status and logistics tracking for the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
            'carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery_at' => 'nullable|date',
            'fulfillment_notes' => 'nullable|string|max:1000',
        ]);

        // Auto-assign sensible carrier & tracking number if admin sets to 'shipped' without entering one
        if ($validated['status'] === 'shipped') {
            if (empty($validated['carrier'])) {
                $validated['carrier'] = $order->carrier ?: 'DHL Express';
            }
            if (empty($validated['tracking_number'])) {
                $validated['tracking_number'] = $order->tracking_number ?: ('TRK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)));
            }
            if (empty($validated['estimated_delivery_at']) && empty($order->estimated_delivery_at)) {
                $validated['estimated_delivery_at'] = now()->addDays(3)->toDateString();
            }
            if (!$order->shipped_at) {
                $validated['shipped_at'] = now();
            }
        }

        if ($validated['status'] === 'delivered' && !$order->delivered_at) {
            $validated['delivered_at'] = now();
        }

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order #ORD-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . " fulfillment updated to " . ucfirst($order->status) . ".");
    }
}
