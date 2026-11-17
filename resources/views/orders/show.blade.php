<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 block mb-0.5">Order Invoice</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('orders.invoice', $order) }}"
                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Download PDF Invoice</span>
                </a>
                <a href="{{ route('orders.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">
                    ← Back to Orders
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            
            {{-- Header Overview Strip --}}
            <div class="p-5 bg-zinc-50 dark:bg-zinc-900/60 border-b border-zinc-200 dark:border-zinc-800 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-zinc-400 block mb-1">Order Date</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block mb-1">Payment Status</span>
                    @php
                        $statusClasses = match($order->status) {
                            'paid', 'completed' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                            'shipped'           => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                            'pending'           => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                            'failed'            => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                            default             => 'bg-zinc-50 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800',
                        };
                    @endphp
                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium border {{ $statusClasses }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div>
                    <span class="text-zinc-400 block mb-1">Total Paid</span>
                    <span class="font-serif text-base font-bold text-zinc-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-zinc-400 block mb-1">Transaction Ref</span>
                    <span class="font-mono text-[11px] text-zinc-600 dark:text-zinc-400 truncate block" title="{{ $order->stripe_payment_intent_id }}">
                        {{ $order->stripe_payment_intent_id ? Str::limit($order->stripe_payment_intent_id, 14) : 'Offline/N/A' }}
                    </span>
                </div>
            </div>

            {{-- Items List --}}
            <div class="p-5 sm:p-6 divide-y divide-zinc-100 dark:divide-zinc-800">
                <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white pb-3">Volumes Purchased</h2>

                @foreach ($order->items as $item)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                            <div class="w-12 h-16 rounded overflow-hidden bg-zinc-100 dark:bg-zinc-900 flex-shrink-0 border border-zinc-200 dark:border-zinc-800 p-1 flex items-center justify-center">
                                <img src="{{ asset($item->book?->cover_image ?: 'images/covers/default.png') }}"
                                    alt="{{ $item->book?->title ?? 'Book' }}"
                                    class="max-h-full max-w-full object-contain">
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ $item->book?->title ?? 'Title no longer listed' }}
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">by {{ $item->book?->author ?? 'Unknown' }}</p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>

                        <span class="font-serif font-bold text-sm text-zinc-900 dark:text-white">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Accounting Footer --}}
            <div class="p-5 sm:p-6 bg-zinc-50 dark:bg-zinc-900/60 border-t border-zinc-200 dark:border-zinc-800">
                <div class="max-w-xs ml-auto rtl:ml-0 rtl:mr-auto space-y-2 text-xs">
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Items Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-white">${{ number_format($order->total_amount + ($order->discount_amount ?? 0), 2) }}</span>
                    </div>

                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-zinc-900 dark:text-white font-medium">
                            <span>Discount ({{ $order->coupon_code }})</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Tracked Shipping</span>
                        <span class="font-medium text-zinc-900 dark:text-white">Free</span>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2.5 flex justify-between items-baseline">
                        <span class="font-serif text-sm font-bold text-zinc-900 dark:text-white">Final Total</span>
                        <span class="font-serif text-xl font-bold text-zinc-900 dark:text-white">
                            ${{ number_format($order->total_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>