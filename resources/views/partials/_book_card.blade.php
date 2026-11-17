{{-- resources/views/partials/_book_card.blade.php --}}
@props(['book'])

<div class="group relative bg-white dark:bg-darkcard rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100/80 dark:border-darkborder hover:border-gold-300 dark:hover:border-gold-500/50 flex flex-col justify-between hover:-translate-y-1">
    <div>
        {{-- Cover Image Container with Book Depth Effect --}}
        <div class="relative overflow-hidden aspect-[3/4] bg-gradient-to-tr from-gray-100 to-gray-50 dark:from-navy-950 dark:to-navy-900 flex items-center justify-center p-4">
            <a href="{{ route('books.show', $book->slug) }}" class="block w-full h-full relative group/img">
                <div class="w-full h-full rounded-lg overflow-hidden book-depth-shadow transition-transform duration-500 group-hover:scale-[1.03]">
                    <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                        alt="{{ $book->title }}"
                        loading="lazy"
                        class="w-full h-full object-cover">
                </div>
            </a>

            {{-- Floating Category Badge --}}
            @if ($book->category)
                <a href="{{ route('categories.show', $book->category->slug) }}"
                    class="absolute top-3 left-3 rtl:left-auto rtl:right-3 bg-[#1a1a2e]/90 backdrop-blur-md text-gold-400 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-gold-500/30 hover:bg-[#1a1a2e] transition shadow-sm z-10">
                    {{ $book->category->name }}
                </a>
            @endif

            {{-- Floating Stock Status Badge --}}
            <div class="absolute top-3 right-3 rtl:right-auto rtl:left-3 z-10">
                @if ($book->stock > 0)
                    @if ($book->stock <= 5)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/95 text-white backdrop-blur-sm shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-white mr-1 rtl:mr-0 rtl:ml-1 animate-pulse"></span>
                            {{ __('Only :count Left', ['count' => $book->stock]) }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-600/90 text-white backdrop-blur-sm shadow-sm">
                            {{ __('In Stock') }}
                        </span>
                    @endif
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-600/90 text-white backdrop-blur-sm shadow-sm">
                        {{ __('Sold Out') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Book Metadata --}}
        <div class="p-5 flex flex-col">
            <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium mb-1 truncate">
                {{ $book->author }}
            </p>

            <a href="{{ route('books.show', $book->slug) }}" class="group-hover:text-gold-600 dark:group-hover:text-gold-400 transition-colors">
                <h3 class="font-serif font-bold text-gray-900 dark:text-gray-100 text-base leading-snug line-clamp-2 min-h-[2.75rem]" title="{{ $book->title }}">
                    {{ $book->title }}
                </h3>
            </a>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-serif text-xl font-bold text-gray-900 dark:text-gray-100">
                    ${{ number_format($book->price, 2) }}
                </span>
                @if ($book->isbn)
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-mono tracking-tight" title="ISBN: {{ $book->isbn }}">
                        ISBN {{ Str::limit($book->isbn, 8) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions / Add to Cart with Live AJAX & Micro-Interactions --}}
    <div class="p-5 pt-0 mt-auto">
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
                                        title: data.title || '{{ __('Added!') }}',
                                        message: data.message,
                                        type: 'success',
                                        actionUrl: data.actionUrl,
                                        actionLabel: data.actionLabel
                                    }
                                }));
                                setTimeout(() => { this.added = false; }, 2200);
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
                    class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 hover:to-gold-400 transition-all duration-200 shadow-sm flex items-center justify-center space-x-2 rtl:space-x-reverse active:scale-95 group/btn disabled:opacity-75">
                    <template x-if="!loading && !added">
                        <span class="inline-flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="w-4 h-4 transform group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>{{ __('Add to Bag') }}</span>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="inline-flex items-center space-x-2 rtl:space-x-reverse text-xs">
                            <svg class="animate-spin h-3.5 w-3.5 text-[#1a1a2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('Adding...') }}</span>
                        </span>
                    </template>
                    <template x-if="added">
                        <span class="inline-flex items-center space-x-1.5 rtl:space-x-reverse text-xs font-bold text-[#1a1a2e]">
                            <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('Added!') }}</span>
                        </span>
                    </template>
                </button>
            </form>
        @else
            <button disabled
                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 dark:bg-navy-900/60 dark:text-gray-500 cursor-not-allowed">
                {{ __('Out of Stock') }}
            </button>
        @endif
    </div>
</div>