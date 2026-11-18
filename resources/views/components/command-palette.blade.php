<div x-data="commandPalette()"
    @keydown.window.prevent.cmd.k="open()"
    @keydown.window.prevent.ctrl.k="open()"
    @open-command-palette.window="open()"
    x-cloak>

    {{-- Solid Translucent Backdrop (No AI blur glassmorphism K3) --}}
    <div x-show="isOpen"
        x-cloak
        style="display: none;"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 flex items-start justify-center pt-[10vh] sm:pt-[14vh] px-3 sm:px-4"
        @click="close()">

        {{-- Palette Container: Monochromatic, crisp 1px borders, restrained elevation --}}
        <div @click.stop
            x-show="isOpen"
            x-cloak
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('Command Palette') }}"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-xl bg-white dark:bg-[#12131a] rounded-lg shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col max-h-[75vh]">

            {{-- Top Search Bar --}}
            <div class="relative flex items-center px-4 py-3.5 border-b border-zinc-100 dark:border-zinc-800">
                <svg class="w-4 h-4 text-zinc-400 mr-3 rtl:mr-0 rtl:ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <input type="text"
                    x-ref="searchInput"
                    x-model="query"
                    @input.debounce.200ms="performSearch()"
                    @keydown.down.prevent="selectNext()"
                    @keydown.up.prevent="selectPrev()"
                    @keydown.enter.prevent="executeCurrent()"
                    @keydown.escape="close()"
                    placeholder="{{ __('Search titles, authors, genres, or actions...') }}"
                    aria-label="{{ __('Search titles, authors, genres, or actions...') }}"
                    class="w-full bg-transparent border-0 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-0 p-0 font-normal">

                <button @click="close()"
                    aria-label="{{ __('Close search dialog') }}"
                    class="px-2 py-1 text-[11px] font-sans font-medium text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 rounded transition ml-2 rtl:ml-0 rtl:mr-2">
                    ESC
                </button>
            </div>

            {{-- Results & Command List --}}
            <div class="overflow-y-auto p-2" x-ref="resultsList">

                {{-- Loading Skeleton --}}
                <template x-if="loading">
                    <div class="p-3 space-y-2">
                        <div class="h-10 bg-zinc-100 dark:bg-zinc-800/60 rounded animate-pulse"></div>
                        <div class="h-10 bg-zinc-100 dark:bg-zinc-800/60 rounded animate-pulse"></div>
                    </div>
                </template>

                {{-- Book Search Results --}}
                <template x-if="!loading && query.trim() !== '' && results.length > 0">
                    <div class="space-y-1">
                        <div class="px-3 py-1.5 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                            Volumes Found (<span x-text="results.length"></span>)
                        </div>
                        <template x-for="(item, idx) in results" :key="item.id">
                            <a :href="item.url"
                                @mouseenter="selectedIndex = idx"
                                :class="{ 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white': selectedIndex === idx }"
                                class="flex items-center justify-between px-3 py-2 rounded text-xs transition group">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse min-w-0">
                                    <div class="w-8 h-10 rounded bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex-shrink-0 overflow-hidden flex items-center justify-center p-0.5">
                                        <img :src="item.cover" :alt="item.title" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="truncate">
                                        <p class="font-serif font-bold truncate text-zinc-900 dark:text-zinc-100" x-text="item.title"></p>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">
                                            <span x-text="item.author"></span>
                                            <span class="text-zinc-300 dark:text-zinc-600 px-1">—</span>
                                            <span class="text-zinc-400 dark:text-zinc-500" x-text="item.category"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 rtl:space-x-reverse ml-3 flex-shrink-0">
                                    <span class="font-serif font-bold text-zinc-900 dark:text-white" x-text="item.price"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>

                {{-- Empty Search State --}}
                <template x-if="!loading && query.trim() !== '' && results.length === 0">
                    <div class="p-8 text-center">
                        <p class="font-serif text-sm font-semibold text-zinc-900 dark:text-white mb-1">No volumes found</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            No titles, authors, or categories matched "<span x-text="query"></span>".
                        </p>
                    </div>
                </template>

                {{-- Default Navigation & Actions (Shown when query is empty) --}}
                <template x-if="query.trim() === ''">
                    <div class="space-y-4 p-1">
                        {{-- Navigation Group (Monochromatic SVG Icons, no emoji slop CP6) --}}
                        <div>
                            <div class="px-2.5 py-1 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                                Store Navigation
                            </div>
                            <div class="space-y-0.5 mt-1">
                                <a href="{{ route('books.index') }}"
                                    class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span class="font-medium text-zinc-900 dark:text-white">Explore All Books</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400">/books</span>
                                </a>

                                <a href="{{ route('cart.index') }}"
                                    class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span class="font-medium text-zinc-900 dark:text-white">Shopping Bag</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400">/cart</span>
                                </a>

                                <a href="{{ route('wishlist.index') }}"
                                    class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="font-medium text-zinc-900 dark:text-white">Saved Volumes</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400">/wishlist</span>
                                </a>

                                @auth
                                    <a href="{{ route('orders.index') }}"
                                        class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            <span class="font-medium text-zinc-900 dark:text-white">Order History & Invoices</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400">/my-orders</span>
                                    </a>
                                @endauth
                            </div>
                        </div>

                        {{-- Preferences Group --}}
                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <div class="px-2.5 py-1 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                                System Preferences
                            </div>
                            <div class="space-y-1.5 mt-1">
                                <button type="button" @click="toggleTheme()"
                                    class="w-full group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition text-left rtl:text-right">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                        <span class="font-medium text-zinc-900 dark:text-white">Toggle Dark / Light Theme</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400">Switch mode</span>
                                </button>

                                <div class="grid grid-cols-3 gap-1.5 pt-1">
                                    <a href="{{ route('locale.switch', 'en') }}"
                                        class="flex items-center justify-center px-2 py-1.5 rounded text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'en' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        English
                                    </a>
                                    <a href="{{ route('locale.switch', 'fr') }}"
                                        class="flex items-center justify-center px-2 py-1.5 rounded text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'fr' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        Français
                                    </a>
                                    <a href="{{ route('locale.switch', 'ar') }}"
                                        class="flex items-center justify-center px-2 py-1.5 rounded text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'ar' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        العربية
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Actions Group (If user is Admin) --}}
                        @if (auth()->check() && auth()->user()->is_admin)
                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                <div class="px-2.5 py-1 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                                    Store Administration
                                </div>
                                <div class="space-y-0.5 mt-1">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <span class="font-medium text-zinc-900 dark:text-white">Admin Operations & Analytics</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400">Dashboard</span>
                                    </a>

                                    <a href="{{ route('admin.books.create') }}"
                                        class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="font-medium text-zinc-900 dark:text-white">Add New Book</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400">New Volume</span>
                                    </a>

                                    <a href="{{ route('admin.orders.index') }}"
                                        class="group flex items-center justify-between px-2.5 py-2 rounded text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-medium text-zinc-900 dark:text-white">Orders & Sales Audit</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400">Orders</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </template>

            </div>

            {{-- Footer Command Bar Keyboard Hints --}}
            <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-900/40 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-400">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <span class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/70 dark:bg-zinc-800 text-[10px] text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↑</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/70 dark:bg-zinc-800 text-[10px] text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↓</kbd>
                        <span class="text-[11px]">Navigate</span>
                    </span>
                    <span class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/70 dark:bg-zinc-800 text-[10px] text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↵</kbd>
                        <span class="text-[11px]">Select</span>
                    </span>
                </div>
                <div>
                    <span class="text-[11px] text-zinc-400">BookStore Palette</span>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function commandPalette() {
        return {
            isOpen: false,
            query: '',
            results: [],
            selectedIndex: 0,
            loading: false,
            previousFocusedElement: null,

            open() {
                this.previousFocusedElement = document.activeElement;
                this.isOpen = true;
                this.query = '';
                this.results = [];
                this.selectedIndex = 0;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            },

            close() {
                this.isOpen = false;
                if (this.previousFocusedElement && typeof this.previousFocusedElement.focus === 'function') {
                    this.previousFocusedElement.focus();
                }
            },

            async performSearch() {
                const q = this.query.trim();
                if (q.length < 1) {
                    this.results = [];
                    this.loading = false;
                    return;
                }

                this.loading = true;
                try {
                    const res = await fetch(`/api/search/palette?q=${encodeURIComponent(q)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) {
                        this.results = await res.json();
                        this.selectedIndex = 0;
                    }
                } catch (e) {
                    console.error('Command Palette Search Error:', e);
                } finally {
                    this.loading = false;
                }
            },

            selectNext() {
                if (this.results.length > 0) {
                    this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
                }
            },

            selectPrev() {
                if (this.results.length > 0) {
                    this.selectedIndex = (this.selectedIndex - 1 + this.results.length) % this.results.length;
                }
            },

            executeCurrent() {
                if (this.results.length > 0 && this.results[this.selectedIndex]) {
                    window.location.href = this.results[this.selectedIndex].url;
                }
            },

            toggleTheme() {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                this.close();
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { title: 'Theme Updated', message: isDark ? 'Switched to Midnight Dark mode' : 'Switched to Clean Light mode', type: 'info' }
                }));
            }
        };
    }
</script>
