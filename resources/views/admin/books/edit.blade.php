<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Catalog</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Edit: {{ $book->title }}</h1>
            </div>
            <a href="{{ route('admin.books.index') }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                ← Back to Catalog
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.books._form')
            </form>
        </div>
    </div>
</x-admin-layout>
