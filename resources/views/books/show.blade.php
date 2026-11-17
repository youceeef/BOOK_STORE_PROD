<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-8 overflow-hidden">
            <a href="{{ route('home') }}" class="hover:text-gold-600 transition flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Home
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('books.index') }}" class="hover:text-gold-600 transition">Books</a>
            <span class="text-gray-300">/</span>
            @if ($book->category)
                <a href="{{ route('categories.show', $book->category->slug) }}" class="hover:text-gold-600 transition truncate">
                    {{ $book->category->name }}
                </a>
                <span class="text-gray-300">/</span>
            @endif
            <span class="text-gray-900 font-semibold truncate">{{ $book->title }}</span>
        </nav>

        {{-- Book Details Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 p-6 sm:p-10 lg:p-14">
                
                {{-- Left: 3D Cover Visual Presentation --}}
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="relative w-full max-w-sm flex items-center justify-center p-4 sm:p-6 bg-gradient-to-b from-gray-50 via-white to-gray-50 rounded-2xl border border-gray-100">
                        {{-- Bestseller / Staff Pick Ribbon --}}
                        <div class="absolute top-4 left-4 z-20">
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
                        <div class="bg-[#faf9f7] p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider">Classification</span>
                            <span class="font-semibold text-gray-800">{{ $book->category->name ?? 'General Literature' }}</span>
                        </div>
                        <div class="bg-[#faf9f7] p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider">Standard Number</span>
                            <span class="font-mono font-semibold text-gray-800">{{ $book->isbn ?: 'Available on order' }}</span>
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        Only {{ $book->stock }} copies remaining
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                                        In Stock & Ready for Priority Dispatch
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    ✕ Out of Stock
                                </span>
                            @endif
                        </div>

                        {{-- Title & Author --}}
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-2">
                            {{ $book->title }}
                        </h1>
                        <p class="text-base sm:text-lg text-gray-600 mb-6">
                            Written by <span class="font-semibold text-gray-900">{{ $book->author }}</span>
                        </p>

                        {{-- Price Presentation --}}
                        <div class="py-4 border-y border-gray-100 flex items-baseline space-x-3 mb-6">
                            <span class="font-serif text-4xl font-bold text-gray-900">
                                ${{ number_format($book->price, 2) }}
                            </span>
                            <span class="text-xs text-gray-500">VAT & local taxes included</span>
                            <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                Free Delivery on orders over $35
                            </span>
                        </div>

                        {{-- Synopsis / Description --}}
                        <div class="prose prose-sm max-w-none text-gray-600 mb-8 leading-relaxed">
                            <h3 class="font-serif text-base font-bold text-gray-900 uppercase tracking-wider mb-2">
                                Publisher Synopsis
                            </h3>
                            <p class="whitespace-pre-line text-sm leading-relaxed">{{ $book->description ?: 'No official synopsis available for this release.' }}</p>
                        </div>
                    </div>

                    {{-- Add to Cart Form Section --}}
                    <div class="pt-6 border-t border-gray-100">
                        @if ($book->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">

                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                    {{-- Quantity Selector --}}
                                    <div class="flex items-center border border-gray-200 rounded-2xl bg-[#faf9f7] p-1 w-36 justify-between shadow-sm">
                                        <button type="button"
                                            onclick="let el = document.getElementById('qty-input'); if(el.value > 1) el.value--;"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-700 hover:text-black shadow-sm font-bold active:scale-95 transition">
                                            -
                                        </button>
                                        <input type="number" id="qty-input" name="quantity" value="1" min="1" max="{{ $book->stock }}"
                                            class="w-12 text-center bg-transparent border-0 font-bold text-gray-900 focus:ring-0 text-sm">
                                        <button type="button"
                                            onclick="let el = document.getElementById('qty-input'); if(el.value < {{ $book->stock }}) el.value++;"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-700 hover:text-black shadow-sm font-bold active:scale-95 transition">
                                            +
                                        </button>
                                    </div>

                                    {{-- Submit CTA Button --}}
                                    <button type="submit"
                                        class="flex-1 py-4 px-8 rounded-2xl text-base font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 transition-all duration-200 shadow-lg flex items-center justify-center space-x-3 active:scale-98">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span>Add to Shopping Bag</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center">
                                <p class="text-sm font-medium text-gray-500">This volume is currently out of stock. Check back soon.</p>
                            </div>
                        @endif

                        {{-- Reassurance Badges --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Authentic Publisher Print</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>Tracked & Bubble-Wrapped</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Stripe 256-Bit SSL Protection</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Related Recommendations Section --}}
        @if (isset($relatedBooks) && $relatedBooks->isNotEmpty())
            <div class="mt-16">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 mb-8">
                    <div>
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
                            More in {{ $book->category->name ?? 'This Shelf' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Curated companion reads selected for your library
                        </p>
                    </div>
                    @if ($book->category)
                        <a href="{{ route('categories.show', $book->category->slug) }}"
                            class="text-xs sm:text-sm font-semibold text-gold-600 hover:text-gold-700 transition flex items-center gap-1">
                            <span>Browse Shelf</span>
                            <span>→</span>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
                    @foreach ($relatedBooks as $related)
                        @include('partials._book_card', ['book' => $related])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
