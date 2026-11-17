{{-- Top Announcement Bar --}}
<div class="bg-gradient-to-r from-[#121220] via-[#1a1a2e] to-[#121220] text-gray-300 border-b border-navy-800 text-[11px] sm:text-xs py-2 px-4 no-print">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gold-500/20 text-gold-400 border border-gold-500/30">
                PROMO
            </span>
            <span class="text-gray-300 hidden sm:inline">Use code <strong class="text-gold-400 font-mono">WELCOME10</strong> for 10% off your order</span>
            <span class="text-gray-300 sm:hidden">Code: <strong class="text-gold-400 font-mono">WELCOME10</strong> (10% off)</span>
        </div>
        <div class="flex items-center space-x-4 rtl:space-x-reverse text-gray-400 text-[11px]">
            <span class="hidden md:inline-flex items-center space-x-1 rtl:space-x-reverse">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ __('Free Tracked Delivery') }} over $35</span>
            </span>
            <span class="hidden sm:inline">•</span>
            <span class="text-gold-400 font-serif italic hidden sm:inline">{{ __('Curated Editions & Rare Finds') }}</span>
        </div>
    </div>
</div>

{{-- Main Navigation Bar --}}
<nav x-data="{ open: false, searchFocused: false }" class="bg-[#1a1a2e]/95 backdrop-blur-md border-b border-navy-800 text-gray-200 sticky top-0 z-50 shadow-lg no-print transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            {{-- Left: Brand Logo & Navigation Links --}}
            <div class="flex items-center space-x-8 rtl:space-x-reverse">
                <a href="{{ route('home') }}" class="flex items-center space-x-3.5 rtl:space-x-reverse group">
                    <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-gold-400 via-gold-500 to-gold-600 flex items-center justify-center text-[#1a1a2e] shadow-lg group-hover:scale-105 group-hover:shadow-gold-500/20 transition-all duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3.75L3.75 6.75v12L12 15.75l8.25 3V6.75L12 3.75zM12 14.25l-6.75 2.25V7.875L12 5.625l6.75 2.25v8.625L12 14.25z"/>
                        </svg>
                    </span>
                    <div class="flex flex-col">
                        <span class="font-serif text-2xl font-bold tracking-tight text-white group-hover:text-gold-400 transition-colors">
                            BookStore
                        </span>
                        <span class="text-[10px] uppercase tracking-widest text-gold-400 font-semibold -mt-1">
                            Curated Editions
                        </span>
                    </div>
                </a>

                {{-- Desktop Nav Menu Links --}}
                <div class="hidden lg:flex items-center space-x-1 rtl:space-x-reverse pl-4 rtl:pl-0 rtl:pr-4 border-l rtl:border-l-0 rtl:border-r border-navy-800">
                    <a href="{{ route('books.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('books.*') ? 'bg-white/10 text-gold-400 font-semibold' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        {{ __('Explore Books') }}
                    </a>

                    {{-- Categories Dropdown Menu --}}
                    <div class="relative" x-data="{ catOpen: false }" @click.outside="catOpen = false">
                        <button @click="catOpen = !catOpen" type="button"
                            class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 transition duration-150">
                            <span>{{ __('Browse Genres') }}</span>
                            <svg class="ml-1.5 rtl:ml-0 rtl:mr-1.5 h-4 w-4 text-gold-400 transition-transform duration-200" :class="{ 'rotate-180': catOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="catOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 rtl:left-auto rtl:right-0 z-50 mt-2 w-64 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2 divide-y divide-navy-800"
                            style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('books.index') }}"
                                    class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gold-400 hover:bg-white/5 transition">
                                    <span>{{ __('All Categories') }}</span>
                                    <span>→</span>
                                </a>
                            </div>
                            <div class="py-1 space-y-0.5 max-h-64 overflow-y-auto">
                                @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                                    <a href="{{ route('categories.show', $category->slug) }}"
                                        class="flex items-center justify-between px-3.5 py-2 rounded-xl text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                        <span>{{ $category->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('orders.index') }}"
                            class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-white/10 text-gold-400 font-semibold' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            {{ __('My Orders') }}
                        </a>

                        @if (Auth::user()->is_admin)
                            {{-- Admin Dropdown --}}
                            <div class="relative pl-2 rtl:pl-0 rtl:pr-2" x-data="{ admOpen: false }" @click.outside="admOpen = false">
                                <button @click="admOpen = !admOpen" type="button"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gold-500/20 text-gold-400 border border-gold-500/30 hover:bg-gold-500/30 transition shadow-sm">
                                    <span>{{ __('Admin Panel') }}</span>
                                    <svg class="ml-1 rtl:ml-0 rtl:mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="admOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    class="absolute left-0 rtl:left-auto rtl:right-0 z-50 mt-2 w-52 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2"
                                    style="display: none;">
                                    <a href="{{ route('admin.dashboard') }}" class="block px-3.5 py-2 rounded-xl text-xs font-semibold text-gold-400 hover:bg-white/5">Overview Dashboard</a>
                                    <a href="{{ route('admin.books.index') }}" class="block px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5">Manage Books</a>
                                    <a href="{{ route('admin.categories.index') }}" class="block px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5">Manage Categories</a>
                                    <a href="{{ route('admin.orders.index') }}" class="block px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5">Orders & Sales</a>
                                    <a href="{{ route('admin.coupons.index') }}" class="block px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5">Coupons & Promos</a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Center/Right: Enhanced Search & Interactive Actions --}}
            <div class="hidden md:flex items-center space-x-3.5 rtl:space-x-reverse">
                {{-- Fast Search Input --}}
                <form action="{{ route('books.index') }}" method="GET" class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search books, authors, ISBN...') }}"
                        class="w-48 lg:w-64 bg-[#121220]/80 text-sm text-gray-200 placeholder-gray-400 pl-10 pr-8 rtl:pl-8 rtl:pr-10 py-2 rounded-full border border-navy-800 focus:w-72 focus:bg-[#121220] focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20 transition-all duration-300">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3 group-focus-within:text-gold-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if (request('search'))
                        <a href="{{ route('books.index') }}" class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-gray-400 hover:text-white text-xs">
                            ✕
                        </a>
                    @endif
                </form>

                {{-- Shopping Bag with Live Counter --}}
                <div class="relative" x-data="{ count: {{ $cartCountGlobal ?? 0 }} }" @cart-updated.window="count = $event.detail.count">
                    <a href="{{ route('cart.index') }}"
                        title="{{ __('Shopping Bag') }}"
                        class="relative p-2.5 rounded-2xl text-gray-300 hover:text-white hover:bg-white/5 transition flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="count > 0" x-text="count"
                            class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[11px] font-bold leading-none text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 rounded-full shadow-md min-w-[20px] h-[20px] transition-transform duration-200 transform scale-100"
                            style="{{ (isset($cartCountGlobal) && $cartCountGlobal > 0) ? '' : 'display: none;' }}">
                            {{ $cartCountGlobal ?? '' }}
                        </span>
                    </a>
                </div>

                {{-- shadcn/ui Theme Switcher Dropdown --}}
                <div class="relative" x-data="{
                    themeOpen: false,
                    currentTheme: localStorage.getItem('theme') || 'system',
                    setTheme(val) {
                        this.currentTheme = val;
                        if (val === 'dark') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        } else if (val === 'light') {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme', 'light');
                        } else {
                            localStorage.removeItem('theme');
                            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                        this.themeOpen = false;
                    }
                }" @click.outside="themeOpen = false">
                    <button @click="themeOpen = !themeOpen" type="button"
                        title="Theme Mode"
                        class="p-2.5 rounded-2xl text-gray-300 hover:text-white hover:bg-white/5 transition flex items-center justify-center border border-transparent hover:border-navy-800">
                        {{-- Sun Icon (visible in light mode) --}}
                        <svg class="w-5 h-5 block dark:hidden text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{-- Moon Icon (visible in dark mode) --}}
                        <svg class="w-5 h-5 hidden dark:block text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <div x-show="themeOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        class="absolute right-0 rtl:right-auto rtl:left-0 z-50 mt-2 w-36 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-1.5 text-xs text-gray-200"
                        style="display: none;">
                        <button @click="setTheme('light')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl hover:bg-white/5 transition" :class="{ 'text-gold-400 font-semibold bg-white/5': currentTheme === 'light' }">
                            <span class="flex items-center gap-2"><span>☀️</span><span>{{ __('Light') }}</span></span>
                            <span x-show="currentTheme === 'light'" class="text-gold-400">✓</span>
                        </button>
                        <button @click="setTheme('dark')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl hover:bg-white/5 transition" :class="{ 'text-gold-400 font-semibold bg-white/5': currentTheme === 'dark' }">
                            <span class="flex items-center gap-2"><span>🌙</span><span>{{ __('Dark') }}</span></span>
                            <span x-show="currentTheme === 'dark'" class="text-gold-400">✓</span>
                        </button>
                        <button @click="setTheme('system')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl hover:bg-white/5 transition" :class="{ 'text-gold-400 font-semibold bg-white/5': currentTheme === 'system' }">
                            <span class="flex items-center gap-2"><span>💻</span><span>{{ __('System') }}</span></span>
                            <span x-show="currentTheme === 'system'" class="text-gold-400">✓</span>
                        </button>
                    </div>
                </div>

                {{-- Language Switcher Dropdown --}}
                <div class="relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen" type="button"
                        title="Language"
                        class="flex items-center space-x-1.5 rtl:space-x-reverse px-2.5 py-1.5 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-white/5 border border-navy-800 transition">
                        <span>
                            @if(app()->getLocale() === 'fr') 🇫🇷 FR
                            @elseif(app()->getLocale() === 'ar') 🇸🇦 العربية
                            @else 🇬🇧 EN
                            @endif
                        </span>
                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': langOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="langOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        class="absolute right-0 rtl:right-auto rtl:left-0 z-50 mt-2 w-36 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-1.5 text-xs text-gray-200"
                        style="display: none;">
                        <a href="{{ route('locale.switch', 'en') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-xl hover:bg-white/5 transition {{ app()->getLocale() === 'en' ? 'text-gold-400 font-semibold bg-white/5' : '' }}">
                            <span>🇬🇧 English</span>
                            @if(app()->getLocale() === 'en') <span class="text-gold-400">✓</span> @endif
                        </a>
                        <a href="{{ route('locale.switch', 'fr') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-xl hover:bg-white/5 transition {{ app()->getLocale() === 'fr' ? 'text-gold-400 font-semibold bg-white/5' : '' }}">
                            <span>🇫🇷 Français</span>
                            @if(app()->getLocale() === 'fr') <span class="text-gold-400">✓</span> @endif
                        </a>
                        <a href="{{ route('locale.switch', 'ar') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-xl hover:bg-white/5 transition {{ app()->getLocale() === 'ar' ? 'text-gold-400 font-semibold bg-white/5' : '' }}">
                            <span>🇸🇦 العربية</span>
                            @if(app()->getLocale() === 'ar') <span class="text-gold-400">✓</span> @endif
                        </a>
                    </div>
                </div>

                {{-- User Profile / Auth State --}}
                @auth
                    <div class="relative" x-data="{ userOpen: false }" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" type="button"
                            class="flex items-center space-x-3 rtl:space-x-reverse text-sm focus:outline-none p-1.5 rounded-2xl hover:bg-white/5 transition group">
                            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#252849] to-[#121220] text-gold-400 border border-gold-500/40 flex items-center justify-center font-bold text-sm shadow-sm group-hover:border-gold-400 transition">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="text-gray-200 group-hover:text-white font-medium text-sm hidden xl:block">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="userOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            class="absolute right-0 rtl:right-auto rtl:left-0 z-50 mt-2 w-60 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2 divide-y divide-navy-800"
                            style="display: none;">
                            <div class="px-3.5 py-2.5">
                                <p class="text-xs text-gray-400">Signed in as</p>
                                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition">
                                    Account Settings
                                </a>
                                <a href="{{ route('orders.index') }}" class="flex items-center px-3.5 py-2 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition">
                                    {{ __('My Orders') }}
                                </a>
                                @if (Auth::user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2 rounded-xl text-xs font-semibold text-gold-400 hover:bg-white/5 transition">
                                        {{ __('Admin Panel') }}
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left rtl:text-right px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-white/5 transition">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium text-gray-300 hover:text-white px-2.5 py-2 transition">
                            {{ __('Sign In') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 rounded-full shadow-md transition-all duration-200">
                            {{ __('Join Free') }}
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Mobile Actions Bar --}}
            <div class="flex items-center space-x-2 rtl:space-x-reverse md:hidden">
                {{-- Mobile Theme Switcher Icon --}}
                <button type="button"
                    onclick="if (document.documentElement.classList.contains('dark')) { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); } else { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); }"
                    class="p-2 text-gray-300 hover:text-white rounded-xl">
                    <svg class="w-5 h-5 block dark:hidden text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-300"
                    x-data="{ count: {{ $cartCountGlobal ?? 0 }} }" @cart-updated.window="count = $event.detail.count">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-show="count > 0" x-text="count"
                        class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-[#1a1a2e] bg-gold-400 rounded-full"
                        style="{{ (isset($cartCountGlobal) && $cartCountGlobal > 0) ? '' : 'display: none;' }}">
                        {{ $cartCountGlobal ?? '' }}
                    </span>
                </a>

                <button @click="open = !open" type="button" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 focus:outline-none">
                    <svg class="h-6 w-6" :class="{ 'hidden': open, 'block': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'block': open, 'hidden': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Expandable Drawer --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden bg-[#1e2038] border-b border-navy-800 px-4 pt-3 pb-6 space-y-4">
        {{-- Mobile Search --}}
        <form action="{{ route('books.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="{{ __('Search books, authors, ISBN...') }}"
                class="w-full bg-[#121220] text-sm text-gray-200 placeholder-gray-400 pl-10 pr-4 rtl:pl-4 rtl:pr-10 py-2.5 rounded-xl border border-navy-800 focus:outline-none focus:border-gold-500">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </form>

        {{-- Mobile Language & Theme Bar --}}
        <div class="p-2 rounded-2xl bg-[#121220] border border-navy-800 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1 rounded-lg {{ app()->getLocale() === 'en' ? 'bg-gold-500/20 text-gold-400 font-bold border border-gold-500/30' : 'text-gray-400 hover:text-white' }}">EN</a>
                <a href="{{ route('locale.switch', 'fr') }}" class="px-2.5 py-1 rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-gold-500/20 text-gold-400 font-bold border border-gold-500/30' : 'text-gray-400 hover:text-white' }}">FR</a>
                <a href="{{ route('locale.switch', 'ar') }}" class="px-2.5 py-1 rounded-lg {{ app()->getLocale() === 'ar' ? 'bg-gold-500/20 text-gold-400 font-bold border border-gold-500/30' : 'text-gray-400 hover:text-white' }}">العربية</a>
            </div>
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                <button type="button" onclick="document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light');" class="px-2 py-1 rounded-lg text-gray-300 hover:text-amber-400">☀️</button>
                <button type="button" onclick="document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark');" class="px-2 py-1 rounded-lg text-gray-300 hover:text-gold-400">🌙</button>
            </div>
        </div>

        <div class="space-y-1">
            <a href="{{ route('books.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                {{ __('Explore Books') }}
            </a>

            <div x-data="{ expanded: false }" class="border-t border-navy-800 pt-2">
                <button @click="expanded = !expanded" class="flex justify-between items-center w-full px-3 py-2 text-base font-medium text-gray-200">
                    <span>{{ __('Browse Genres') }}</span>
                    <svg :class="{ 'rotate-180': expanded }" class="w-4 h-4 text-gold-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="pl-4 rtl:pl-0 rtl:pr-4 space-y-1 mt-1">
                    @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="block px-3 py-1.5 text-sm text-gray-400 hover:text-white">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                    {{ __('My Orders') }}
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                    Account Settings
                </a>

                @if (Auth::user()->is_admin)
                    <div class="border-t border-navy-800 pt-2 pb-1">
                        <span class="px-3 text-xs uppercase font-semibold text-gold-400">{{ __('Admin Panel') }}</span>
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Dashboard</a>
                        <a href="{{ route('admin.books.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Manage Books</a>
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Manage Orders</a>
                    </div>
                @endif

                <div class="border-t border-navy-800 pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rtl:text-right px-3 py-2 text-base font-medium text-rose-400 hover:bg-white/5 rounded-xl">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-navy-800 pt-4 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-medium text-white border border-navy-800 hover:bg-white/5">
                        {{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-bold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 shadow-md">
                        {{ __('Join Free') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>