<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center space-x-2 rtl:space-x-reverse text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-8 overflow-hidden">
            <a href="{{ route('home') }}" class="hover:text-gold-600 dark:hover:text-gold-400 transition flex items-center">
                <svg class="w-3.5 h-3.5 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Home</span>
            </a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <a href="{{ route('books.index') }}" class="hover:text-gold-600 dark:hover:text-gold-400 transition">{{ __('Explore Books') }}</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            @if ($book->category)
                <a href="{{ route('categories.show', $book->category->slug) }}" class="hover:text-gold-600 dark:hover:text-gold-400 transition truncate">
                    {{ $book->category->name }}
                </a>
                <span class="text-gray-300 dark:text-gray-600">/</span>
            @endif
            <span class="text-gray-900 dark:text-gray-100 font-semibold truncate">{{ $book->title }}</span>
        </nav>

        {{-- Book Details Card --}}
        <div class="bg-white dark:bg-darkcard rounded-3xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 p-6 sm:p-10 lg:p-14">
                
                {{-- Left: 3D Cover Visual Presentation --}}
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="relative w-full max-w-sm flex items-center justify-center p-4 sm:p-6 bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-navy-950 dark:via-darkcard dark:to-navy-950 rounded-2xl border border-gray-100 dark:border-darkborder">
                        {{-- Bestseller / Staff Pick Ribbon --}}
                        <div class="absolute top-4 left-4 rtl:left-auto rtl:right-4 z-20">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#1a1a2e] text-gold-400 border border-gold-500/30 shadow-md">
                                ★ Staff Selection
                            </span>
                        </div>

                        {{-- 3D Book Visual with Depth Shadow --}}
                        <div class="w-full aspect-[3/4] max-w-[280px] rounded-xl overflow-hidden book-depth-shadow relative transform hover:scale-[1.02] transition-transform duration-300">
                            <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                                alt="{{ $book->title }} Cover"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Quick Specs Grid --}}
                    <div class="mt-6 w-full max-w-sm grid grid-cols-2 gap-3 text-center text-xs">
                        <div class="bg-[#faf9f7] dark:bg-navy-950 p-3 rounded-xl border border-gray-100 dark:border-navy-800">
                            <span class="text-gray-400 dark:text-gray-500 block mb-0.5 text-[11px] uppercase tracking-wider">{{ __('Classification') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $book->category->name ?? 'General Literature' }}</span>
                        </div>
                        <div class="bg-[#faf9f7] dark:bg-navy-950 p-3 rounded-xl border border-gray-100 dark:border-navy-800">
                            <span class="text-gray-400 dark:text-gray-500 block mb-0.5 text-[11px] uppercase tracking-wider">{{ __('Standard Number') }}</span>
                            <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $book->isbn ?: 'Available on order' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Content & Purchasing --}}
                <div class="lg:col-span-7 flex flex-col justify-between">
                    <div>
                        {{-- Category & Stock Pills --}}
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            @if ($book->category)
                                <a href="{{ route('categories.show', $book->category->slug) }}"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#1a1a2e] text-gold-400 hover:bg-[#252849] transition border border-gold-500/20">
                                    {{ $book->category->name }}
                                </a>
                            @endif

                            @if ($book->stock > 0)
                                @if ($book->stock <= 5)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 rtl:mr-0 rtl:ml-1.5 animate-pulse"></span>
                                        {{ __('Only :count Left', ['count' => $book->stock]) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 rtl:mr-0 rtl:ml-1.5"></span>
                                        {{ __('In Stock') }}
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    ✕ {{ __('Out of Stock') }}
                                </span>
                            @endif
                        </div>

                        {{-- Title & Author --}}
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-gray-100 leading-tight mb-2">
                            {{ $book->title }}
                        </h1>
                        <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 mb-6">
                            Written by <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $book->author }}</span>
                        </p>

                        {{-- Price Presentation --}}
                        <div class="py-4 border-y border-gray-100 dark:border-darkborder flex flex-wrap items-baseline gap-3 mb-6">
                            <span class="font-serif text-4xl font-bold text-gray-900 dark:text-gray-100">
                                ${{ number_format($book->price, 2) }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">VAT & local taxes included</span>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                                {{ __('Free Delivery on orders over $35') }}
                            </span>
                        </div>

                        {{-- Synopsis / Description --}}
                        <div class="prose prose-sm max-w-none text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                            <h3 class="font-serif text-base font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-2">
                                {{ __('Publisher Synopsis') }}
                            </h3>
                            <p class="whitespace-pre-line text-sm leading-relaxed">{{ $book->description ?: 'No official synopsis available for this release.' }}</p>
                        </div>
                    </div>

                    {{-- Add to Cart Form Section with Live AJAX & Micro-Interactions --}}
                    <div class="pt-6 border-t border-gray-100 dark:border-darkborder">
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
                                                        title: data.title || '{{ __('Added!') }}',
                                                        message: data.message,
                                                        type: 'success',
                                                        actionUrl: data.actionUrl,
                                                        actionLabel: data.actionLabel
                                                    }
                                                }));
                                                setTimeout(() => { this.added = false; }, 2500);
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

                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                    {{-- Quantity Selector --}}
                                    <div class="flex items-center border border-gray-200 dark:border-navy-800 rounded-2xl bg-[#faf9f7] dark:bg-navy-950 p-1 w-36 justify-between shadow-sm">
                                        <button type="button"
                                            @click="if(qty > 1) qty--"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white shadow-sm font-bold active:scale-95 transition">
                                            -
                                        </button>
                                        <input type="number" id="qty-input" name="quantity" x-model="qty" min="1" max="{{ $book->stock }}"
                                            class="w-12 text-center bg-transparent border-0 font-bold text-gray-900 dark:text-gray-100 focus:ring-0 text-sm">
                                        <button type="button"
                                            @click="if(qty < {{ $book->stock }}) qty++"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white shadow-sm font-bold active:scale-95 transition">
                                            +
                                        </button>
                                    </div>

                                    {{-- Submit CTA Button --}}
                                    <button type="submit" :disabled="loading"
                                        class="flex-1 py-4 px-8 rounded-2xl text-base font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 transition-all duration-200 shadow-lg flex items-center justify-center space-x-3 rtl:space-x-reverse active:scale-98 disabled:opacity-75">
                                        <template x-if="!loading && !added">
                                            <span class="inline-flex items-center space-x-2.5 rtl:space-x-reverse">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                                <span>{{ __('Add to Shopping Bag') }}</span>
                                            </span>
                                        </template>
                                        <template x-if="loading">
                                            <span class="inline-flex items-center space-x-2 rtl:space-x-reverse">
                                                <svg class="animate-spin h-5 w-5 text-[#1a1a2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>{{ __('Adding...') }}</span>
                                            </span>
                                        </template>
                                        <template x-if="added">
                                            <span class="inline-flex items-center space-x-2 rtl:space-x-reverse font-bold text-[#1a1a2e]">
                                                <svg class="w-5 h-5 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>{{ __('Added!') }}</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-navy-800 text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">This volume is currently out of stock. Check back soon.</p>
                            </div>
                        @endif

                        {{-- Reassurance Badges --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-darkborder grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Authentic Publisher Print') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>{{ __('Tracked & Bubble-Wrapped') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>{{ __('Stripe 256-Bit SSL Protection') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>