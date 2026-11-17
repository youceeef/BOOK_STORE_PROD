<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <div class="flex items-center space-x-2 rtl:space-x-reverse mb-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium">Store Operations</span>
                    <span>&bull;</span>
                    <span class="tabular-nums">{{ now()->format('M d, Y') }}</span>
                </div>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Store Analytics & Operations</h1>
            </div>

            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                <a href="{{ route('admin.books.create') }}"
                    class="inline-flex items-center px-3.5 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                    <svg class="w-3.5 h-3.5 mr-1.5 rtl:mr-0 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Volume
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    All Orders
                </a>

                <a href="{{ route('admin.coupons.index') }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    Coupons
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- 1. Metric Strip (Architectural 1px Border Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Gross Revenue --}}
            <div class="bg-white dark:bg-[#12131a] p-5 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium">Gross Revenue</span>
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="font-serif text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    ${{ number_format($revenue, 2) }}
                </p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 tabular-nums">
                    Avg. Order: ${{ number_format($avgOrderValue, 2) }}
                </p>
            </div>

            {{-- Total Orders --}}
            <div class="bg-white dark:bg-[#12131a] p-5 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium">Orders Placed</span>
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="font-serif text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    {{ number_format($orderCount) }}
                </p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center">
                    <span>Synchronous & tracked</span>
                </p>
            </div>

            {{-- Catalog Titles --}}
            <div class="bg-white dark:bg-[#12131a] p-5 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium">Volumes Cataloged</span>
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="font-serif text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    {{ number_format($bookCount) }}
                </p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ count($categoryLabels) }} primary genres
                </p>
            </div>

            {{-- Inventory Alerts --}}
            <div class="bg-white dark:bg-[#12131a] p-5 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium">Inventory Health</span>
                    <svg class="w-4 h-4 {{ $lowStockBooks->count() > 0 ? 'text-amber-500' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="font-serif text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">
                    {{ $lowStockBooks->count() }}
                    <span class="text-xs font-sans font-normal text-zinc-500">low stock</span>
                </p>
                <p class="text-xs {{ $outOfStockCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                    {{ $outOfStockCount > 0 ? $outOfStockCount . ' completely out of stock' : 'No depleted items' }}
                </p>
            </div>

        </div>

        {{-- 2. Interactive Charts Section (Chart.js via Alpine) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
            x-data="{
                activeMetric: 'revenue',
                labels: {{ json_encode($chartLabels) }},
                revenueData: {{ json_encode($revenueData) }},
                ordersData: {{ json_encode($ordersData) }},
                categoryLabels: {{ json_encode($categoryLabels) }},
                categoryCounts: {{ json_encode($categoryCounts) }},
                chartInstance: null,
                donutInstance: null,

                init() {
                    this.$nextTick(() => {
                        this.renderVelocityChart();
                        this.renderDonutChart();
                        
                        // Watch for dark/light mode toggle to refresh chart palettes
                        const observer = new MutationObserver(() => {
                            this.renderVelocityChart();
                            this.renderDonutChart();
                        });
                        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                    });
                },

                renderVelocityChart() {
                    const ctx = document.getElementById('velocityChart');
                    if (!ctx || !window.Chart) return;

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                    const textColor = isDark ? '#a1a1aa' : '#71717a';
                    const isRevenue = this.activeMetric === 'revenue';

                    this.chartInstance = new window.Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: isRevenue ? 'Gross Revenue ($)' : 'Daily Orders',
                                data: isRevenue ? this.revenueData : this.ordersData,
                                borderColor: isDark ? '#ffffff' : '#18181b',
                                backgroundColor: isDark ? 'rgba(255, 255, 255, 0.04)' : 'rgba(24, 24, 27, 0.04)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: isDark ? '#ffffff' : '#18181b',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#18181b' : '#ffffff',
                                    titleColor: isDark ? '#ffffff' : '#18181b',
                                    bodyColor: isDark ? '#a1a1aa' : '#52525b',
                                    borderColor: isDark ? '#27272a' : '#e4e4e7',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        label: (context) => isRevenue ? ` Revenue: $${context.parsed.y.toFixed(2)}` : ` Orders: ${context.parsed.y}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: textColor, font: { size: 10 } }
                                },
                                y: {
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        font: { size: 10 },
                                        callback: (value) => isRevenue ? `$${value}` : value
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },

                renderDonutChart() {
                    const ctx = document.getElementById('donutChart');
                    if (!ctx || !window.Chart) return;

                    if (this.donutInstance) {
                        this.donutInstance.destroy();
                    }

                    const isDark = document.documentElement.classList.contains('dark');
                    const palettes = isDark
                        ? ['#ffffff', '#a1a1aa', '#71717a', '#52525b', '#27272a']
                        : ['#18181b', '#52525b', '#71717a', '#a1a1aa', '#d4d4d8'];

                    this.donutInstance = new window.Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.categoryLabels,
                            datasets: [{
                                data: this.categoryCounts,
                                backgroundColor: palettes,
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        color: isDark ? '#a1a1aa' : '#71717a',
                                        font: { size: 10 },
                                        padding: 12
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#18181b' : '#ffffff',
                                    titleColor: isDark ? '#ffffff' : '#18181b',
                                    bodyColor: isDark ? '#a1a1aa' : '#52525b',
                                    borderColor: isDark ? '#27272a' : '#e4e4e7',
                                    borderWidth: 1,
                                    padding: 10
                                }
                            },
                            cutout: '72%'
                        }
                    });
                }
            }">

            {{-- 14-Day Velocity Area Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white">Sales & Demand Trajectory</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">14-day rolling performance analysis</p>
                    </div>

                    {{-- Metric Toggle Pills --}}
                    <div class="inline-flex rounded-md p-0.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs">
                        <button type="button"
                            @click="activeMetric = 'revenue'; renderVelocityChart()"
                            :class="activeMetric === 'revenue' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white font-medium shadow-xs' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white'"
                            class="px-2.5 py-1 rounded transition text-[11px]">
                            Revenue
                        </button>
                        <button type="button"
                            @click="activeMetric = 'orders'; renderVelocityChart()"
                            :class="activeMetric === 'orders' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white font-medium shadow-xs' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white'"
                            class="px-2.5 py-1 rounded transition text-[11px]">
                            Volume (Orders)
                        </button>
                    </div>
                </div>

                <div class="relative h-64 w-full">
                    <canvas id="velocityChart"></canvas>
                </div>
            </div>

            {{-- Genre Breakdown Doughnut --}}
            <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 flex flex-col justify-between">
                <div>
                    <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white">Genre Distribution</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Top volume shares by category</p>
                </div>

                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>

        </div>

        {{-- 3. Operational Data Grid (Recent Orders & Low Stock Alerts) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Orders Stream (2 cols) --}}
            <div class="lg:col-span-2 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                <div class="p-4 sm:px-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white">Recent Orders Stream</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Latest transactions across the storefront</p>
                    </div>

                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                        View All
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400 font-medium text-xs">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Order</th>
                                <th class="px-5 py-3 font-semibold">Customer</th>
                                <th class="px-5 py-3 font-semibold">Amount</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold">Date</th>
                                <th class="px-5 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                            @forelse ($recentOrders as $order)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                    <td class="px-5 py-3 font-medium tabular-nums text-zinc-900 dark:text-white">
                                        #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="font-medium text-zinc-900 dark:text-white block">{{ $order->user->name ?? 'Guest' }}</span>
                                        <span class="text-[11px] text-zinc-400">{{ $order->user->email ?? 'No email' }}</span>
                                    </td>
                                    <td class="px-5 py-3 font-serif font-bold text-zinc-900 dark:text-white">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $badgeClasses = match($order->status) {
                                                'paid', 'completed' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200 dark:border-zinc-700',
                                                'shipped'           => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                                'pending'           => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                                'failed'            => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                                default             => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $badgeClasses }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-zinc-400 text-[11px] whitespace-nowrap">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-xs text-zinc-400">
                                        No recent orders recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Low Stock & Catalog Alerts (1 col) --}}
            <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white">Inventory Health</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Volumes needing restock (≤ 5 copies)</p>
                        </div>
                        <a href="{{ route('admin.books.index') }}" class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                            Catalog
                        </a>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 mt-2">
                        @forelse ($lowStockBooks as $lowBook)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-serif font-bold text-xs text-zinc-900 dark:text-white truncate">
                                        {{ $lowBook->title }}
                                    </h3>
                                    <p class="text-[11px] text-zinc-400 truncate">by {{ $lowBook->author }}</p>
                                </div>

                                <div class="flex items-center space-x-2.5 rtl:space-x-reverse flex-shrink-0">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $lowBook->stock === 0 ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' }}">
                                        {{ $lowBook->stock }} left
                                    </span>
                                    <a href="{{ route('admin.books.edit', $lowBook->id) }}"
                                        class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-medium"
                                        title="Restock volume">
                                        Restock
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-zinc-400">
                                All volumes adequately stocked.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 mt-4">
                    <a href="{{ route('admin.books.create') }}"
                        class="w-full inline-flex items-center justify-center px-4 py-2 rounded-md text-xs font-medium text-zinc-800 dark:text-zinc-200 bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                        Add New Volume
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-admin-layout>
