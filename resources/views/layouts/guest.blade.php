<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookStore') }} — Account Access</title>

    <!-- Google Fonts: Inter, Playfair Display, Noto Sans Arabic, Amiri (DESIGN.md Editorial System) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- avoid-ai-design-ignore: T1 -->
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

<body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-[#090a0f] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 transition-colors">
    <div class="w-full max-w-4xl bg-white dark:bg-[#12131a] rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col md:flex-row min-h-[520px]">
        
        <!-- Left Showcase Panel -->
        <div class="md:w-5/12 bg-zinc-900 text-white p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5 rtl:space-x-reverse">
                    <span class="w-7 h-7 rounded-md bg-white text-zinc-900 flex items-center justify-center font-serif font-bold text-xs">
                        B
                    </span>
                    <span class="font-serif text-xl font-bold tracking-tight text-white">
                        BookStore
                    </span>
                </a>

                <div class="mt-10 space-y-2">
                    <h2 class="font-serif text-2xl font-bold leading-tight text-white">
                        Where Curious Minds Find Rare Discoveries.
                    </h2>
                    <p class="text-xs text-zinc-400 leading-relaxed pt-1">
                        Access your curated library, track your physical book deliveries, and discover timeless literature.
                    </p>
                </div>
            </div>

            <div class="pt-6 border-t border-zinc-800">
                <blockquote class="text-xs text-zinc-400 italic font-serif">
                    "Reading is essential for those who seek to rise above the ordinary."
                </blockquote>
                <p class="text-[11px] text-zinc-500 font-medium mt-1">— Jim Rohn</p>
            </div>
        </div>

        <!-- Right Content / Form Panel -->
        <div class="md:w-7/12 p-8 sm:p-10 flex flex-col justify-center bg-white dark:bg-[#12131a]">
            <div class="w-full max-w-sm mx-auto">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>

</html>