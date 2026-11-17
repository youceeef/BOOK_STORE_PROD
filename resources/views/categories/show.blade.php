<x-app-layout>
    {{-- Category Header Banner --}}
    <section class="bg-gradient-to-b from-[#1a1a2e] to-[#252849] text-white py-14 px-4 sm:px-6 lg:px-8 border-b border-navy-800">
        <div class="max-w-7xl mx-auto space-y-4">
            <nav class="flex items-center space-x-2 text-xs text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-gold-400 transition">Home</a>
                <span>/</span>
                <a href="{{ route('books.index') }}" class="hover:text-gold-400 transition">Categories</a>
                <span>/</span>
                <span class="text-gold-400 font-semibold">{{ $category->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-gold-500/20 text-gold-400 border border-gold-500/30 mb-2">
                        Category Archive
                    </span>
                    <h1 class="font-serif text-3xl sm:text-5xl font-bold text-white tracking-tight">
                        {{ $category->name }}
                    </h1>
                </div>
                <span class="text-sm text-gray-400">
                    {{ $books->total() }} {{ Str::plural('title', $books->total()) }} indexed
                </span>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($books->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200 p-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gold-50 text-gold-500 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-bold text-gray-900 mb-2">No Titles in this Category</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                    Our curators are currently preparing new acquisitions for this section. Check back soon.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
                    Browse All Books
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
                @foreach ($books as $book)
                    @include('partials._book_card', ['book' => $book])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
