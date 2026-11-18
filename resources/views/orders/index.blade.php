<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
            {{ __('Order History') }}
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($orders->isEmpty())
            <div class="max-w-md mx-auto text-center py-16 px-6 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800">
                <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white mb-1">No Past Orders Found</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">
                    You have not placed any orders yet. Once you complete a purchase, your tracked orders and invoices will appear here.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                    {{ __('Explore Books') }}
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
                    <div class="bg-white dark:bg-[#12131a] rounded-lg p-5 border border-zinc-200 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-semibold text-zinc-900 dark:text-white">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @php
                                    $payClasses = match($order->payment_status) {
                                        'paid'      => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'pending'   => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        'refunded'  => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                        'failed'    => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        default     => 'bg-zinc-50 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800',
                                    };
                                    $fulClasses = match($order->fulfillment_status) {
                                        'delivered'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'shipped'     => 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 border-zinc-900 dark:border-white font-semibold',
                                        'unfulfilled' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                        'cancelled'   => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        default       => 'bg-zinc-50 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium border {{ $payClasses }}">
                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium border {{ $fulClasses }}">
                                    {{ ucfirst($order->fulfillment_status ?? 'unfulfilled') }}
                                </span>
                                @if ($order->tracking_number)
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        {{ $order->carrier ? $order->carrier . ':' : '' }} {{ $order->tracking_number }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $order->created_at->format('F j, Y') }} • {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between md:justify-end space-x-3 rtl:space-x-reverse border-t md:border-t-0 pt-3 md:pt-0 border-zinc-100 dark:border-zinc-800">
                            <div class="text-left md:text-right rtl:md:text-left pr-3">
                                <span class="text-[11px] text-zinc-400 block">Total</span>
                                <span class="font-serif text-lg font-bold text-zinc-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            <a href="{{ route('orders.invoice', $order) }}"
                                title="Download PDF Tax Invoice"
                                class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-[#151722] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>PDF</span>
                            </a>

                            <a href="{{ route('orders.show', $order->id) }}"
                                class="inline-flex items-center px-3.5 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 dark:bg-zinc-100 dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-white transition shadow-sm">
                                View Order
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>