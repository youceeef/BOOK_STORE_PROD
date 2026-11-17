<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex-1 overflow-auto focus:outline-none">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">

                {{-- Stat Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

                    {{-- Total Books --}}
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Books</h3>
                                <p class="mt-1 text-3xl font-semibold text-blue-600">{{ $bookCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.books.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Manage Books →
                            </a>
                        </div>
                    </div>

                    {{-- Total Orders --}}
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Orders</h3>
                                <p class="mt-1 text-3xl font-semibold text-green-600">{{ $orderCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.orders.index') }}" class="text-green-600 hover:text-green-800 text-sm">
                                Manage Orders →
                            </a>
                        </div>
                    </div>

                    {{-- Total Revenue --}}
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Revenue</h3>
                                <p class="mt-1 text-3xl font-semibold text-indigo-600">${{ number_format($revenue, 2) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-gray-400 text-sm">From paid orders</span>
                        </div>
                    </div>

                    {{-- Total Users --}}
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Users</h3>
                                <p class="mt-1 text-3xl font-semibold text-purple-600">{{ $userCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-gray-400 text-sm">Registered accounts</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Recent Orders --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Recent Orders</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($recentOrders as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->user->name ?? 'Guest' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusColor = match($order->status) {
                                                            'paid'      => 'bg-green-100 text-green-800',
                                                            'pending'   => 'bg-yellow-100 text-yellow-800',
                                                            'shipped'   => 'bg-blue-100 text-blue-800',
                                                            'failed'    => 'bg-red-100 text-red-800',
                                                            default     => 'bg-gray-100 text-gray-800',
                                                        };
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $order->created_at->format('Y-m-d') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                                    No orders yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Low Stock Alert --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">⚠️ Low Stock (≤5)</h2>
                            </div>
                            <div class="divide-y divide-gray-200">
                                @forelse ($lowStockBooks as $lowBook)
                                    <div class="px-6 py-3 flex items-center justify-between">
                                        <span class="text-sm text-gray-700 truncate max-w-xs">{{ $lowBook->title }}</span>
                                        <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full {{ $lowBook->stock <= 2 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $lowBook->stock }} left
                                        </span>
                                    </div>
                                @empty
                                    <div class="px-6 py-8 text-center text-sm text-gray-500">
                                        All books well stocked ✅
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Quick Links --}}
                        <div class="mt-4 bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Quick Links</h2>
                            </div>
                            <div class="p-4 space-y-2">
                                <a href="{{ route('admin.books.create') }}" class="flex items-center px-3 py-2 text-sm text-indigo-600 hover:bg-indigo-50 rounded-md transition">
                                    + Add New Book
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 rounded-md transition">
                                    + Manage Categories
                                </a>
                                <a href="{{ route('admin.coupons.index') }}" class="flex items-center px-3 py-2 text-sm text-yellow-600 hover:bg-yellow-50 rounded-md transition">
                                    + Manage Coupons
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
