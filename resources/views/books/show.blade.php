<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb Navigation: Quiet, minimal --}}
        <nav class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-zinc-500 dark:text-zinc-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-zinc-900 dark:hover:text-white transition">Home</a>
            <span>/</span>
            <a href="{{ route('books.index') }}" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Explore Books') }}</a>
            @if ($book->category)
                <span>/</span>
                <a href="{{ route('categories.show', $book->category->slug) }}" class="hover:text-zinc-900 dark:hover:text-white transition truncate">
                    {{ $book->category->name }}
                </a>
            @endif
            <span>/</span>
            <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate">{{ $book->title }}</span>
        </nav>

        {{-- Book Details: Grounded editorial layout --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 p-6 sm:p-10">
                
                {{-- Left: Cover Presentation & Specifications --}}
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="w-full aspect-[3/4] max-w-xs bg-zinc-100/70 dark:bg-zinc-900/50 rounded-lg p-6 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                        <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                            alt="{{ $book->title }} Cover"
                            class="max-h-full max-w-full object-contain shadow-md">
                    </div>

                    {{-- Specifications: Clean table format --}}
                    <div class="mt-6 w-full max-w-xs divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        <div class="py-2.5 flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ __('Classification') }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $book->category->name ?? 'General Literature' }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ __('Standard Number') }}</span>
                            <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $book->isbn ?: 'N/A' }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Availability</span>
                            <span class="font-medium {{ $book->stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400' }}">
                                {{ $book->stock > 0 ? __('In Stock') . " ({$book->stock})" : __('Out of Stock') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right: Content, Synopsis, & Order Actions --}}
                <div class="lg:col-span-7 flex flex-col justify-between">
                    <div>
                        {{-- Category Line --}}
                        <div class="flex items-center space-x-2 rtl:space-x-reverse mb-3">
                            @if ($book->category)
                                <a href="{{ route('categories.show', $book->category->slug) }}"
                                    class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                                    {{ $book->category->name }}
                                </a>
                            @endif
                        </div>

                        {{-- Title & Author --}}
                        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white leading-tight mb-2">
                            {{ $book->title }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                            by <span class="font-medium text-zinc-900 dark:text-zinc-200">{{ $book->author }}</span>
                        </p>

                        {{-- Price Presentation --}}
                        <div class="py-3 border-y border-zinc-100 dark:border-zinc-800 flex items-baseline space-x-3 rtl:space-x-reverse mb-6">
                            <span class="font-serif text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                                ${{ number_format($book->price, 2) }}
                            </span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">All local taxes included</span>
                        </div>

                        {{-- Synopsis / Description --}}
                        <div class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed mb-8">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">
                                {{ __('Publisher Synopsis') }}
                            </h3>
                            <p class="whitespace-pre-line">{{ $book->description ?: 'No official synopsis available for this volume.' }}</p>
                        </div>
                    </div>

                    {{-- Add to Cart Form Section with Live AJAX --}}
                    <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        @if ($book->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST" class="space-y-4"
                                x-data="{
                                    qty: 1,
                                    loading: false,
                                    added: false,
                                    async addToCart(e) {
                                        e.preventDefault();
                                        this.loading = true;
                                        try {
                                            const res = await fetch('{{ route('cart.store') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ book_id: {{ $book->id }}, quantity: this.qty })
                                            });
                                            const data = await res.json();
                                            if (res.ok && data.success) {
                                                this.added = true;
                                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cartCount } }));
                                                window.dispatchEvent(new CustomEvent('notify', {
                                                    detail: {
                                                        title: '{{ __('Added!') }}',
                                                        message: data.message,
                                                        type: 'success',
                                                        actionUrl: data.actionUrl,
                                                        actionLabel: data.actionLabel
                                                    }
                                                }));
                                                setTimeout(() => { this.added = false; }, 2000);
                                            } else {
                                                window.dispatchEvent(new CustomEvent('notify', {
                                                    detail: { title: 'Notice', message: data.message || 'Could not add to bag', type: 'error' }
                                                }));
                                            }
                                        } catch (err) {
                                            e.target.submit();
                                        } finally {
                                            this.loading = false;
                                        }
                                    }
                                }"
                                @submit="addToCart($event)">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">

                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                    {{-- Quantity Selector --}}
                                    <div class="flex items-center border border-zinc-200 dark:border-zinc-800 rounded-md bg-zinc-50 dark:bg-zinc-900 w-32 justify-between">
                                        <button type="button"
                                            @click="if(qty > 1) qty--"
                                            class="w-9 h-9 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:text-black dark:hover:text-white font-bold transition">
                                            -
                                        </button>
                                        <input type="number" id="qty-input" name="quantity" x-model="qty" min="1" max="{{ $book->stock }}"
                                            class="w-10 text-center bg-transparent border-0 font-medium text-zinc-900 dark:text-white focus:ring-0 text-sm p-0">
                                        <button type="button"
                                            @click="if(qty < {{ $book->stock }}) qty++"
                                            class="w-9 h-9 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:text-black dark:hover:text-white font-bold transition">
                                            +
                                        </button>
                                    </div>

                                    {{-- Submit CTA Button: Confident solid styling --}}
                                    <button type="submit" :disabled="loading"
                                        class="flex-1 py-2.5 px-6 rounded-md text-sm font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition flex items-center justify-center space-x-2 rtl:space-x-reverse disabled:opacity-75">
                                        <template x-if="!loading && !added">
                                            <span>{{ __('Add to Shopping Bag') }}</span>
                                        </template>
                                        <template x-if="loading">
                                            <span class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>{{ __('Adding...') }}</span>
                                            </span>
                                        </template>
                                        <template x-if="added">
                                            <span class="inline-flex items-center space-x-1.5 rtl:space-x-reverse font-semibold">
                                                <span>✓</span>
                                                <span>{{ __('Added!') }}</span>
                                            </span>
                                        </template>
                                    </button>

                                    {{-- Wishlist Toggle Button --}}
                                    @php
                                        $isWishlistedShow = $book->isWishlistedBy(Auth::user());
                                    @endphp
                                    <div x-data="{
                                        saved: {{ $isWishlistedShow ? 'true' : 'false' }},
                                        toggling: false,
                                        async toggleWishlist() {
                                            if (this.toggling) return;
                                            this.toggling = true;
                                            try {
                                                const res = await fetch('{{ route('wishlist.toggle') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ book_id: {{ $book->id }} })
                                                });
                                                const data = await res.json();
                                                if (data.success) {
                                                    this.saved = data.attached;
                                                    window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                                    window.dispatchEvent(new CustomEvent('notify', {
                                                        detail: {
                                                            title: this.saved ? '{{ __('Saved to Wishlist') }}' : '{{ __('Removed') }}',
                                                            message: data.message,
                                                            type: this.saved ? 'success' : 'info'
                                                        }
                                                    }));
                                                }
                                            } catch (e) {
                                                console.error(e);
                                            } finally {
                                                this.toggling = false;
                                            }
                                        }
                                    }">
                                        <button type="button" @click="toggleWishlist()" :disabled="toggling"
                                            class="h-10 px-4 rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] hover:bg-zinc-50 dark:hover:bg-zinc-800 text-xs font-medium text-zinc-700 dark:text-zinc-300 transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse"
                                            :title="saved ? '{{ __('Remove from Saved') }}' : '{{ __('Save Volume') }}'">
                                            <svg class="w-4 h-4 transition-transform active:scale-125" :class="saved ? 'text-rose-500 fill-current' : 'text-zinc-400 fill-none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span x-text="saved ? '{{ __('Saved') }}' : '{{ __('Save') }}'"></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="space-y-3">
                                <div class="p-3 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center">
                                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('This volume is currently out of stock.') }}</p>
                                </div>

                                {{-- Wishlist Toggle even when out of stock --}}
                                @php
                                    $isWishlistedShow = $book->isWishlistedBy(Auth::user());
                                @endphp
                                <div x-data="{
                                    saved: {{ $isWishlistedShow ? 'true' : 'false' }},
                                    toggling: false,
                                    async toggleWishlist() {
                                        if (this.toggling) return;
                                        this.toggling = true;
                                        try {
                                            const res = await fetch('{{ route('wishlist.toggle') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ book_id: {{ $book->id }} })
                                            });
                                            const data = await res.json();
                                            if (data.success) {
                                                this.saved = data.attached;
                                                window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                                window.dispatchEvent(new CustomEvent('notify', {
                                                    detail: {
                                                        title: this.saved ? '{{ __('Saved to Wishlist') }}' : '{{ __('Removed') }}',
                                                        message: data.message,
                                                        type: this.saved ? 'success' : 'info'
                                                    }
                                                }));
                                            }
                                        } catch (e) {
                                            console.error(e);
                                        } finally {
                                            this.toggling = false;
                                        }
                                    }
                                }">
                                    <button type="button" @click="toggleWishlist()" :disabled="toggling"
                                        class="w-full py-2.5 px-4 rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] hover:bg-zinc-50 dark:hover:bg-zinc-800 text-xs font-medium text-zinc-700 dark:text-zinc-300 transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                                        <svg class="w-4 h-4 transition-transform active:scale-125" :class="saved ? 'text-rose-500 fill-current' : 'text-zinc-400 fill-none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span x-text="saved ? '{{ __('Saved in Wishlist') }}' : '{{ __('Save to Wishlist for Restock') }}'"></span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Quiet Assurance Strip --}}
                        <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex flex-wrap items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                            <span>✓ {{ __('Free Delivery on orders over $35') }}</span>
                            <span>✓ {{ __('Tracked & Bubble-Wrapped') }}</span>
                            <span>✓ 14-Day Return Policy</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>