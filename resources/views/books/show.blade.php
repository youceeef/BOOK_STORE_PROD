<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
            readerOpen: false,
            fontSize: 'base',
            readerTheme: 'paper',
            scrollProgress: 0,
            updateProgress(e) {
                const el = e.target;
                const total = el.scrollHeight - el.clientHeight;
                this.scrollProgress = total > 0 ? Math.round((el.scrollTop / total) * 100) : 0;
            }
        }"
        @keydown.escape.window="readerOpen = false">
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

                    {{-- "Look Inside" Sample Reader Button --}}
                    @if ($book->hasSample())
                        <div class="mt-3.5 w-full max-w-xs">
                            <button type="button" @click="readerOpen = true"
                                class="w-full py-2.5 px-4 rounded-md text-xs font-medium text-zinc-900 dark:text-white bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition flex items-center justify-center space-x-2 rtl:space-x-reverse shadow-xs">
                                <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Look Inside (Sample Excerpt)</span>
                                <span class="text-[10px] text-zinc-400 dark:text-zinc-400 font-mono">~{{ $book->readingTime() }} min</span>
                            </button>
                        </div>
                    @endif

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

        {{-- ========================================================
             Verified Reader Reviews & Social Proof Section
             ======================================================== --}}
        <div class="mt-12 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-6 sm:p-10 mb-12"
            x-data="{
                showForm: false,
                rating: {{ $userReview ? $userReview->rating : 5 }},
                hoverRating: 0,
                submitting: false,
                async submitReview(e) {
                    e.preventDefault();
                    this.submitting = true;
                    const form = e.target;
                    const formData = new FormData(form);

                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    title: 'Review Published',
                                    message: data.message,
                                    type: 'success'
                                }
                            }));
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    title: 'Review Error',
                                    message: data.message || 'Please check your inputs.',
                                    type: 'error'
                                }
                            }));
                        }
                    } catch (err) {
                        console.error(err);
                        form.submit();
                    } finally {
                        this.submitting = false;
                    }
                }
            }">

            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="text-xs uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-1">Social Proof</span>
                    <h2 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Reader Reviews & Critique</h2>
                </div>

                @auth
                    <button type="button" @click="showForm = !showForm"
                        class="inline-flex items-center px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs self-start md:self-auto">
                        <svg class="w-3.5 h-3.5 mr-1.5 rtl:mr-0 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span x-text="showForm ? 'Cancel Review' : '{{ $userReview ? 'Edit Your Review' : 'Write a Review' }}'"></span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition self-start md:self-auto">
                        Sign in to review →
                    </a>
                @endauth
            </div>

            {{-- Aggregated Rating Overview & Histogram --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center pb-8 border-b border-zinc-100 dark:border-zinc-800/80 mb-8">
                {{-- Big Score --}}
                <div class="md:col-span-4 flex flex-col items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border border-zinc-200 dark:border-zinc-800 text-center">
                    <span class="font-serif text-5xl font-bold text-zinc-900 dark:text-white mb-2">
                        {{ $averageRating > 0 ? number_format($averageRating, 1) : '—' }}
                    </span>
                    <div class="flex items-center space-x-1 rtl:space-x-reverse text-amber-500 mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'fill-current' : 'text-zinc-300 dark:text-zinc-700 fill-current' }}" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                        {{ $reviewsCount }} {{ Str::plural('rating', $reviewsCount) }} recorded
                    </p>
                </div>

                {{-- Histogram Bars --}}
                <div class="md:col-span-8 space-y-2">
                    @foreach ($ratingBreakdown as $star => $data)
                        <div class="flex items-center text-xs space-x-3 rtl:space-x-reverse">
                            <span class="w-8 font-mono text-zinc-600 dark:text-zinc-400 text-right">{{ $star }} ★</span>
                            <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                                <div class="bg-zinc-900 dark:bg-white h-2 rounded-full transition-all duration-500" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="w-16 font-mono text-[11px] text-zinc-400 text-right">
                                {{ $data['percentage'] }}% ({{ $data['count'] }})
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Review Submission Form Drawer --}}
            @auth
                <div x-show="showForm" x-cloak class="mb-10 p-6 bg-zinc-50 dark:bg-zinc-900/60 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <h3 class="font-serif text-base font-bold text-zinc-900 dark:text-white mb-1">
                        {{ $userReview ? 'Update Your Review' : 'Share Your Review' }}
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-5">
                        Your honest feedback guides fellow readers.
                    </p>

                    @if ($isVerifiedBuyer)
                        <div class="mb-5 p-3 rounded-md bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center">
                            <span class="font-bold mr-1.5 rtl:mr-0 rtl:ml-1.5">✓ Verified Buyer:</span>
                            You purchased this volume through Lara Bookstore. Your critique will receive an official Verified Buyer badge.
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $book->slug) }}" method="POST" @submit="submitReview" class="space-y-4">
                        @csrf

                        {{-- Star Selection --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">Your Rating</label>
                            <input type="hidden" name="rating" :value="rating">
                            <div class="flex items-center space-x-1.5 rtl:space-x-reverse cursor-pointer">
                                <template x-for="i in 5" :key="i">
                                    <button type="button"
                                        @click="rating = i"
                                        @mouseenter="hoverRating = i"
                                        @mouseleave="hoverRating = 0"
                                        class="p-1 focus:outline-none transition-transform hover:scale-110">
                                        <svg class="w-6 h-6 transition-colors"
                                            :class="(hoverRating || rating) >= i ? 'text-amber-500 fill-current' : 'text-zinc-300 dark:text-zinc-700 fill-none'"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </template>
                                <span class="text-xs text-zinc-500 font-mono ml-2 rtl:ml-0 rtl:mr-2" x-text="`${hoverRating || rating} / 5 Stars`"></span>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label for="review_title" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">Review Headline (Optional)</label>
                            <input type="text" id="review_title" name="title"
                                value="{{ old('title', $userReview?->title) }}"
                                placeholder="e.g. Masterful prose, exquisite printing quality"
                                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] text-zinc-900 dark:text-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white">
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label for="review_comment" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">Your Critique / Thoughts</label>
                            <textarea id="review_comment" name="comment" rows="4" required minlength="5"
                                placeholder="Reflect on the author's writing, themes, paper weight, and translation fidelity..."
                                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] text-zinc-900 dark:text-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white">{{ old('comment', $userReview?->comment) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse pt-2">
                            <button type="button" @click="showForm = false"
                                class="px-3.5 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="submitting"
                                class="px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs disabled:opacity-75">
                                <span x-text="submitting ? 'Publishing...' : 'Publish Critique'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            @endauth

            {{-- Reviews Stream --}}
            <div class="space-y-6">
                @forelse ($book->reviews as $review)
                    <div class="p-5 rounded-lg border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-900/30 space-y-3"
                        x-data="{
                            helpfulCount: {{ $review->helpful_count }},
                            voted: false,
                            voting: false,
                            async voteHelpful() {
                                if (this.voted || this.voting) return;
                                this.voting = true;
                                try {
                                    const res = await fetch('{{ route('reviews.helpful', $review->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.helpfulCount = data.helpful_count;
                                        this.voted = true;
                                        window.dispatchEvent(new CustomEvent('notify', {
                                            detail: { title: 'Helpful Vote', message: data.message, type: 'success' }
                                        }));
                                    }
                                } catch (e) {
                                    console.error(e);
                                } finally {
                                    this.voting = false;
                                }
                            }
                        }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <div class="w-7 h-7 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center text-[11px] font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ strtoupper(substr($review->user->name ?? 'Reader', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-xs text-zinc-900 dark:text-white">{{ $review->user->name ?? 'Reader' }}</span>
                                    <span class="text-[11px] text-zinc-400 font-mono ml-1.5 rtl:ml-0 rtl:mr-1.5">• {{ $review->created_at->diffForHumans() }}</span>
                                </div>

                                @if ($review->is_verified_purchase)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        ✓ Verified Buyer
                                    </span>
                                @endif
                            </div>

                            {{-- Star Rating --}}
                            <div class="flex items-center space-x-1 rtl:space-x-reverse text-amber-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-zinc-300 dark:text-zinc-700 fill-current' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        @if ($review->title)
                            <h4 class="font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                {{ $review->title }}
                            </h4>
                        @endif

                        <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-line">
                            {{ $review->comment }}
                        </p>

                        <div class="pt-2 flex items-center justify-between text-xs text-zinc-400">
                            <button type="button" @click="voteHelpful()" :disabled="voting || voted"
                                class="inline-flex items-center space-x-1.5 rtl:space-x-reverse text-[11px] font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition"
                                :class="voted ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : ''">
                                <span>👍 Helpful</span>
                                <span class="font-mono" x-text="`(${helpfulCount})`"></span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-xs text-zinc-400">
                        <p class="mb-2">No reader reviews recorded for this volume yet.</p>
                        <p class="text-[11px] text-zinc-500">Be the first to share an authentic critique of this edition.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Related Volumes Grid --}}
        @if ($relatedBooks->isNotEmpty())
            <div class="mt-12">
                <div class="mb-6">
                    <span class="text-xs uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Complementary Works</span>
                    <h2 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">More in {{ $book->category->name }}</h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($relatedBooks as $relatedBook)
                        @include('partials._book_card', ['book' => $relatedBook])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ========================================================
             "Look Inside" Chapter Reader Modal (Alpine.js)
             ======================================================== --}}
        @if ($book->hasSample())
            <div x-show="readerOpen" x-cloak
                class="fixed inset-0 z-50 overflow-hidden bg-black/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <div @click.away="readerOpen = false"
                    :class="{
                        'bg-[#fbf7ee] text-[#2c2214] border-[#e8dfcf]': readerTheme === 'paper',
                        'bg-white text-zinc-900 border-zinc-200': readerTheme === 'light',
                        'bg-[#0e0f15] text-zinc-100 border-zinc-800': readerTheme === 'dark'
                    }"
                    class="relative w-full max-w-3xl h-[88vh] flex flex-col rounded-xl shadow-2xl overflow-hidden border transition-colors duration-200">

                    {{-- Reading Progress Bar --}}
                    <div class="h-1 bg-zinc-200/50 dark:bg-zinc-800/50 w-full flex-shrink-0">
                        <div class="h-1 bg-zinc-900 dark:bg-zinc-100 transition-all duration-150"
                            :style="'width: ' + scrollProgress + '%'"></div>
                    </div>

                    {{-- Modal Header & Reader Controls --}}
                    <div class="px-5 py-3 border-b flex items-center justify-between flex-shrink-0"
                        :class="{
                            'border-[#e8dfcf] bg-[#f7f0e3]': readerTheme === 'paper',
                            'border-zinc-200 bg-zinc-50': readerTheme === 'light',
                            'border-zinc-800 bg-zinc-900/60': readerTheme === 'dark'
                        }">
                        <div class="min-w-0 pr-4">
                            <span class="text-[10px] uppercase font-mono tracking-wider opacity-70 block">Sample Excerpt</span>
                            <h3 class="font-serif font-bold text-sm truncate">{{ $book->title }}</h3>
                        </div>

                        <div class="flex items-center space-x-2.5 rtl:space-x-reverse flex-shrink-0">
                            {{-- Theme Selector --}}
                            <div class="inline-flex rounded-md p-0.5 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs">
                                <button type="button" @click="readerTheme = 'paper'"
                                    :class="readerTheme === 'paper' ? 'bg-white shadow-xs font-semibold text-zinc-900' : 'opacity-70 hover:opacity-100'"
                                    class="px-2 py-0.5 rounded text-[11px] transition">
                                    Paper
                                </button>
                                <button type="button" @click="readerTheme = 'light'"
                                    :class="readerTheme === 'light' ? 'bg-white shadow-xs font-semibold text-zinc-900' : 'opacity-70 hover:opacity-100'"
                                    class="px-2 py-0.5 rounded text-[11px] transition">
                                    Clean
                                </button>
                                <button type="button" @click="readerTheme = 'dark'"
                                    :class="readerTheme === 'dark' ? 'bg-zinc-800 shadow-xs font-semibold text-white' : 'opacity-70 hover:opacity-100'"
                                    class="px-2 py-0.5 rounded text-[11px] transition">
                                    Midnight
                                </button>
                            </div>

                            {{-- Font Sizing Buttons --}}
                            <div class="inline-flex rounded-md p-0.5 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs">
                                <button type="button"
                                    @click="fontSize = fontSize === 'xl' ? 'lg' : (fontSize === 'lg' ? 'base' : 'sm')"
                                    title="Decrease font size"
                                    class="px-2 py-0.5 rounded text-[11px] opacity-70 hover:opacity-100 transition">
                                    A-
                                </button>
                                <button type="button"
                                    @click="fontSize = fontSize === 'sm' ? 'base' : (fontSize === 'base' ? 'lg' : 'xl')"
                                    title="Increase font size"
                                    class="px-2 py-0.5 rounded text-[11px] opacity-70 hover:opacity-100 transition">
                                    A+
                                </button>
                            </div>

                            {{-- Close Button --}}
                            <button type="button" @click="readerOpen = false"
                                class="w-7 h-7 rounded-md flex items-center justify-center opacity-70 hover:opacity-100 hover:bg-black/5 dark:hover:bg-white/10 transition">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Reader Scrollable Body --}}
                    <div class="flex-1 overflow-y-auto px-6 sm:px-14 py-8 sm:py-12 select-text"
                        @scroll="updateProgress($event)">
                        <div class="max-w-xl mx-auto space-y-6"
                            :class="{
                                'text-sm leading-relaxed': fontSize === 'sm',
                                'text-base leading-relaxed': fontSize === 'base',
                                'text-lg leading-loose': fontSize === 'lg',
                                'text-xl leading-loose': fontSize === 'xl'
                            }">

                            <div class="text-center pb-6 mb-6 border-b border-current/10">
                                <span class="text-[11px] uppercase font-mono tracking-widest opacity-60 block mb-1">Introductory Pages</span>
                                <h1 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight mb-2">{{ $book->title }}</h1>
                                <p class="text-xs opacity-75">by {{ $book->author }}</p>
                            </div>

                            @php
                                $paragraphs = array_filter(explode("\n\n", trim($book->sample_excerpt ?? '')));
                            @endphp

                            @foreach ($paragraphs as $index => $paragraph)
                                @if ($index === 0)
                                    <p class="font-serif first-letter:float-left first-letter:text-5xl first-letter:font-bold first-letter:mr-3 first-letter:leading-none">
                                        {{ $paragraph }}
                                    </p>
                                @else
                                    <p class="font-serif">
                                        {{ $paragraph }}
                                    </p>
                                @endif
                            @endforeach

                            {{-- End of Excerpt Banner --}}
                            <div class="mt-12 pt-8 border-t border-current/15 text-center space-y-4">
                                <p class="font-serif italic text-sm opacity-80">
                                    You have reached the conclusion of this free sample excerpt.
                                </p>

                                @if ($book->stock > 0)
                                    <form action="{{ route('cart.store') }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="inline-flex items-center px-5 py-2.5 rounded-md text-xs font-semibold text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                                            <span>Add Full Archival Volume to Bag (${{ number_format($book->price, 2) }})</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs opacity-75">This volume is currently sold out.</span>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
</x-app-layout>