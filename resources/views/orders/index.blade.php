<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
            Order History
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($orders->isEmpty())
            <div class="max-w-xl mx-auto text-center py-20 px-6 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gold-50 text-gold-500 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="font-serif text-xl font-bold text-gray-900 mb-2">No Past Orders Found</h2>
                <p class="text-sm text-gray-500 mb-6">
                    You have not placed any orders yet. Once you complete a purchase, your tracked orders and invoices will appear here.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-6 py-3 rounded-2xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
                    Browse Books
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-gold-300 transition duration-150">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <span class="font-mono text-sm font-bold text-gray-900">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @php
                                    $statusClasses = match($order->status) {
                                        'paid', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'shipped'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'pending'           => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'failed'            => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default             => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-500">
                                Placed on <span class="font-medium text-gray-700">{{ $order->created_at->format('F j, Y') }}</span>
                                • {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between md:justify-end space-x-6 border-t md:border-t-0 pt-4 md:pt-0 border-gray-100">
                            <div class="text-left md:text-right">
                                <span class="text-xs text-gray-400 block">Total Billed</span>
                                <span class="font-serif text-xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            <a href="{{ route('orders.show', $order->id) }}"
                                class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-[#1a1a2e] hover:text-white border border-gray-200 transition duration-150">
                                View Order Details →
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
