{{-- Announcement Bar: Quiet ultra-slim literary strip --}}
<div class="bg-zinc-950 text-zinc-400 text-[10.5px] py-1 px-4 border-b border-zinc-800/80 text-center font-normal no-print">
    <span>{{ __('Free Delivery on orders over $35') }} — {{ __('Curated Editions & Rare Finds') }}</span>
</div>

{{-- Main Navigation Bar: Sleek, compact 54px, architectural, crisp borders --}}
<nav x-data="{ open: false }" class="bg-white dark:bg-[#0e0f15] border-b border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 sticky top-0 z-50 transition-colors no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-13 sm:h-14">
            
            {{-- Left: Brand Logo & Navigation Links --}}
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 rtl:space-x-reverse group">
                    <span class="w-7 h-7 rounded-md bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center transition-transform group-hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3.75L3.75 6.75v12L12 15.75l8.25 3V6.75L12 3.75zM12 14.25l-6.75 2.25V7.875L12 5.625l6.75 2.25v8.625L12 14.25z"/>
                        </svg>
                    </span>
                    <span class="font-serif text-lg font-bold tracking-tight text-zinc-900 dark:text-white">
                        BookStore
                    </span>
                </a>

                {{-- Desktop Nav Menu Links --}}
                <div class="hidden lg:flex items-center space-x-1 rtl:space-x-reverse">
                    <a href="{{ route('books.index') }}"
                        class="px-2.5 py-1 rounded-md text-xs font-medium transition {{ request()->routeIs('books.*') ? 'text-zinc-900 dark:text-white font-semibold bg-zinc-100 dark:bg-zinc-800' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
                        {{ __('Explore Books') }}
                    </a>

                    {{-- Categories Dropdown Menu --}}
                    <div class="relative" x-data="{ catOpen: false }" @click.outside="catOpen = false">
                        <button @click="catOpen = !catOpen" type="button"
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">
                            <span>{{ __('Browse Genres') }}</span>
                            <svg class="ml-1 rtl:ml-0 rtl:mr-1 h-3 w-3 text-zinc-400 transition-transform duration-200" :class="{ 'rotate-180': catOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="catOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            class="absolute left-0 rtl:left-auto rtl:right-0 z-50 mt-1.5 w-56 rounded-lg shadow-lg bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 p-1.5 text-xs divide-y divide-zinc-100 dark:divide-zinc-800"
                            style="display: none;">
                            <div class="pb-1">
                                <a href="{{ route('books.index') }}"
                                    class="flex items-center justify-between px-3 py-1.5 rounded-md font-semibold text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                    <span>{{ __('All Categories') }}</span>
                                    <span>→</span>
                                </a>
                            </div>
                            <div class="pt-1 space-y-0.5 max-h-60 overflow-y-auto">
                                @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                                    <a href="{{ route('categories.show', $category->slug) }}"
                                        class="block px-3 py-1.5 rounded-md text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('orders.index') }}"
                            class="px-2.5 py-1 rounded-md text-xs font-medium transition {{ request()->routeIs('orders.*') ? 'text-zinc-900 dark:text-white font-semibold bg-zinc-100 dark:bg-zinc-800' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
                            {{ __('My Orders') }}
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Center/Right: Restrained Search & Functional Controls --}}
            <div class="hidden md:flex items-center space-x-3 rtl:space-x-reverse">
                {{-- Fast Search Input with Command Palette Trigger (⌘K) --}}
                <div class="relative cursor-pointer group" @click="$dispatch('open-command-palette')">
                    <button type="button"
                        class="w-48 lg:w-64 bg-zinc-50 dark:bg-zinc-900 text-xs text-zinc-400 pl-8 pr-3 rtl:pl-3 rtl:pr-8 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 flex items-center justify-between text-left rtl:text-right transition-all">
                        <span class="truncate">{{ __('Search or press ⌘K...') }}</span>
                        <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-mono text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded pointer-events-none">⌘K</kbd>
                    </button>
                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 rtl:left-auto rtl:right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Language Switcher: Minimal, discrete text selector --}}
                <div class="relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen" type="button"
                        class="flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="langOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 rtl:right-auto rtl:left-0 z-50 mt-1.5 w-32 rounded-lg shadow-lg bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 p-1 text-xs"
                        style="display: none;">
                        <a href="{{ route('locale.switch', 'en') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'en' ? 'font-semibold text-zinc-900 dark:text-white bg-zinc-50 dark:bg-zinc-800/60' : 'text-zinc-600 dark:text-zinc-400' }}">
                            <span>English</span>
                            @if(app()->getLocale() === 'en') <span>✓</span> @endif
                        </a>
                        <a href="{{ route('locale.switch', 'fr') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'fr' ? 'font-semibold text-zinc-900 dark:text-white bg-zinc-50 dark:bg-zinc-800/60' : 'text-zinc-600 dark:text-zinc-400' }}">
                            <span>Français</span>
                            @if(app()->getLocale() === 'fr') <span>✓</span> @endif
                        </a>
                        <a href="{{ route('locale.switch', 'ar') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'ar' ? 'font-semibold text-zinc-900 dark:text-white bg-zinc-50 dark:bg-zinc-800/60' : 'text-zinc-600 dark:text-zinc-400' }}">
                            <span>العربية</span>
                            @if(app()->getLocale() === 'ar') <span>✓</span> @endif
                        </a>
                    </div>
                </div>

                {{-- Theme Switcher: Direct Sun/Moon icon button --}}
                <button type="button"
                    onclick="if (document.documentElement.classList.contains('dark')) { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); } else { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); }"
                    title="Toggle Theme"
                    class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    <svg class="w-4 h-4 block dark:hidden text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @php
                    $wishlistCountGlobal = app(\App\Services\WishlistService::class)->getWishlistCount(Auth::user());
                @endphp

                {{-- Wishlist / Saved Volumes Icon --}}
                <div class="relative" x-data="{ count: {{ $wishlistCountGlobal }} }" @wishlist-updated.window="count = $event.detail.count">
                    <a href="{{ route('wishlist.index') }}"
                        title="{{ __('Saved Volumes') }}"
                        class="p-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center justify-center relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span x-show="count > 0" x-text="count"
                            class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center px-1 text-[10px] font-bold text-white bg-rose-600 rounded-full min-w-[18px] h-[18px]"
                            style="{{ ($wishlistCountGlobal > 0) ? '' : 'display: none;' }}">
                            {{ $wishlistCountGlobal }}
                        </span>
                    </a>
                </div>

                {{-- Shopping Bag: Architectural icon with crisp counter --}}
                <div class="relative" x-data="{ count: {{ $cartCountGlobal ?? 0 }} }" @cart-updated.window="count = $event.detail.count">
                    <a href="{{ route('cart.index') }}"
                        title="{{ __('Shopping Bag') }}"
                        class="p-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center justify-center relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="count > 0" x-text="count"
                            class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center px-1 text-[10px] font-bold text-white bg-zinc-900 dark:bg-white dark:text-zinc-900 rounded-full min-w-[18px] h-[18px]"
                            style="{{ (isset($cartCountGlobal) && $cartCountGlobal > 0) ? '' : 'display: none;' }}">
                            {{ $cartCountGlobal ?? '' }}
                        </span>
                    </a>
                </div>

                {{-- User Profile / Auth State --}}
                @auth
                    <div class="relative pl-1 rtl:pl-0 rtl:pr-1" x-data="{ userOpen: false }" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" type="button"
                            class="flex items-center space-x-2 rtl:space-x-reverse text-xs font-medium p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                            <span class="w-6 h-6 rounded-md bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center font-bold text-[11px]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="text-zinc-800 dark:text-zinc-200 hidden xl:block">
                                {{ Auth::user()->name }}
                            </span>
                        </button>

                        <div x-show="userOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 rtl:right-auto rtl:left-0 z-50 mt-1.5 w-52 rounded-lg shadow-lg bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 p-1.5 text-xs divide-y divide-zinc-100 dark:divide-zinc-800"
                            style="display: none;">
                            <div class="px-2.5 py-1.5">
                                <p class="text-[11px] text-zinc-400">Account</p>
                                <p class="font-medium text-zinc-900 dark:text-white truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-2.5 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    {{ __('Settings') }}
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-2.5 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    {{ __('My Orders') }}
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="block px-2.5 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    {{ __('Saved Volumes') }}
                                </a>
                            </div>

                            @if (Auth::user()->is_admin)
                                <div class="py-1 border-t border-zinc-100 dark:border-zinc-800">
                                    <span class="px-2.5 text-[10px] uppercase tracking-wider text-zinc-400 font-bold block mb-1">Store Management</span>
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-md font-semibold text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        <span>{{ __('Admin Panel') }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-mono">⌘</span>
                                    </a>
                                    <a href="{{ route('admin.books.index') }}" class="block px-2.5 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        Manage Books
                                    </a>
                                    <a href="{{ route('admin.orders.index') }}" class="block px-2.5 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        Orders & Sales
                                    </a>
                                </div>
                            @endif
                            <div class="pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left rtl:text-right px-2.5 py-1.5 rounded-md text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 font-medium">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-2 rtl:space-x-reverse pl-1 rtl:pl-0 rtl:pr-1">
                        <a href="{{ route('login') }}" class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white px-3 py-1.5 transition">
                            {{ __('Sign In') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-3.5 py-1.5 text-xs font-medium rounded-lg text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                            {{ __('Join Free') }}
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Mobile Actions Bar --}}
            <div class="flex items-center space-x-2 rtl:space-x-reverse md:hidden">
                <button type="button"
                    onclick="if (document.documentElement.classList.contains('dark')) { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); } else { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); }"
                    class="p-2 text-zinc-600 dark:text-zinc-400">
                    <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                {{-- Mobile Wishlist Icon --}}
                <a href="{{ route('wishlist.index') }}" class="relative p-2 text-zinc-600 dark:text-zinc-400"
                    x-data="{ count: {{ $wishlistCountGlobal ?? 0 }} }" @wishlist-updated.window="count = $event.detail.count">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span x-show="count > 0" x-text="count"
                        class="absolute top-1 right-1 inline-flex items-center justify-center px-1 text-[10px] font-bold text-white bg-rose-600 rounded-full min-w-[16px] h-[16px]"
                        style="{{ (isset($wishlistCountGlobal) && $wishlistCountGlobal > 0) ? '' : 'display: none;' }}">
                        {{ $wishlistCountGlobal ?? '' }}
                    </span>
                </a>

                <a href="{{ route('cart.index') }}" class="relative p-2 text-zinc-600 dark:text-zinc-400"
                    x-data="{ count: {{ $cartCountGlobal ?? 0 }} }" @cart-updated.window="count = $event.detail.count">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-show="count > 0" x-text="count"
                        class="absolute top-1 right-1 inline-flex items-center justify-center px-1 text-[10px] font-bold text-white bg-zinc-900 dark:bg-white dark:text-zinc-900 rounded-full min-w-[16px] h-[16px]"
                        style="{{ (isset($cartCountGlobal) && $cartCountGlobal > 0) ? '' : 'display: none;' }}">
                        {{ $cartCountGlobal ?? '' }}
                    </span>
                </a>

                <button @click="open = !open" type="button" class="p-2 text-zinc-600 dark:text-zinc-400 focus:outline-none">
                    <svg class="h-5 w-5" :class="{ 'hidden': open, 'block': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-5 w-5" :class="{ 'block': open, 'hidden': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Expandable Drawer --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden bg-white dark:bg-[#12131a] border-b border-zinc-200 dark:border-zinc-800 px-4 pt-3 pb-5 space-y-3">
        <button type="button" @click="open = false; $dispatch('open-command-palette')"
            class="w-full bg-zinc-50 dark:bg-zinc-900 text-xs text-zinc-400 px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-left rtl:text-right">
            <span>{{ __('Search or open command palette...') }}</span>
            <kbd class="px-1.5 py-0.5 text-[10px] font-mono text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded">⌘K</kbd>
        </button>

        <div class="flex items-center justify-between py-2 border-y border-zinc-100 dark:border-zinc-800 text-xs">
            <span class="text-zinc-500">Language:</span>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-600 dark:text-zinc-400' }}">EN</a>
                <a href="{{ route('locale.switch', 'fr') }}" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'fr' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-600 dark:text-zinc-400' }}">FR</a>
                <a href="{{ route('locale.switch', 'ar') }}" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'ar' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-600 dark:text-zinc-400' }}">العربية</a>
            </div>
        </div>

        <div class="space-y-1 text-sm font-medium">
            <a href="{{ route('books.index') }}" class="block px-2 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                {{ __('Explore Books') }}
            </a>
            <a href="{{ route('wishlist.index') }}" class="flex items-center justify-between px-2 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <span>{{ __('Saved Volumes') }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold"
                    x-data="{ count: {{ $wishlistCountGlobal ?? 0 }} }" @wishlist-updated.window="count = $event.detail.count" x-text="count"></span>
            </a>
            @auth
                <a href="{{ route('orders.index') }}" class="block px-2 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    {{ __('My Orders') }}
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-2 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    {{ __('Account Settings') }}
                </a>
                @if (Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-2 py-1.5 rounded-md text-zinc-900 dark:text-white font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        {{ __('Admin Panel') }}
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left rtl:text-right px-2 py-1.5 rounded-md text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                        {{ __('Log Out') }}
                    </button>
                </form>
            @else
                <div class="pt-2 flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-xs font-medium border border-zinc-200 dark:border-zinc-800 rounded-lg text-zinc-800 dark:text-zinc-200">
                        {{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-xs font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 rounded-lg">
                        {{ __('Join Free') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>