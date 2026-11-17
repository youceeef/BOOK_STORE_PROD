<x-app-layout>
    {{-- Category Header: Clean, editorial, matches books.index --}}
    <header class="bg-zinc-50 dark:bg-[#0c0d12] border-b border-zinc-200 dark:border-zinc-800/80 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-3">
            <nav class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('home') }}" class="hover:text-zinc-900 dark:hover:text-white transition">Home</a>
                <span>/</span>
                <a href="{{ route('books.index') }}" class="hover:text-zinc-900 dark:hover:text-white transition">{{ __('Browse Genres') }}</a>
                <span>/</span>
                <span class="text-zinc-800 dark:text-zinc-200 font-medium">{{ $category->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 pt-1">
                <div>
                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white tracking-tight">
                        {{ $category->name }}
                    </h1>
                </div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $books->total() }} {{ Str::plural('volume', $books->total()) }} in this shelf
                </span>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($books->isEmpty())
            <div class="text-center py-16 bg-white dark:bg-[#12131a] rounded-lg border border-dashed border-zinc-200 dark:border-zinc-800 p-8">
                <h3 class="font-serif text-lg font-bold text-zinc-900 dark:text-white mb-1">No Titles in this Category</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mb-4">
                    Our curators are currently preparing new acquisitions for this section.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                    {{ __('Explore Books') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($books as $book)
                    @include('partials._book_card', ['book' => $book])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</x-app-layout>