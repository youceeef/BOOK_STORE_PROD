<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Catalog Management</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Manage Volumes</h1>
            </div>
            <a href="{{ route('admin.books.create') }}"
                class="inline-flex items-center px-3.5 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                <svg class="w-3.5 h-3.5 mr-1.5 rtl:mr-0 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Volume
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Volume</th>
                            <th class="px-5 py-3.5 font-semibold">Category</th>
                            <th class="px-5 py-3.5 font-semibold">Price</th>
                            <th class="px-5 py-3.5 font-semibold">Stock</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($books as $book)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                        <div class="w-10 h-14 rounded overflow-hidden bg-zinc-100 dark:bg-zinc-900 flex-shrink-0 border border-zinc-200 dark:border-zinc-800">
                                            <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}"
                                                alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('books.show', $book->slug) }}" target="_blank"
                                                class="font-serif font-bold text-sm text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition block">
                                                {{ $book->title }}
                                            </a>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">by {{ $book->author }}</p>
                                            @if ($book->isbn)
                                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">ISBN: {{ $book->isbn }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-600 dark:text-zinc-400">
                                    {{ $book->category?->name ?? 'Uncategorized' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                    ${{ number_format($book->price, 2) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($book->stock <= 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Out of stock (0)
                                        </span>
                                    @elseif ($book->stock <= 5)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Low: {{ $book->stock }} left
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            {{ $book->stock }} in stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs space-x-1.5 rtl:space-x-reverse">
                                    <a href="{{ route('admin.books.edit', $book->id) }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this volume?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-xs text-zinc-400">
                                    No books currently cataloged.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
