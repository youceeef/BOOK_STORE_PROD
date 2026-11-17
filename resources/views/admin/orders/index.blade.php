<x-admin-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs uppercase tracking-wider text-gold-600 font-bold block mb-1">Sales & Fulfillment</span>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Manage Orders</h1>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-[#faf9f7]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-[#faf9f7]/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-gray-900">
                                #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->user?->name ?? 'Guest Buyer' }}</div>
                                <div class="text-xs text-gray-400">{{ $order->user?->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match($order->status) {
                                        'paid', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'shipped'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'pending'           => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'failed'            => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default             => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-serif font-bold text-sm text-gray-900">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition">
                                    Review Order →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                                No customer orders recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</x-admin-layout>
