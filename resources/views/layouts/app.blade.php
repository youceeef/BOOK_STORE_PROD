<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookStore') }} — Curated Reads & Literary Finds</title>

    <!-- Google Fonts: Inter & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-[#faf9f7] flex flex-col min-h-screen selection:bg-gold-500 selection:text-white">
    <!-- Main Navigation -->
    @include('layouts.navigation')

    <!-- Optional Page Heading -->
    @if (isset($header))
        <header class="bg-white border-b border-gray-200/80 shadow-sm">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Alert / Flash Messages -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Global Footer -->
    <footer class="bg-[#1a1a2e] text-gray-300 mt-20 border-t border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center space-x-3">
                        <span class="p-2 rounded-lg bg-gold-500/20 text-gold-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        <span class="font-serif text-2xl font-bold tracking-tight text-white">BookStore</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Curated stories, timeless classics, and contemporary breakthroughs. Your sanctuary for ideas that matter.
                    </p>
                </div>

                <!-- Explore Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gold-400 tracking-wider uppercase mb-4 font-sans">Explore</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('books.index') }}" class="hover:text-gold-400 transition">All Books</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-gold-400 transition">Shopping Cart</a></li>
                        @auth
                            <li><a href="{{ route('orders.index') }}" class="hover:text-gold-400 transition">My Orders</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-gold-400 transition">Account Settings</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-gold-400 transition">Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-gold-400 transition">Register</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Categories Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gold-400 tracking-wider uppercase mb-4 font-sans">Categories</h3>
                    <ul class="space-y-2.5 text-sm">
                        @foreach (cache()->remember('footer_categories', 3600, fn() => \App\Models\Category::where('active', 1)->take(5)->get()) as $cat)
                            <li>
                                <a href="{{ route('categories.show', $cat->slug) }}" class="hover:text-gold-400 transition">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Newsletter / Trust -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gold-400 tracking-wider uppercase font-sans">Guaranteed Quality</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Every title is carefully inspected. Free tracked delivery on qualifying orders, guaranteed safe checkout via Stripe.
                    </p>
                    <div class="flex items-center space-x-2 text-xs text-gray-400">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>256-bit Encrypted SSL Checkout</span>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-navy-800 text-center sm:flex sm:justify-between sm:text-left text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} BookStore Inc. All rights reserved.</p>
                <p class="mt-2 sm:mt-0 font-serif italic text-gold-500/80">"A room without books is like a body without a soul."</p>
            </div>
        </div>
    </footer>
</body>

</html>
