<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller;
use App\Models\Order; // Import Order model
use App\Models\Book;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with('user')->withCount('items');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
                $q->orWhere('customer_name_snapshot', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email_snapshot', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($fulfillmentStatus = $request->input('fulfillment_status')) {
            $query->where('fulfillment_status', $fulfillmentStatus);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

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

        // Handle Order Cancellation & Inventory/Payment Compensation
        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            DB::transaction(function () use ($order, &$validated) {
                // 1. If order was paid via Stripe, process refund
                if ($order->payment_status === 'paid' && $order->stripe_payment_intent_id) {
                    try {
                        if ($order->user && method_exists($order->user, 'refund')) {
                            $order->user->refund($order->stripe_payment_intent_id);
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Stripe refund failed for Order #{$order->id}: " . $e->getMessage());
                    }
                    $validated['payment_status'] = 'refunded';
                    $validated['refunded_amount'] = $order->total_amount;
                }

                // 2. Restore book inventory
                foreach ($order->items as $item) {
                    $book = Book::where('id', $item->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $book->increment('stock', $item->quantity);
                    }
                }

                // 3. Compensate coupon usage if used
                if ($order->coupon_code) {
                    $coupon = Coupon::where('code', $order->coupon_code)->lockForUpdate()->first();
                    if ($coupon && $coupon->usage_count > 0) {
                        $coupon->decrement('usage_count');
                    }
                }

                $validated['cancelled_at'] = now();
                $validated['fulfillment_status'] = 'cancelled';
            });
        }

        $validated['fulfillment_status'] = in_array($validated['status'], ['processing', 'shipped', 'delivered', 'cancelled'])
            ? $validated['status']
            : ($order->fulfillment_status ?: 'unfulfilled');

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order #ORD-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . " fulfillment updated to " . ucfirst($order->status) . ".");
    }
}
