<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookStore') }} — Administration Panel</title>

    <!-- Google Fonts: Inter & Playfair Display (DESIGN.md Editorial System) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- avoid-ai-design-ignore: T1 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Dark Mode Anti-FOUC Script & Alpine x-cloak Shield -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-zinc-900 bg-[#faf9f6] dark:bg-[#0a0a0f] dark:text-zinc-100 min-h-screen flex flex-col">
    <!-- Top Bar (Public Navigation Header) -->
    @include('layouts.navigation')

    <!-- Global Floating Toast Notifications -->
    <x-toast />

    <!-- Global Spotlight Command Palette (⌘K / Ctrl+K) -->
    <x-command-palette />

    <div class="flex flex-1">
        <!-- Admin Dedicated Monochromatic Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 bg-white dark:bg-[#0e0f15] border-r border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 flex-shrink-0">
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80">
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Control Center</span>
                <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white tracking-tight">Store Administration</h2>
            </div>

            <nav class="p-3 space-y-1 flex-1">
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Overview & Analytics</span>
                </a>

                {{-- Books --}}
                <a href="{{ route('admin.books.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.books.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Manage Volumes</span>
                </a>

                {{-- Categories --}}
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Genres & Categories</span>
                </a>

                {{-- Orders --}}
                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Orders & Invoices</span>
                </a>

                {{-- Coupons --}}
                <a href="{{ route('admin.coupons.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.coupons.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Coupons & Discounts</span>
                </a>
            </nav>

            <div class="p-3 border-t border-zinc-200 dark:border-zinc-800">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-900/60 transition">
                    <svg class="w-3.5 h-3.5 mr-2 rtl:mr-0 rtl:ml-2 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Storefront
                </a>
            </div>
        </aside>

        <!-- Main Admin Content Area -->
        <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            @if (isset($header))
                <div class="mb-6 pb-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    {{ $header }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>

</html>
