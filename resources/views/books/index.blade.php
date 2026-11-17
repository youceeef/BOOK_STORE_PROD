<x-app-layout>
    {{-- Hero Banner --}}
    @if (!request('search') && !request('category') && !request('sort') && !request('in_stock'))
        <section class="bg-gradient-to-b from-[#1a1a2e] via-[#1f2139] to-[#252849] text-white py-16 px-4 sm:px-6 lg:px-8 border-b border-navy-800 relative overflow-hidden">
            {{-- Decorative Background Elements --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-gold-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-5xl mx-auto text-center relative z-10 space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-widest bg-gold-500/15 text-gold-300 border border-gold-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-400"></span>
                    Curated Editions & Rare Finds
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white leading-tight">
                    Where Great Minds Meet Timeless Words
                </h1>

                <p class="max-w-2xl mx-auto text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                    Explore an exquisite collection of literary masterpieces, contemporary non-fiction, philosophy, and technical treatises bound for your library.
                </p>

                {{-- Hero Search Form --}}
                <div class="max-w-2xl mx-auto pt-2">
                    <form action="{{ route('books.index') }}" method="GET" class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by title, author, or ISBN reference..."
                            class="w-full pl-12 pr-32 py-4 bg-white/10 backdrop-blur-md text-white placeholder-gray-400 border border-white/20 rounded-2xl shadow-xl focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gold-500 transition-all text-sm sm:text-base">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-gold-500 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button type="submit"
                            class="absolute right-2 top-2 bottom-2 px-6 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 hover:to-gold-400 shadow-md transition flex items-center justify-center">
                            Search
                        </button>
                    </form>
                </div>

                {{-- Category Quick Filters --}}
                <div class="pt-3 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-xs text-gray-400 font-medium mr-1">Popular Shelves:</span>
                    @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->take(5)->get()) as $cat)
                        <a href="{{ route('books.index', ['category' => $cat->slug]) }}"
                            class="text-xs px-3.5 py-1.5 rounded-full bg-white/5 hover:bg-white/15 text-gray-200 hover:text-gold-300 transition border border-white/10">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Controls Bar: Filtering, Sorting, Context --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-8 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                {{-- Heading & Active Filter Notice --}}
                <div>
                    <h2 class="font-serif text-2xl font-bold text-gray-900 flex items-center gap-2">
                        @if (request('search'))
                            Search Results for <span class="text-gold-600">"{{ request('search') }}"</span>
                        @elseif (request('category'))
                            @php $activeCategory = \App\Models\Category::where('slug', request('category'))->first(); @endphp
                            Shelf: <span class="text-gold-600">{{ $activeCategory->name ?? ucfirst(request('category')) }}</span>
                        @else
                            The Book Collection
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Showing {{ $books->total() }} {{ Str::plural('title', $books->total()) }} in this view
                    </p>
                </div>

                {{-- Filter & Sort Form --}}
                <form id="filter-form" action="{{ route('books.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    {{-- Category Select Filter --}}
                    <div>
                        <select name="category" onchange="document.getElementById('filter-form').submit();"
                            class="text-xs font-medium rounded-xl border-gray-200 bg-[#faf9f7] text-gray-700 py-2 pl-3 pr-8 focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            <option value="">All Categories</option>
                            @foreach (cache()->remember('active_categories_all', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- In Stock Only Toggle --}}
                    <label class="flex items-center space-x-2 text-xs text-gray-700 cursor-pointer bg-[#faf9f7] border border-gray-200 rounded-xl px-3 py-2 select-none hover:bg-gray-100/60 transition">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') == '1' ? 'checked' : '' }}
                            onchange="document.getElementById('filter-form').submit();"
                            class="rounded border-gray-300 text-gold-600 focus:ring-gold-500 h-4 w-4">
                        <span>In Stock Only</span>
                    </label>

                    {{-- Sort Dropdown --}}
                    <div>
                        <select name="sort" onchange="document.getElementById('filter-form').submit();"
                            class="text-xs font-medium rounded-xl border-gray-200 bg-[#faf9f7] text-gray-700 py-2 pl-3 pr-8 focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Newest Releases</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title: A to Z</option>
                        </select>
                    </div>

                    {{-- Reset / Clear Filters Pill --}}
                    @if (request('search') || request('category') || request('sort') || request('in_stock'))
                        <a href="{{ route('books.index') }}"
                            class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 px-3 py-2 rounded-xl border border-rose-200 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Book Listing Grid --}}
        <div>
            @if ($books->isEmpty())
                <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200 p-8 shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gold-50 text-gold-500 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-gray-900 mb-2">No Volumes Found</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                        We couldn't locate any titles matching your current selection. Try clearing your filters or search terms.
                    </p>
                    <a href="{{ route('books.index') }}"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
                        View Entire Collection
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
                    @foreach ($books as $book)
                        @include('partials._book_card', ['book' => $book])
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-12">
                    {{ $books->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
