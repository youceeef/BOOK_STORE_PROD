{{-- Top Announcement Bar --}}
<div class="bg-gradient-to-r from-[#121220] via-[#1a1a2e] to-[#121220] text-gray-300 border-b border-navy-800 text-[11px] sm:text-xs py-2 px-4 no-print">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gold-500/20 text-gold-400 border border-gold-500/30">
                PROMO
            </span>
            <span class="text-gray-300 hidden sm:inline">Use code <strong class="text-gold-400 font-mono">WELCOME10</strong> for 10% off your first literary order</span>
            <span class="text-gray-300 sm:hidden">Code: <strong class="text-gold-400 font-mono">WELCOME10</strong> (10% off)</span>
        </div>
        <div class="flex items-center space-x-4 text-gray-400 text-[11px]">
            <span class="hidden md:inline-flex items-center space-x-1">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Free Tracked Delivery over $35</span>
            </span>
            <span>•</span>
            <span class="text-gold-400 font-serif italic">Curated Classics & Modern Releases</span>
        </div>
    </div>
</div>

{{-- Main Navigation Bar --}}
<nav x-data="{ open: false, searchFocused: false }" class="bg-[#1a1a2e]/95 backdrop-blur-md border-b border-navy-800 text-gray-200 sticky top-0 z-50 shadow-lg no-print transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            {{-- Left: Brand Logo & Navigation Links --}}
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-3.5 group">
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
                <div class="hidden lg:flex items-center space-x-1 pl-4 border-l border-navy-800">
                    <a href="{{ route('books.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('books.*') ? 'bg-white/10 text-gold-400 font-semibold' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Explore Books
                    </a>

                    {{-- Categories Dropdown Menu --}}
                    <div class="relative" x-data="{ catOpen: false }" @click.outside="catOpen = false">
                        <button @click="catOpen = !catOpen" type="button"
                            class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 transition duration-150">
                            <span>Browse Genres</span>
                            <svg class="ml-1.5 h-4 w-4 text-gold-400 transition-transform duration-200" :class="{ 'rotate-180': catOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            class="absolute z-50 mt-2 w-64 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2 divide-y divide-navy-800"
                            style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('books.index') }}"
                                    class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-gold-400 hover:bg-white/5 transition">
                                    <span>All Genres & Editions</span>
                                    <span>→</span>
                                </a>
                            </div>
                            <div class="py-1 space-y-0.5">
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
                            My Orders
                        </a>

                        @if (Auth::user()->is_admin)
                            {{-- Admin Dropdown --}}
                            <div class="relative pl-2" x-data="{ admOpen: false }" @click.outside="admOpen = false">
                                <button @click="admOpen = !admOpen" type="button"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gold-500/20 text-gold-400 border border-gold-500/30 hover:bg-gold-500/30 transition shadow-sm">
                                    <span>Admin Panel</span>
                                    <svg class="ml-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="admOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    class="absolute z-50 mt-2 w-52 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2"
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
            <div class="hidden md:flex items-center space-x-5">
                {{-- Fast Search Input --}}
                <form action="{{ route('books.index') }}" method="GET" class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search books, authors, ISBN..."
                        class="w-56 lg:w-72 bg-[#121220]/80 text-sm text-gray-200 placeholder-gray-400 pl-10 pr-8 py-2.5 rounded-full border border-navy-800 focus:w-80 focus:bg-[#121220] focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20 transition-all duration-300">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5 group-focus-within:text-gold-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if (request('search'))
                        <a href="{{ route('books.index') }}" class="absolute right-3 top-3 text-gray-400 hover:text-white text-xs">
                            ✕
                        </a>
                    @endif
                </form>

                {{-- Shopping Bag with Live Counter --}}
                <div class="relative" x-data="{ count: {{ $cartCountGlobal ?? 0 }} }" @cart-updated.window="count = $event.detail.count">
                    <a href="{{ route('cart.index') }}"
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

                {{-- User Profile / Auth State --}}
                @auth
                    <div class="relative" x-data="{ userOpen: false }" @click.outside="userOpen = false">
                        <button @click="userOpen = !userOpen" type="button"
                            class="flex items-center space-x-3 text-sm focus:outline-none p-1.5 rounded-2xl hover:bg-white/5 transition group">
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
                            class="absolute right-0 z-50 mt-2 w-60 rounded-2xl shadow-2xl bg-[#1e2038] border border-navy-800 p-2 divide-y divide-navy-800"
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
                                    My Orders
                                </a>
                                @if (Auth::user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2 rounded-xl text-xs font-semibold text-gold-400 hover:bg-white/5 transition">
                                        Admin Dashboard
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-white/5 transition">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white px-3 py-2 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 rounded-full shadow-md transition-all duration-200">
                            Join Free
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Mobile Actions Bar --}}
            <div class="flex items-center space-x-3 md:hidden">
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @if (isset($cartCountGlobal) && $cartCountGlobal > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-[#1a1a2e] bg-gold-400 rounded-full">
                            {{ $cartCountGlobal }}
                        </span>
                    @endif
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
        <form action="{{ route('books.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search catalog..."
                class="w-full bg-[#121220] text-sm text-gray-200 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl border border-navy-800 focus:outline-none focus:border-gold-500">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </form>

        <div class="space-y-1">
            <a href="{{ route('books.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                Browse All Books
            </a>

            <div x-data="{ expanded: false }" class="border-t border-navy-800 pt-2">
                <button @click="expanded = !expanded" class="flex justify-between items-center w-full px-3 py-2 text-base font-medium text-gray-200">
                    <span>Genres</span>
                    <svg :class="{ 'rotate-180': expanded }" class="w-4 h-4 text-gold-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="pl-4 space-y-1 mt-1">
                    @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="block px-3 py-1.5 text-sm text-gray-400 hover:text-white">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                    My Orders
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-gray-200 hover:bg-white/5">
                    Profile Settings
                </a>

                @if (Auth::user()->is_admin)
                    <div class="border-t border-navy-800 pt-2 pb-1">
                        <span class="px-3 text-xs uppercase font-semibold text-gold-400">Admin Area</span>
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Dashboard</a>
                        <a href="{{ route('admin.books.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Manage Books</a>
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white">Manage Orders</a>
                    </div>
                @endif

                <div class="border-t border-navy-800 pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-base font-medium text-rose-400 hover:bg-white/5 rounded-xl">
                            Sign Out
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-navy-800 pt-4 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-medium text-white border border-navy-800 hover:bg-white/5">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-bold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 shadow-md">
                        Join Free
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
