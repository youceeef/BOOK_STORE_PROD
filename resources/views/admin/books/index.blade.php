<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-xs uppercase tracking-wider text-gold-600 font-bold block mb-1">Catalog Management</span>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Manage Books</h1>
            </div>
            <a href="{{ route('admin.books.create') }}"
                class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Book
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-[#faf9f7]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($books as $book)
                            <tr class="hover:bg-[#faf9f7]/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-200 shadow-sm">
                                            <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                                                alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('books.show', $book->slug) }}" target="_blank"
                                                class="font-serif font-bold text-sm text-gray-900 hover:text-gold-600 transition block">
                                                {{ $book->title }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-0.5">by {{ $book->author }}</p>
                                            @if ($book->isbn)
                                                <span class="text-[10px] text-gray-400 font-mono">ISBN: {{ $book->isbn }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                    {{ $book->category?->name ?? 'Uncategorized' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-serif font-bold text-sm text-gray-900">
                                    ${{ number_format($book->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($book->stock <= 0)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Out of stock (0)
                                        </span>
                                    @elseif ($book->stock <= 5)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Low: {{ $book->stock }} left
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $book->stock }} units
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs space-x-2">
                                    <a href="{{ route('admin.books.edit', $book->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-transparent transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                                    No books currently cataloged.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-100">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
