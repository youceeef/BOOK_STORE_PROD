<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ __('Saved Volumes') }}
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    {{ $count }} {{ Str::plural('title', $count) }} {{ __('saved for later') }}
                </p>
            </div>
            <a href="{{ route('books.index') }}" class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
                <span>←</span>
                <span>{{ __('Continue Browsing') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
            totalCount: {{ $count }},
            async removeItem(url, bookId) {
                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.totalCount = data.wishlistCount;
                        window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                title: '{{ __('Removed') }}',
                                message: data.message,
                                type: 'info'
                            }
                        }));
                        const row = document.getElementById('wishlist-item-' + bookId);
                        if (row) row.remove();
                        if (data.isEmpty) {
                            window.location.reload();
                        }
                    }
                } catch (e) {
                    window.location.reload();
                }
            },
            async moveToBag(url, bookId) {
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.totalCount = data.wishlistCount;
                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cartCount } }));
                        window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                title: '{{ __('Moved to Bag!') }}',
                                message: data.message,
                                type: 'success',
                                actionUrl: data.actionUrl,
                                actionLabel: data.actionLabel
                            }
                        }));
                        const row = document.getElementById('wishlist-item-' + bookId);
                        if (row) row.remove();
                        if (data.wishlistCount === 0) {
                            window.location.reload();
                        }
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                title: '{{ __('Notice') }}',
                                message: data.message,
                                type: 'error'
                            }
                        }));
                    }
                } catch (e) {
                    window.location.reload();
                }
            }
        }">

        @if ($books->isEmpty())
            {{-- Empty Wishlist State --}}
            <div class="max-w-md mx-auto text-center py-16 px-6 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h2 class="font-serif text-xl font-bold text-zinc-900 dark:text-white mb-1">{{ __('Your Wishlist is Empty') }}</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">
                    {{ __('Save volumes while browsing to keep track of books you want to acquire or read next.') }}
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                    {{ __('Explore The Library') }}
                </a>
            </div>
        @else
            <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-100 dark:divide-zinc-800 overflow-hidden">
                @foreach ($books as $book)
                    <div id="wishlist-item-{{ $book->id }}"
                        class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition hover:bg-zinc-50/50 dark:hover:bg-zinc-900/40">
                        {{-- Cover & Info --}}
                        <div class="flex items-center space-x-4 rtl:space-x-reverse flex-1 min-w-0">
                            <div class="w-16 h-22 flex-shrink-0 bg-zinc-100 dark:bg-zinc-900 rounded p-1 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                                <img src="{{ $book->cover_url }}"
                                    alt="{{ $book->title }}"
                                    width="64"
                                    height="88"
                                    loading="lazy"
                                    class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-medium text-zinc-400 uppercase tracking-wider block mb-0.5">
                                    {{ $book->category ? $book->category->name : 'Volume' }}
                                </span>
                                <a href="{{ route('books.show', $book->slug) }}"
                                    class="font-serif font-bold text-base text-zinc-900 dark:text-white hover:underline truncate block"
                                    title="{{ $book->title }}">
                                    {{ $book->title }}
                                </a>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $book->author }}
                                </p>
                                <div class="mt-2 flex items-center gap-2 text-xs">
                                    <span class="font-serif font-bold text-zinc-900 dark:text-white">
                                        ${{ number_format($book->price, 2) }}
                                    </span>
                                    <span>•</span>
                                    @if ($book->stock > 0)
                                        @if ($book->stock <= 5)
                                            <span class="text-amber-600 dark:text-amber-400 font-medium">
                                                {{ __('Only :count left', ['count' => $book->stock]) }}
                                            </span>
                                        @else
                                            <span class="text-zinc-700 dark:text-zinc-300 font-medium">
                                                {{ __('In Stock') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 font-medium">
                                            {{ __('Sold Out') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Actions: Move to Bag & Remove --}}
                        <div class="flex items-center space-x-3 rtl:space-x-reverse w-full sm:w-auto justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-zinc-100 dark:border-zinc-800">
                            @if ($book->stock > 0)
                                <button type="button"
                                    @click="moveToBag('{{ route('wishlist.move-to-bag', $book->id) }}', {{ $book->id }})"
                                    class="py-2 px-4 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                                    {{ __('Move to Bag') }}
                                </button>
                            @else
                                <button type="button" disabled
                                    class="py-2 px-4 rounded-md text-xs font-medium text-zinc-400 bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-600 cursor-not-allowed">
                                    {{ __('Sold Out') }}
                                </button>
                            @endif

                            <button type="button"
                                @click="removeItem('{{ route('wishlist.destroy', $book->id) }}', {{ $book->id }})"
                                class="p-2 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition"
                                title="{{ __('Remove from saved volumes') }}">
                                ✕
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between text-xs">
                <a href="{{ route('books.index') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                    ← {{ __('Browse more volumes') }}
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
