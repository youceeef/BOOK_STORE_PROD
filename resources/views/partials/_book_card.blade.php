{{-- resources/views/partials/_book_card.blade.php --}}
@props(['book'])

<div class="group bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition flex flex-col justify-between overflow-hidden">
    <div>
        {{-- Cover Presentation: Clean frame, unoccluded artwork --}}
        <div class="block relative aspect-[3/4] bg-zinc-100/70 dark:bg-zinc-900/50 p-5 overflow-hidden">
            <a href="{{ route('books.show', $book->slug) }}" class="w-full h-full flex items-center justify-center">
                <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                    alt="{{ $book->title }}"
                    loading="lazy"
                    class="max-h-full max-w-full object-contain shadow-sm group-hover:scale-[1.02] transition-transform duration-300">
            </a>

            {{-- Wishlist Heart Button --}}
            @php
                $isWishlisted = $book->isWishlistedBy(Auth::user());
            @endphp
            <div class="absolute top-2.5 right-2.5 rtl:right-auto rtl:left-2.5 z-10"
                x-data="{
                    saved: {{ $isWishlisted ? 'true' : 'false' }},
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
                <button type="button"
                    @click="toggleWishlist()"
                    :disabled="toggling"
                    title="{{ __('Save volume') }}"
                    class="w-7 h-7 rounded-full bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-center text-zinc-400 hover:text-rose-500 hover:border-rose-300 transition shadow-xs focus:outline-none">
                    <svg class="w-3.5 h-3.5 transition-transform active:scale-125" :class="saved ? 'text-rose-500 fill-current' : 'text-zinc-400 hover:text-rose-500 fill-none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Book Metadata --}}
        <div class="p-4 flex flex-col">
            <div class="flex items-center justify-between text-[11px] mb-1.5">
                <span class="font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider truncate mr-2 rtl:mr-0 rtl:ml-2">
                    {{ $book->category ? $book->category->name : 'Volume' }}
                </span>
                
                {{-- Stock Status: Integrated inline, no radioactive pill --}}
                @if ($book->stock > 0)
                    @if ($book->stock <= 5)
                        <span class="text-amber-600 dark:text-amber-400 font-medium flex-shrink-0">
                            {{ __('Only :count Left', ['count' => $book->stock]) }}
                        </span>
                    @else
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium flex-shrink-0">
                            {{ __('In Stock') }}
                        </span>
                    @endif
                @else
                    <span class="text-zinc-400 dark:text-zinc-500 font-medium flex-shrink-0">
                        {{ __('Sold Out') }}
                    </span>
                @endif
            </div>

            <a href="{{ route('books.show', $book->slug) }}" class="block mb-1">
                <h3 class="font-serif font-bold text-zinc-900 dark:text-white text-base leading-snug line-clamp-2 hover:underline decoration-zinc-400" title="{{ $book->title }}">
                    {{ $book->title }}
                </h3>
            </a>

            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2 truncate">
                {{ $book->author }}
            </p>

            @php
                $cardAvg = $book->reviews_avg_rating ?? ($book->relationLoaded('reviews') ? $book->reviews->avg('rating') : null);
                $cardCount = $book->reviews_count ?? ($book->relationLoaded('reviews') ? $book->reviews->count() : 0);
            @endphp
            @if ($cardCount > 0 && $cardAvg)
                <div class="flex items-center space-x-1 rtl:space-x-reverse text-xs mb-3 text-amber-500">
                    <span class="text-[11px]">★</span>
                    <span class="font-medium text-xs text-zinc-800 dark:text-zinc-200">{{ number_format($cardAvg, 1) }}</span>
                    <span class="text-[11px] text-zinc-400">({{ $cardCount }})</span>
                </div>
            @else
                <div class="mb-3"></div>
            @endif

            <div class="flex items-baseline justify-between pt-1 border-t border-zinc-100 dark:border-zinc-800/80">
                <span class="font-serif text-lg font-bold text-zinc-900 dark:text-zinc-100">
                    ${{ number_format($book->price, 2) }}
                </span>
                @if ($book->isbn)
                    <span class="text-[10px] text-zinc-400 font-mono" title="ISBN: {{ $book->isbn }}">
                        {{ Str::limit($book->isbn, 10) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions: Solid, confident CTA with live feedback --}}
    <div class="p-4 pt-0 mt-auto">
        @if ($book->stock > 0)
            <form action="{{ route('cart.store') }}" method="POST"
                x-data="{
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
                                body: JSON.stringify({ book_id: {{ $book->id }}, quantity: 1 })
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
                <input type="hidden" name="quantity" value="1">
                <button type="submit" :disabled="loading"
                    class="w-full py-2 px-3 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-900 transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse disabled:opacity-75">
                    <template x-if="!loading && !added">
                        <span>{{ __('Add to Bag') }}</span>
                    </template>
                    <template x-if="loading">
                        <span class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('Adding...') }}</span>
                        </span>
                    </template>
                    <template x-if="added">
                        <span class="inline-flex items-center space-x-1 rtl:space-x-reverse text-emerald-300 dark:text-emerald-700 font-semibold">
                            <span>✓</span>
                            <span>{{ __('Added!') }}</span>
                        </span>
                    </template>
                </button>
            </form>
        @else
            <button disabled
                class="w-full py-2 px-3 rounded-md text-xs font-medium text-zinc-400 bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-500 cursor-not-allowed">
                {{ __('Out of Stock') }}
            </button>
        @endif
    </div>
</div>