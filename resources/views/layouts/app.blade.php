<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'BookStore') : config('app.name', 'BookStore') . ' — ' . __('Curated Reads & Literary Finds') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Discover curated books, literary treasures, and editorial recommendations at BookStore.' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

    <!-- Google Fonts: Inter, Playfair Display, Noto Sans Arabic, Amiri (DESIGN.md Editorial System) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- avoid-ai-design-ignore: T1 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Noto+Sans+Arabic:wght@400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Dark/Light Mode Detector & Alpine x-cloak Shield -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-[#ffffff] dark:bg-[#090a0f] flex flex-col min-h-screen selection:bg-zinc-900 selection:text-white dark:selection:bg-zinc-100 dark:selection:text-zinc-900 transition-colors duration-150">
    <!-- Main Navigation -->
    @include('layouts.navigation')

    {{-- Unverified Email Verification Prompt Shield --}}
    @auth
        @if (!auth()->user()->hasVerifiedEmail())
            <div class="bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-800/80 px-4 py-2 text-xs text-amber-900 dark:text-amber-200">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <span class="w-4 h-4 rounded-full bg-amber-200 dark:bg-amber-900 text-amber-800 dark:text-amber-200 flex items-center justify-center font-bold text-[10px] shrink-0">!</span>
                        <span>
                            Please confirm your email address (<strong>{{ auth()->user()->email }}</strong>) to unlock checkout and order history.
                        </span>
                    </div>
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <form method="POST" action="{{ route('verification.send') }}" class="inline">
                            @csrf
                            <button type="submit" class="underline font-semibold hover:text-amber-950 dark:hover:text-white transition">
                                Resend Email
                            </button>
                        </form>
                        <span>&bull;</span>
                        <a href="{{ route('verification.notice') }}" class="underline font-semibold hover:text-amber-950 dark:hover:text-white transition">
                            Verify Now
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Optional Page Heading -->
    @if (isset($header))
        <header class="bg-zinc-50 dark:bg-[#0e0f15] border-b border-zinc-200 dark:border-zinc-800 transition-colors">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Global Footer: Clean, architectural, quiet -->
    <footer class="bg-zinc-900 dark:bg-[#07070b] text-zinc-400 mt-16 border-t border-zinc-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Info -->
                <div class="space-y-2">
                    <span class="font-serif text-lg font-bold text-white tracking-tight">BookStore</span>
                    <p class="text-zinc-400 leading-relaxed text-xs max-w-sm">
                        Curated volumes, independent publishing, and timeless literature.
                    </p>
                </div>

                <!-- Explore Links -->
                <div>
                    <h4 class="font-semibold text-zinc-200 uppercase tracking-wider text-[11px] mb-3">Explore</h4>
                    <ul class="space-y-1.5 text-xs text-zinc-400">
                        <li><a href="{{ route('books.index') }}" class="hover:text-white transition">All Volumes</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-white transition">Shopping Bag</a></li>
                        @auth
                            <li><a href="{{ route('orders.index') }}" class="hover:text-white transition">My Orders</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition">Sign In</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Guarantee & Direct Contact -->
                <div>
                    <h4 class="font-semibold text-zinc-200 uppercase tracking-wider text-[11px] mb-3">Service & Shipping</h4>
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        Tracked domestic & international fulfillment. Authentic editions directly from verified publishers.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-zinc-500">
                <p>&copy; {{ date('Y') }} BookStore. All rights reserved.</p>
                <p class="font-serif italic text-zinc-400">Where great minds meet timeless words.</p>
            </div>
        </div>
    </footer>

    {{-- Minimal Toast Notifications --}}
    <x-toast />

    {{-- Global Spotlight Command Palette (⌘K / Ctrl+K) --}}
    <x-command-palette />

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { title: 'Success', message: @json(session('success')), type: 'success' }
                }));
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { title: 'Notice', message: @json(session('error')), type: 'error' }
                }));
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>