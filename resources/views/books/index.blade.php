<x-app-layout>
    {{-- Editorial Hero Header: Grounded, typographic, zero gradient slop --}}
    @if (!request('search') && !request('category') && !request('sort') && !request('in_stock'))
        <header class="bg-zinc-50 dark:bg-[#0c0d12] border-b border-zinc-200 dark:border-zinc-800/80 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-2xl space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                        {{ __('Curated Editions & Rare Finds') }}
                    </p>
                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-zinc-900 dark:text-white leading-tight">
                        {{ __('Where Great Minds Meet Timeless Words') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 font-normal leading-relaxed pt-1">
                        {{ __('Discover Stories That Stand The Test Of Time') }}
                    </p>
                </div>

                {{-- Shelf Quick Selectors --}}
                <div class="flex flex-wrap items-center gap-1.5 self-start md:self-end">
                    @foreach (cache()->remember('active_categories', 3600, fn() => \App\Models\Category::where('active', 1)->take(4)->get()) as $cat)
                        <a href="{{ route('books.index', ['category' => $cat->slug]) }}"
                            class="text-xs px-3 py-1.5 rounded-md bg-white dark:bg-[#12131a] hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 transition">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </header>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Controls Bar: Filtering, Sorting, Scope --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-4 border border-zinc-200 dark:border-zinc-800 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            {{-- Title & Count --}}
            <div>
                <h2 class="font-serif text-xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    @if (request('search'))
                        {{ __('Search Results for') }} <span class="italic text-zinc-600 dark:text-zinc-400">"{{ request('search') }}"</span>
                    @elseif (request('category'))
                        @php $activeCategory = \App\Models\Category::where('slug', request('category'))->first(); @endphp
                        {{ __('Shelf') }}: <span>{{ $activeCategory->name ?? ucfirst(request('category')) }}</span>
                    @else
                        {{ __('The Book Collection') }}
                    @endif
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    {{ __('Showing') }} {{ $books->total() }} {{ __('volumes') }}
                </p>
            </div>

            {{-- Filter & Sort Controls --}}
            <form id="filter-form" action="{{ route('books.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                {{-- Category Filter --}}
                <select name="category" onchange="document.getElementById('filter-form').submit();"
                    class="text-xs font-medium rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 py-1.5 pl-2.5 pr-7 rtl:pl-7 rtl:pr-2.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400 focus:border-zinc-900">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach (cache()->remember('active_categories_all', 3600, fn() => \App\Models\Category::where('active', 1)->orderBy('name')->get()) as $category)
                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                {{-- In Stock Filter --}}
                <label class="flex items-center space-x-1.5 rtl:space-x-reverse cursor-pointer bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-2.5 py-1.5 select-none hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') == '1' ? 'checked' : '' }}
                        onchange="document.getElementById('filter-form').submit();"
                        class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-0 h-3.5 w-3.5">
                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('In Stock Only') }}</span>
                </label>

                {{-- Sort Dropdown --}}
                <select name="sort" onchange="document.getElementById('filter-form').submit();"
                    class="text-xs font-medium rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 py-1.5 pl-2.5 pr-7 rtl:pl-7 rtl:pr-2.5 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400 focus:border-zinc-900">
                    <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>{{ __('Newest Releases') }}</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>{{ __('Title: A to Z') }}</option>
                </select>

                {{-- Reset Filter Pill --}}
                @if (request('search') || request('category') || request('sort') || request('in_stock'))
                    <a href="{{ route('books.index') }}"
                        class="font-medium text-rose-600 dark:text-rose-400 hover:text-rose-700 bg-rose-50 dark:bg-rose-950/30 px-2.5 py-1.5 rounded-md border border-rose-200 dark:border-rose-900 transition flex items-center gap-1">
                        <span>✕</span>
                        <span>{{ __('Reset') }}</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- Book Listing Grid --}}
        <div>
            @if ($books->isEmpty())
                <div class="text-center py-16 bg-white dark:bg-[#12131a] rounded-lg border border-dashed border-zinc-200 dark:border-zinc-800 p-8">
                    <h3 class="font-serif text-lg font-bold text-zinc-900 dark:text-white mb-1">{{ __('No Volumes Found') }}</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mb-4">
                        We couldn't locate any titles matching your current selection.
                    </p>
                    <a href="{{ route('books.index') }}"
                        class="inline-flex items-center px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 dark:bg-white dark:text-zinc-900 hover:bg-black transition">
                        {{ __('View Entire Collection') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($books as $book)
                        @include('partials._book_card', ['book' => $book])
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-10">
                    {{ $books->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>