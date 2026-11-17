<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookStore') }} — Account Access</title>

    <!-- Google Fonts: Inter, Playfair Display, Noto Sans Arabic, Amiri -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Noto+Sans+Arabic:wght@400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Anti-FOUC Dark/Light Mode Detector -->
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

<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-[#faf9f7] dark:bg-[#0d0e15] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 transition-colors duration-200">
    <div class="w-full max-w-5xl bg-white dark:bg-darkcard rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-darkborder flex flex-col md:flex-row min-h-[600px]">
        
        <!-- Left Showcase Panel (Navy & Gold Bookstore Aesthetic) -->
        <div class="md:w-5/12 bg-[#1a1a2e] text-white p-8 sm:p-12 flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative Background Element -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-gold-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -top-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 rtl:space-x-reverse group">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-[#1a1a2e] shadow-md group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3.75L3.75 6.75v12L12 15.75l8.25 3V6.75L12 3.75zM12 14.25l-6.75 2.25V7.875L12 5.625l6.75 2.25v8.625L12 14.25z"/>
                        </svg>
                    </span>
                    <span class="font-serif text-2xl font-bold tracking-tight text-white group-hover:text-gold-400 transition-colors">
                        BookStore
                    </span>
                </a>

                <div class="mt-12 space-y-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-gold-500/20 text-gold-400 border border-gold-500/30">
                        Member Sanctuary
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold leading-tight text-white">
                        Where Curious Minds Find Rare Discoveries.
                    </h2>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Access your curated library, track your physical book deliveries, and unlock special member privileges.
                    </p>
                </div>
            </div>

            <div class="relative z-10 pt-8 mt-8 border-t border-navy-800">
                <blockquote class="text-xs text-gray-400 italic font-serif">
                    "Reading is essential for those who seek to rise above the ordinary."
                </blockquote>
                <p class="text-[11px] text-gold-400 font-semibold mt-1">— Jim Rohn</p>
            </div>
        </div>

        <!-- Right Content / Form Panel -->
        <div class="md:w-7/12 p-8 sm:p-12 flex flex-col justify-center bg-[#faf9f7] dark:bg-darkcard">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>

</html>