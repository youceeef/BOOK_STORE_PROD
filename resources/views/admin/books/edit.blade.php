<x-admin-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs uppercase tracking-wider text-gold-600 font-bold block mb-1">Catalog</span>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Edit: {{ $book->title }}</h1>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.books.update', $book->id) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.books._form')
            </form>
        </div>
    </div>
</x-admin-layout>
