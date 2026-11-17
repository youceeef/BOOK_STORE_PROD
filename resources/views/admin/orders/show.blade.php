<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Order Invoice (Admin Audit)</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
            </div>
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition self-start sm:self-auto">
                ← Back to All Orders
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl mx-auto">

        {{-- Overview Summary Strip --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 block mb-1">Placed At</span>
                <span class="font-medium text-zinc-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 block mb-1">Fulfillment Status</span>
                @php
                    $statusClasses = match($order->status) {
                        'paid', 'completed' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                        'shipped'           => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                        'pending'           => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                        'failed'            => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                        default             => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                    };
                @endphp
                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $statusClasses }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 block mb-1">Total Paid</span>
                <span class="font-serif text-base font-bold text-zinc-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 block mb-1">Stripe Payment Ref</span>
                <span class="font-mono text-[11px] text-zinc-600 dark:text-zinc-400 truncate block" title="{{ $order->stripe_payment_intent_id }}">
                    {{ $order->stripe_payment_intent_id ? Str::limit($order->stripe_payment_intent_id, 14) : 'Offline/N/A' }}
                </span>
            </div>
        </div>

        {{-- Customer & Shipping Details --}}
        @php
            $shipping = is_string($order->shipping_address) ? json_decode($order->shipping_address, true) : $order->shipping_address;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 space-y-2 text-xs">
                <h3 class="font-serif font-bold text-sm text-zinc-900 dark:text-white pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    Customer Details
                </h3>
                <div class="flex justify-between py-1">
                    <span class="text-zinc-500 dark:text-zinc-400">Account Name</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $order->user?->name ?? 'Guest Buyer' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-zinc-500 dark:text-zinc-400">Email Address</span>
                    <span class="font-mono text-zinc-900 dark:text-white">{{ $order->user?->email ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 space-y-2 text-xs">
                <h3 class="font-serif font-bold text-sm text-zinc-900 dark:text-white pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    Delivery Destination
                </h3>
                <div class="text-zinc-700 dark:text-zinc-300 space-y-1">
                    <p class="font-medium text-zinc-900 dark:text-white">{{ $shipping['name'] ?? ($order->user?->name ?? 'Recipient') }}</p>
                    <p>{{ $shipping['address'] ?? 'No street address specified' }}</p>
                    <p>{{ $shipping['city'] ?? '' }} {{ $shipping['postal_code'] ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Order Items Table --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-4 sm:px-6 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="font-serif font-bold text-sm text-zinc-900 dark:text-white">Volumes in Order</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Volume</th>
                            <th class="px-5 py-3 font-semibold">Unit Price</th>
                            <th class="px-5 py-3 font-semibold">Qty</th>
                            <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($order->items as $item)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                        <div class="w-10 h-14 rounded overflow-hidden bg-zinc-100 dark:bg-zinc-900 flex-shrink-0 border border-zinc-200 dark:border-zinc-800 p-0.5 flex items-center justify-center">
                                            <img src="{{ asset($item->book?->cover_image ?: 'images/covers/default.png') }}"
                                                alt="{{ $item->book?->title ?? 'Book' }}" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div>
                                            <span class="font-serif font-bold text-xs text-zinc-900 dark:text-white block">
                                                {{ $item->book?->title ?? 'Title no longer listed' }}
                                            </span>
                                            <span class="text-[11px] text-zinc-400">by {{ $item->book?->author ?? 'Unknown' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-zinc-600 dark:text-zinc-400">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-zinc-900 dark:text-white font-medium">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-serif font-bold text-zinc-900 dark:text-white">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-xs text-zinc-400">
                                    No items recorded for this transaction.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Accounting Breakdown Footer --}}
            <div class="p-5 sm:p-6 bg-zinc-50 dark:bg-zinc-900/60 border-t border-zinc-200 dark:border-zinc-800">
                <div class="max-w-xs ml-auto rtl:ml-0 rtl:mr-auto space-y-2 text-xs">
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Items Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-white">${{ number_format($order->total_amount + ($order->discount_amount ?? 0), 2) }}</span>
                    </div>

                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium">
                            <span>Discount ({{ $order->coupon_code }})</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Tracked Shipping</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">Free</span>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2.5 flex justify-between items-baseline">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Grand Total</span>
                        <span class="font-serif text-lg font-bold text-zinc-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
