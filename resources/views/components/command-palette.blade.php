<div x-data="commandPalette()"
    @keydown.window.prevent.cmd.k="open()"
    @keydown.window.prevent.ctrl.k="open()"
    @open-command-palette.window="open()"
    x-cloak>

    {{-- Backdrop & Modal Overlay --}}
    <div x-show="isOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-start justify-center pt-[10vh] sm:pt-[14vh] px-3 sm:px-4"
        @click="close()">

        {{-- Palette Container --}}
        <div @click.stop
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-xl bg-white dark:bg-[#12131a] rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col max-h-[75vh]">

            {{-- Top Search Bar --}}
            <div class="relative flex items-center px-4 py-3 border-b border-zinc-100 dark:border-zinc-800/80">
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
                    placeholder="{{ __('Type a title, author, category, or command...') }}"
                    class="w-full bg-transparent border-0 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-0 p-0">

                <button @click="close()"
                    class="px-1.5 py-0.5 text-[10px] font-mono text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded transition ml-2 rtl:ml-0 rtl:mr-2">
                    ESC
                </button>
            </div>

            {{-- Results / Command List --}}
            <div class="overflow-y-auto p-2 divide-y divide-zinc-100 dark:divide-zinc-800/60" x-ref="resultsList">

                {{-- Loading Skeleton --}}
                <template x-if="loading">
                    <div class="p-3 space-y-2">
                        <div class="h-10 bg-zinc-100 dark:bg-zinc-800/60 rounded-lg animate-pulse"></div>
                        <div class="h-10 bg-zinc-100 dark:bg-zinc-800/60 rounded-lg animate-pulse"></div>
                        <div class="h-10 bg-zinc-100 dark:bg-zinc-800/60 rounded-lg animate-pulse"></div>
                    </div>
                </template>

                {{-- Book Search Results --}}
                <template x-if="!loading && query.trim() !== '' && results.length > 0">
                    <div class="space-y-1">
                        <div class="px-3 py-1.5 text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-semibold">
                            Books & Catalog Matches (<span x-text="results.length"></span>)
                        </div>
                        <template x-for="(item, idx) in results" :key="item.id">
                            <a :href="item.url"
                                @mouseenter="selectedIndex = idx"
                                :class="{ 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white': selectedIndex === idx }"
                                class="flex items-center justify-between px-3 py-2 rounded-lg text-xs transition group">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse min-w-0">
                                    <div class="w-8 h-10 rounded bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex-shrink-0 overflow-hidden flex items-center justify-center">
                                        <img :src="item.cover" :alt="item.title" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="truncate">
                                        <p class="font-serif font-bold truncate text-zinc-900 dark:text-zinc-100" x-text="item.title"></p>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">
                                            by <span x-text="item.author"></span> &bull; <span x-text="item.category"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 rtl:space-x-reverse ml-3 flex-shrink-0">
                                    <span class="font-serif font-bold text-zinc-900 dark:text-white" x-text="item.price"></span>
                                    <span class="text-[10px] text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-200 transition">↵</span>
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

                {{-- Default Navigation & Actions (Shown when query is empty or matches commands) --}}
                <template x-if="query.trim() === ''">
                    <div class="space-y-4 p-1">
                        {{-- Navigation Group --}}
                        <div>
                            <div class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-semibold">
                                Navigation & Storefront
                            </div>
                            <div class="space-y-0.5 mt-1">
                                <a href="{{ route('books.index') }}"
                                    class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <span class="text-sm">📚</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">Explore All Books</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400 font-mono">/books</span>
                                </a>

                                <a href="{{ route('cart.index') }}"
                                    class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <span class="text-sm">🛍️</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">Shopping Bag</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400 font-mono">/cart</span>
                                </a>

                                <a href="{{ route('wishlist.index') }}"
                                    class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <span class="text-sm">❤️</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">Saved Volumes</span>
                                    </div>
                                    <span class="text-[11px] text-zinc-400 font-mono">/wishlist</span>
                                </a>

                                @auth
                                    <a href="{{ route('orders.index') }}"
                                        class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <span class="text-sm">📦</span>
                                            <span class="font-medium text-zinc-900 dark:text-white">Order History & Invoices</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 font-mono">/my-orders</span>
                                    </a>
                                @endauth
                            </div>
                        </div>

                        {{-- Preferences Group --}}
                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                            <div class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-semibold">
                                Preferences & Controls
                            </div>
                            <div class="space-y-0.5 mt-1">
                                <button type="button" @click="toggleTheme()"
                                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition text-left rtl:text-right">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <span class="text-sm">🌓</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">Toggle Dark / Light Theme</span>
                                    </div>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 font-mono text-zinc-500">Theme</span>
                                </button>

                                <div class="grid grid-cols-3 gap-1.5 pt-1">
                                    <a href="{{ route('locale.switch', 'en') }}"
                                        class="flex items-center justify-center space-x-1.5 px-2 py-1.5 rounded-lg text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'en' ? 'bg-zinc-100 dark:bg-zinc-800 font-bold' : '' }}">
                                        <span>🇬🇧</span>
                                        <span>English</span>
                                    </a>
                                    <a href="{{ route('locale.switch', 'fr') }}"
                                        class="flex items-center justify-center space-x-1.5 px-2 py-1.5 rounded-lg text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'fr' ? 'bg-zinc-100 dark:bg-zinc-800 font-bold' : '' }}">
                                        <span>🇫🇷</span>
                                        <span>Français</span>
                                    </a>
                                    <a href="{{ route('locale.switch', 'ar') }}"
                                        class="flex items-center justify-center space-x-1.5 px-2 py-1.5 rounded-lg text-xs border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ app()->getLocale() === 'ar' ? 'bg-zinc-100 dark:bg-zinc-800 font-bold' : '' }}">
                                        <span>🇸🇦</span>
                                        <span>العربية</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Actions Group (If user is Admin) --}}
                        @if (auth()->check() && auth()->user()->is_admin)
                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                                <div class="px-2.5 py-1 text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-semibold">
                                    Store Administration (Admin)
                                </div>
                                <div class="space-y-0.5 mt-1">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <span class="text-sm">📊</span>
                                            <span class="font-medium text-zinc-900 dark:text-white">Admin Operations & Analytics</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 font-mono">⌘ Dashboard</span>
                                    </a>

                                    <a href="{{ route('admin.books.create') }}"
                                        class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <span class="text-sm">➕</span>
                                            <span class="font-medium text-zinc-900 dark:text-white">Catalog: Add New Book</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 font-mono">New Book</span>
                                    </a>

                                    <a href="{{ route('admin.orders.index') }}"
                                        class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition">
                                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                            <span class="text-sm">🧾</span>
                                            <span class="font-medium text-zinc-900 dark:text-white">Orders & Fulfillment Audit</span>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 font-mono">Orders</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </template>

            </div>

            {{-- Footer Command Bar Keyboard Hints --}}
            <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-900/60 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-400">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <span class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/80 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↑</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/80 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↓</kbd>
                        <span>Navigate</span>
                    </span>
                    <span class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-200/80 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">↵</kbd>
                        <span>Select</span>
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-mono text-zinc-400">BookStore &bull; Command Palette</span>
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

            open() {
                this.isOpen = true;
                this.query = '';
                this.results = [];
                this.selectedIndex = 0;
                this.$nextTick(() => {
                    this.$refs.searchInput.focus();
                });
            },

            close() {
                this.isOpen = false;
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
