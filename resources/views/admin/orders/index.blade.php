<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-0.5">Sales & Fulfillment</p>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Customer Orders & Invoices</h1>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Search and Filter Controls --}}
        <form method="GET" action="{{ route('admin.orders.index') }}"
            class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label for="order-search" class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Search Orders</label>
                <input type="text" id="order-search" name="search" value="{{ request('search') }}"
                    placeholder="Order ID, customer, email..."
                    class="w-full text-xs rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            </div>

            <div>
                <label for="filter-payment" class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Payment Status</label>
                <select id="filter-payment" name="payment_status"
                    class="w-full text-xs rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
                    <option value="">All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label for="filter-fulfillment" class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Fulfillment Status</label>
                <select id="filter-fulfillment" name="fulfillment_status"
                    class="w-full text-xs rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
                    <option value="">All Fulfillments</option>
                    <option value="unfulfilled" {{ request('fulfillment_status') === 'unfulfilled' ? 'selected' : '' }}>Unfulfilled</option>
                    <option value="shipped" {{ request('fulfillment_status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('fulfillment_status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('fulfillment_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label for="filter-date-from" class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">From Date</label>
                <input type="date" id="filter-date-from" name="date_from" value="{{ request('date_from') }}"
                    class="w-full text-xs rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 py-1.5 px-3 bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 rounded-md font-medium text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'payment_status', 'fulfillment_status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index') }}"
                        class="py-1.5 px-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-md text-xs hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400 font-medium text-xs">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Order ID</th>
                            <th class="px-5 py-3.5 font-semibold">Customer</th>
                            <th class="px-5 py-3.5 font-semibold">Date</th>
                            <th class="px-5 py-3.5 font-semibold">Payment</th>
                            <th class="px-5 py-3.5 font-semibold">Fulfillment</th>
                            <th class="px-5 py-3.5 font-semibold">Items</th>
                            <th class="px-5 py-3.5 font-semibold">Total</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs font-semibold tabular-nums text-zinc-900 dark:text-white">
                                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $order->customer_name ?? $order->user?->name ?? 'Guest Buyer' }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $order->customer_email ?? $order->user?->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 text-xs">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                                {{-- Distinct Payment Badge --}}
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @php
                                        $payClasses = match($order->payment_status) {
                                            'paid'      => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                            'pending'   => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                            'refunded'  => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                            'failed'    => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                            default     => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $payClasses }}">
                                        {{ ucfirst($order->payment_status ?? 'pending') }}
                                    </span>
                                </td>
                                {{-- Distinct Fulfillment Badge --}}
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @php
                                        $fulClasses = match($order->fulfillment_status) {
                                            'delivered'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                            'shipped'     => 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 border-zinc-900 dark:border-white font-semibold',
                                            'unfulfilled' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                            'cancelled'   => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                            default       => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $fulClasses }}">
                                        {{ ucfirst($order->fulfillment_status ?? 'unfulfilled') }}
                                    </span>
                                    @if ($order->tracking_number)
                                        <span class="block text-[10px] font-mono text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            {{ $order->tracking_number }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                    {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs space-x-1.5 rtl:space-x-reverse">
                                    <a href="{{ route('admin.orders.invoice', $order) }}"
                                        title="Download Tax Invoice"
                                        class="inline-flex items-center space-x-1 px-2 py-1 rounded text-[11px] font-medium text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition">
                                        <svg class="w-3 h-3 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>PDF</span>
                                    </a>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-xs text-zinc-400">
                                    No customer orders placed yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
