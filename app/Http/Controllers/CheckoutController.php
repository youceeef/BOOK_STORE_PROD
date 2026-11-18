<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart; // Use Cart facade
use Stripe\Stripe; // Stripe PHP library (Cashier uses it internally)
use Laravel\Cashier\Exceptions\IncompletePayment; // Cashier exception for SCA
use App\Models\Order; // Eloquent models
use App\Models\Book;
use App\Models\Coupon; // Import Coupon model
use App\Services\StockReservationService; // Stock reservation and concurrency service
use Exception; // Base PHP exception
use Illuminate\Support\Facades\Log; // Logging facade
use Illuminate\Support\Facades\DB; // Database facade (optional, if needed elsewhere)
use Illuminate\Support\Str;
use Illuminate\View\View; // For return type hinting
use Illuminate\Http\RedirectResponse; // For return type hinting
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     * Calculates totals including potential coupon discount for display.
     * Creates a Stripe SetupIntent.
     */
    public function index(): View | RedirectResponse // Added RedirectResponse type hint
    {
        $user = Auth::user();
        $cartItems = Cart::getContent();

        // Redirect if cart is empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Concurrency Safety: Reserve inventory for 15 minutes with pessimistic row locking
        $reservation = StockReservationService::reserveCart($user, $cartItems, session()->getId(), 15);
        if (!$reservation['success']) {
            return redirect()->route('cart.index')->with('error', $reservation['error']);
        }

        $cartSubtotal = Cart::getSubTotal(false); // Use subtotal before discount
        $discount = 0;
        $finalTotal = $cartSubtotal; // Start with subtotal
        $appliedCouponCode = null; // For display

        // Calculate potential discount for display purposes on the checkout page
        if (session()->has('coupon')) {
            $couponSessionData = session('coupon');
            $couponModel = Coupon::where('code', $couponSessionData['code'])->first();

            // Perform a basic validation check before displaying discount
            if ($couponModel && $couponModel->isValid() && (!$couponModel->min_amount || $cartSubtotal >= $couponModel->min_amount)) {
                $discount = $couponModel->calculateDiscount($cartSubtotal);
                $finalTotal = max(0, $cartSubtotal - $discount); // Ensure total isn't negative
                $appliedCouponCode = $couponModel->code;
                Log::info("Checkout Index: Displaying discount {$discount} for coupon {$appliedCouponCode}. Subtotal: {$cartSubtotal}, Final: {$finalTotal}");
            } else {
                // If coupon in session is invalid for current cart state, remove it
                Log::warning("Checkout Index: Removing invalid session coupon '{$couponSessionData['code']}'.");
                session()->forget('coupon');
                // No need to redirect here, just won't show discount
            }
        }

        // Check if Stripe credentials are configured
        if (empty(config('cashier.secret')) || empty(config('cashier.key'))) {
            Log::warning("Checkout Index: Stripe credentials missing (STRIPE_KEY or STRIPE_SECRET).");
            return redirect()->route('cart.index')->with('error', 'Stripe payment gateway is not yet configured. Please set STRIPE_KEY and STRIPE_SECRET in your environment.');
        }

        // Ensure user is Stripe customer & create SetupIntent
        try {
            $user->createOrGetStripeCustomer();
            $intent = $user->createSetupIntent();
        } catch (\Throwable $e) {
            Log::error("Stripe Setup Intent creation failed for user {$user->id}: " . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Stripe error: ' . $e->getMessage());
        }

        // Pass calculated totals, intent secret, etc., to the view
        return view('checkout.index', [
            'intentClientSecret' => $intent->client_secret,
            'cartTotal' => $finalTotal, // Pass the potentially discounted total
            'cartSubtotal' => $cartSubtotal,
            'discount' => $discount,
            'appliedCouponCode' => $appliedCouponCode, // Pass coupon code if applied
            'cartItems' => $cartItems,
            'stripeKey' => config('cashier.key')
        ]);
    }

    /**
     * Process the payment after Stripe confirmation on the frontend.
     * Atomically locks stock, validates coupon, builds pending order, and charges Stripe with idempotency.
     */
    public function process(CheckoutRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'] ?? null;

        $cartItemsCollection = Cart::getContent();
        if ($cartItemsCollection->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // --- Atomic Pre-Payment Lock & Pending Order Creation ---
        try {
            $orderData = DB::transaction(function () use ($user, $validated, $cartItemsCollection) {
                $subtotal = 0;
                $lineItems = [];

                // 1. Verify stock & prices under exclusive row lock
                foreach ($cartItemsCollection as $cartItem) {
                    $book = Book::where('id', $cartItem->id)->lockForUpdate()->first();
                    if (!$book || $book->stock < $cartItem->quantity) {
                        $available = $book ? $book->stock : 0;
                        throw new Exception($available === 0
                            ? "Sorry, '{$cartItem->name}' is now out of stock."
                            : "Only {$available} copies of '{$cartItem->name}' remain in stock.");
                    }

                    $price = $book->price;
                    $lineSubtotal = round($price * $cartItem->quantity, 2);
                    $subtotal += $lineSubtotal;

                    $lineItems[] = [
                        'book_id' => $book->id,
                        'title_snapshot' => $book->title,
                        'author_snapshot' => $book->author,
                        'quantity' => $cartItem->quantity,
                        'price' => $price,
                    ];
                }

                // 2. Atomically validate coupon under lock
                $discount = 0;
                $couponCode = null;
                if (session()->has('coupon')) {
                    $couponSession = session('coupon');
                    $coupon = Coupon::where('code', $couponSession['code'])->lockForUpdate()->first();
                    if (!$coupon || !$coupon->isValid()) {
                        session()->forget('coupon');
                        throw new Exception('The applied coupon is no longer valid or has reached its usage limit.');
                    }
                    if ($coupon->min_amount && $subtotal < $coupon->min_amount) {
                        session()->forget('coupon');
                        throw new Exception("Cart total does not meet the minimum amount (\${$coupon->min_amount}) for coupon {$coupon->code}.");
                    }
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                }

                $finalTotal = max(0, round($subtotal - $discount, 2));

                $shippingAddress = json_encode([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'city' => $validated['city'],
                    'postal_code' => $validated['postal_code'],
                ]);

                // 3. Create durable pending order
                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_name_snapshot' => $user->name,
                    'customer_email_snapshot' => $user->email,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'fulfillment_status' => 'unfulfilled',
                    'total_amount' => $finalTotal,
                    'shipping_address' => $shippingAddress,
                    'billing_address' => $shippingAddress,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discount,
                ]);

                foreach ($lineItems as $item) {
                    $order->items()->create($item);
                }

                return [
                    'order' => $order,
                    'finalTotal' => $finalTotal,
                    'amountInCents' => round($finalTotal * 100),
                ];
            });
        } catch (\Throwable $preEx) {
            return redirect()->route('cart.index')->with('error', $preEx->getMessage());
        }

        $order = $orderData['order'];
        $amountInCents = $orderData['amountInCents'];

        // Free order support (e.g. 100% discount coupon)
        if ($amountInCents <= 0) {
            $this->completeOrderPayment($order, 'free_' . Str::random(20));
            Cart::clear();
            session()->forget('coupon');
            return redirect()->route('checkout.success')->with('success', 'Order confirmed! Your order has been placed.');
        }

        if (empty($paymentMethod) || !str_starts_with($paymentMethod, 'pm_')) {
            $order->update(['payment_status' => 'failed', 'status' => 'failed']);
            return redirect()->route('checkout.index')->with('error', 'A valid payment method is required to complete your purchase.');
        }

        // --- Initiate Charge with Idempotency Key & Order Reference ---
        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);

            $stripeMetadata = [
                'order_id' => (string) $order->id,
                'user_id' => (string) $user->id,
            ];

            $charge = $user->charge($amountInCents, $user->defaultPaymentMethod()->id, [
                'return_url' => route('checkout.return', ['order' => $order->id]),
                'description' => "Order #{$order->id} for {$user->email}",
                'metadata' => $stripeMetadata,
                'confirm' => true,
                'idempotency_key' => "order_charge_{$order->id}",
            ]);

            $paymentIntentId = $charge->id ?? ('pi_' . Str::random(24));
            $this->completeOrderPayment($order, $paymentIntentId);

            Cart::clear();
            session()->forget('coupon');

            return redirect()->route('checkout.success')->with('success', 'Payment successful! Your order has been placed.');
        } catch (IncompletePayment $exception) {
            $order->update(['stripe_payment_intent_id' => $exception->payment->id]);
            Log::warning("Checkout Process: Incomplete Payment for user {$user->id} - Requires SCA. Order ID: {$order->id}");
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('checkout.return', ['order' => $order->id])]
            );
        } catch (\Throwable $e) {
            $order->update(['payment_status' => 'failed', 'status' => 'failed']);
            Log::error("Checkout Process Error for Order #{$order->id}: " . $e->getMessage());

            $errorMessage = 'An error occurred during payment processing. Please try again.';
            if ($e instanceof \Stripe\Exception\CardException) {
                $errorMessage = $e->getError()->message ?? $errorMessage;
            } elseif (config('app.debug')) {
                $errorMessage .= ' (' . $e->getMessage() . ')';
            }

            return redirect()->route('checkout.index')->with('error', $errorMessage);
        }
    }

    /**
     * GET-safe return endpoint for 3-D Secure (SCA) payments.
     */
    public function return(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            Cart::clear();
            session()->forget('coupon');
            return redirect()->route('checkout.success')->with('success', 'Payment confirmed! Your order has been placed.');
        }

        // Verify PaymentIntent status with Stripe
        if ($order->stripe_payment_intent_id) {
            try {
                $payment = Auth::user()->findPaymentIntent($order->stripe_payment_intent_id);
                if ($payment && $payment->status === 'succeeded') {
                    $this->completeOrderPayment($order, $order->stripe_payment_intent_id);
                    Cart::clear();
                    session()->forget('coupon');
                    return redirect()->route('checkout.success')->with('success', 'Payment confirmed! Your order has been placed.');
                }
            } catch (\Throwable $e) {
                Log::error("3-D Secure Return PaymentIntent check failed for Order #{$order->id}: " . $e->getMessage());
            }
        }

        return redirect()->route('checkout.index')->with('error', 'Payment verification was not completed. Please try again.');
    }

    /**
     * Atomically transition a pending order to paid, claim stock and coupon, and send confirmation email.
     */
    public function completeOrderPayment(Order $order, string $paymentIntentId): void
    {
        DB::transaction(function () use ($order, $paymentIntentId) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$lockedOrder || $lockedOrder->payment_status === 'paid') {
                return;
            }

            $lockedOrder->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'stripe_payment_intent_id' => $paymentIntentId,
            ]);

            // Decrement inventory stock with exclusive row lock
            foreach ($lockedOrder->items as $item) {
                $book = Book::where('id', $item->book_id)->lockForUpdate()->first();
                if ($book && $book->stock > 0) {
                    $book->decrement('stock', min($book->stock, $item->quantity));
                }
            }

            // Increment coupon usage
            if ($lockedOrder->coupon_code) {
                $coupon = Coupon::where('code', $lockedOrder->coupon_code)->lockForUpdate()->first();
                if ($coupon) {
                    $coupon->increment('usage_count');
                }
            }

            // Release temporary stock holds
            StockReservationService::releaseHolds($lockedOrder->user_id, session()->getId());
        });

        // Dispatch confirmation email exactly once
        $order->refresh();
        if (!$order->confirmation_sent_at) {
            try {
                $recipient = $order->user?->email;
                if ($recipient) {
                    \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\OrderConfirmation($order));
                    $order->update(['confirmation_sent_at' => now()]);
                }
            } catch (\Throwable $mailEx) {
                Log::warning("Order Confirmation email dispatch failed for Order #{$order->id}: " . $mailEx->getMessage());
            }
        }
    }

    /**
     * Display the success page after payment initiation.
     */
    public function success(): View | RedirectResponse
    {
        // Prevent direct access without successful redirection
        if (!session('success')) {
            return redirect()->route('home');
        }
        return view('checkout.success');
    }

    /**
     * Display the cancel page (Optional).
     */
    public function cancel(): View
    {
        if (Auth::check()) {
            StockReservationService::releaseHolds(Auth::id(), session()->getId());
        }
        return view('checkout.cancel');
    }
}
