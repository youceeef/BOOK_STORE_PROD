{{-- resources/views/partials/_book_card.blade.php --}}
@props(['book'])

<div class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100/80 hover:border-gold-300 flex flex-col justify-between hover:-translate-y-1">
    <div>
        {{-- Cover Image Container with Book Depth Effect --}}
        <div class="relative overflow-hidden aspect-[3/4] bg-gradient-to-tr from-gray-100 to-gray-50 flex items-center justify-center p-4">
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
                    class="absolute top-3 left-3 bg-[#1a1a2e]/90 backdrop-blur-md text-gold-400 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-gold-500/30 hover:bg-[#1a1a2e] transition shadow-sm z-10">
                    {{ $book->category->name }}
                </a>
            @endif

            {{-- Floating Stock Status Badge --}}
            <div class="absolute top-3 right-3 z-10">
                @if ($book->stock > 0)
                    @if ($book->stock <= 5)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/95 text-white backdrop-blur-sm shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-white mr-1 animate-pulse"></span>
                            Only {{ $book->stock }} Left
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-600/90 text-white backdrop-blur-sm shadow-sm">
                            In Stock
                        </span>
                    @endif
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-600/90 text-white backdrop-blur-sm shadow-sm">
                        Sold Out
                    </span>
                @endif
            </div>
        </div>

        {{-- Book Metadata --}}
        <div class="p-5 flex flex-col">
            <p class="text-[11px] uppercase tracking-wider text-gray-500 font-medium mb-1 truncate">
                {{ $book->author }}
            </p>

            <a href="{{ route('books.show', $book->slug) }}" class="group-hover:text-gold-600 transition-colors">
                <h3 class="font-serif font-bold text-gray-900 text-base leading-snug line-clamp-2 min-h-[2.75rem]" title="{{ $book->title }}">
                    {{ $book->title }}
                </h3>
            </a>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-serif text-xl font-bold text-gray-900">
                    ${{ number_format($book->price, 2) }}
                </span>
                @if ($book->isbn)
                    <span class="text-[10px] text-gray-400 font-mono tracking-tight" title="ISBN: {{ $book->isbn }}">
                        ISBN {{ Str::limit($book->isbn, 8) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions / Add to Cart --}}
    <div class="p-5 pt-0 mt-auto">
        @if ($book->stock > 0)
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 hover:to-gold-400 transition-all duration-200 shadow-sm flex items-center justify-center space-x-2 active:scale-95 group/btn">
                    <svg class="w-4 h-4 transform group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Add to Bag</span>
                </button>
            </form>
        @else
            <button disabled
                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 cursor-not-allowed">
                Out of Stock
            </button>
        @endif
    </div>
</div>
