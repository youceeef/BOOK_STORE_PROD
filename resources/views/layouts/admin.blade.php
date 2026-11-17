<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookStore') }} — Administration Panel</title>

    <!-- Google Fonts: Inter & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-[#faf9f7] min-h-screen flex flex-col">
    <!-- Top Bar (Public Nav) -->
    @include('layouts.navigation')

    <div class="flex flex-1">
        <!-- Admin Dedicated Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 bg-[#1a1a2e] border-r border-navy-800 text-gray-300">
            <div class="p-6 border-b border-navy-800/80">
                <span class="text-xs uppercase tracking-wider text-gold-400 font-bold block mb-1">Store Management</span>
                <h2 class="font-serif text-lg font-bold text-white tracking-wide">Admin Control</h2>
            </div>

            <nav class="p-4 space-y-1.5 flex-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-gold-500/20 text-gold-300 border-l-4 border-gold-500 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-gold-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.books.index') }}"
                    class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.books.*') ? 'bg-gold-500/20 text-gold-300 border-l-4 border-gold-500 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.books.*') ? 'text-gold-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Manage Books
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-gold-500/20 text-gold-300 border-l-4 border-gold-500 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.categories.*') ? 'text-gold-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Categories
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.orders.*') ? 'bg-gold-500/20 text-gold-300 border-l-4 border-gold-500 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.orders.*') ? 'text-gold-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Orders
                </a>

                <a href="{{ route('admin.coupons.index') }}"
                    class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.coupons.*') ? 'bg-gold-500/20 text-gold-300 border-l-4 border-gold-500 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.coupons.*') ? 'text-gold-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Coupons
                </a>
            </nav>

            <div class="p-4 border-t border-navy-800">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-xs font-medium text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Storefront
                </a>
            </div>
        </aside>

        <!-- Main Admin Content Area -->
        <main class="flex-1 py-8 px-4 sm:px-6 lg:px-10">
            @if (isset($header))
                <div class="mb-8 flex items-center justify-between border-b border-gray-200 pb-4">
                    {{ $header }}
                </div>
            @endif

            <!-- Flash alerts -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>

</html>
