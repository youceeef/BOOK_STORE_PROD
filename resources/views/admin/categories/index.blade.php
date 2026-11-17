<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Taxonomy</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Manage Categories</h1>
            </div>
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center px-3.5 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                <svg class="w-3.5 h-3.5 mr-1.5 rtl:mr-0 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Category</th>
                            <th class="px-5 py-3.5 font-semibold">URL Slug</th>
                            <th class="px-5 py-3.5 font-semibold">Visibility</th>
                            <th class="px-5 py-3.5 font-semibold">Volumes</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5 whitespace-nowrap font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ $category->name }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-zinc-400">
                                    /categories/{{ $category->slug }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($category->active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                    {{ $category->books_count ?? $category->books()->count() }} {{ Str::plural('title', $category->books()->count()) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs space-x-1.5 rtl:space-x-reverse">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                                    No categories configured.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
