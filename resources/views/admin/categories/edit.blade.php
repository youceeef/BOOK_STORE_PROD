<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-0.5">Taxonomy</p>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Edit: {{ $category->name }}</h1>
            </div>
            <a href="{{ route('admin.categories.index') }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                @method('PUT')
                @include('admin.categories._form', ['category' => $category])
            </form>
        </div>
    </div>
</x-admin-layout>
