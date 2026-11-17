<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Sales & Fulfillment</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Customer Orders & Invoices</h1>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Order ID</th>
                            <th class="px-5 py-3.5 font-semibold">Customer</th>
                            <th class="px-5 py-3.5 font-semibold">Date</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                            <th class="px-5 py-3.5 font-semibold">Items</th>
                            <th class="px-5 py-3.5 font-semibold">Total</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-bold text-zinc-900 dark:text-white">
                                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $order->user?->name ?? 'Guest Buyer' }}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono">{{ $order->user?->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 text-xs">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
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
                                        Review →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-xs text-zinc-400">
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
