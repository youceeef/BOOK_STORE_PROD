<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider text-gold-600 block mb-0.5">Order Invoice</span>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
                    Order #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
            </div>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">
                ← Back to Order History
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- Header Overview Strip --}}
            <div class="p-6 sm:p-8 bg-[#faf9f7] border-b border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block mb-1">Order Date</span>
                    <span class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Payment Status</span>
                    @php
                        $statusClasses = match($order->status) {
                            'paid', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'shipped'           => 'bg-blue-50 text-blue-700 border-blue-200',
                            'pending'           => 'bg-amber-50 text-amber-700 border-amber-200',
                            'failed'            => 'bg-rose-50 text-rose-700 border-rose-200',
                            default             => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-block px-2 py-0.5 rounded-full font-semibold border {{ $statusClasses }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Total Paid</span>
                    <span class="font-serif text-base font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Transaction Ref</span>
                    <span class="font-mono text-[11px] text-gray-600 truncate block" title="{{ $order->stripe_payment_intent_id }}">
                        {{ $order->stripe_payment_intent_id ? Str::limit($order->stripe_payment_intent_id, 12) : 'Offline/N/A' }}
                    </span>
                </div>
            </div>

            {{-- Items List --}}
            <div class="p-6 sm:p-8 divide-y divide-gray-100">
                <h2 class="font-serif text-lg font-bold text-gray-900 pb-4">Volumes Purchased</h2>

                @foreach ($order->items as $item)
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-20 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-100 shadow-sm">
                                <img src="{{ asset($item->book?->cover_image ?: 'images/covers/default.png') }}"
                                    alt="{{ $item->book?->title ?? 'Book' }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-sm sm:text-base text-gray-900">
                                    {{ $item->book?->title ?? 'Title no longer listed' }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">by {{ $item->book?->author ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>

                        <span class="font-serif font-bold text-base text-gray-900">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Final Accounting Footer --}}
            <div class="p-6 sm:p-8 bg-[#faf9f7] border-t border-gray-100">
                <div class="max-w-xs ml-auto space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Items Subtotal</span>
                        <span class="font-medium text-gray-900">${{ number_format($order->total_amount + ($order->discount_amount ?? 0), 2) }}</span>
                    </div>

                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600 font-medium">
                            <span>Discount ({{ $order->coupon_code }})</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-gray-600">
                        <span>Tracked Shipping</span>
                        <span class="text-emerald-600 font-medium">Free</span>
                    </div>

                    <div class="border-t border-gray-200 pt-3 flex justify-between items-baseline">
                        <span class="font-serif text-base font-bold text-gray-900">Final Total</span>
                        <span class="font-serif text-2xl font-bold text-gray-900">
                            ${{ number_format($order->total_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
