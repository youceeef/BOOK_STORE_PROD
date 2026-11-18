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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>

<body class="font-sans antialiased text-zinc-900 bg-[#faf9f6] dark:bg-[#0a0a0f] dark:text-zinc-100 min-h-screen flex flex-col"
    x-data="{ mobileNavOpen: false }">
    <!-- Top Bar (Public Navigation Header) -->
    @include('layouts.navigation')

    <!-- Global Floating Toast Notifications -->
    <x-toast />

    <!-- Global Spotlight Command Palette (⌘K / Ctrl+K) -->
    <x-command-palette />

    <!-- Mobile Admin Sub-Header / Drawer Trigger -->
    <div class="md:hidden flex items-center justify-between px-4 py-2.5 bg-white dark:bg-[#0e0f15] border-b border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
            <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 font-semibold">Admin</span>
            <span class="text-zinc-300 dark:text-zinc-700">/</span>
            <span class="text-xs font-serif font-bold text-zinc-900 dark:text-white">Navigation</span>
        </div>
        <button @click="mobileNavOpen = true" aria-label="Open admin navigation drawer"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span>Menu</span>
        </button>
    </div>

    <!-- Mobile Drawer Off-Canvas Backdrop & Panel -->
    <div x-show="mobileNavOpen"
        x-cloak
        style="display: none;"
        class="relative z-50 md:hidden"
        role="dialog"
        aria-modal="true">
        <div x-show="mobileNavOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50"
            @click="mobileNavOpen = false"></div>

        <div class="fixed inset-0 flex">
            <div x-show="mobileNavOpen"
                x-transition:enter="transition ease-in-out duration-200 transform"
                x-transition:enter-start="-translate-x-full rtl:translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full rtl:translate-x-full"
                class="relative mr-16 flex w-full max-w-xs flex-1">
                <div class="flex w-full flex-col bg-white dark:bg-[#0e0f15] border-r border-zinc-200 dark:border-zinc-800 shadow-xl">
                    <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 font-semibold block">Control Center</span>
                            <h2 class="font-serif text-sm font-bold text-zinc-900 dark:text-white">Store Administration</h2>
                        </div>
                        <button @click="mobileNavOpen = false" aria-label="Close navigation drawer"
                            class="p-1 rounded-md text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <nav class="p-3 space-y-1 flex-1 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Overview & Analytics
                        </a>
                        <a href="{{ route('admin.books.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.books.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Manage Volumes
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Genres & Categories
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Orders & Invoices
                        </a>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.coupons.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Coupons & Discounts
                        </a>
                        @unless(app()->isProduction())
                        <a href="{{ route('admin.mailbox.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-md text-xs font-medium {{ request()->routeIs('admin.mailbox.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                            Mailbox Inspector
                        </a>
                        @endunless
                    </nav>

                    <div class="p-3 border-t border-zinc-200 dark:border-zinc-800">
                        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-900/60">
                            Back to Storefront
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                {{-- Mailbox Inspector --}}
                @unless(app()->isProduction())
                <a href="{{ route('admin.mailbox.index') }}"
                    class="flex items-center px-3 py-2 rounded-md text-xs font-medium transition {{ request()->routeIs('admin.mailbox.*') ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900/60' }}">
                    <svg class="w-4 h-4 mr-2.5 rtl:mr-0 rtl:ml-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Mailbox Inspector</span>
                </a>
                @endunless
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
