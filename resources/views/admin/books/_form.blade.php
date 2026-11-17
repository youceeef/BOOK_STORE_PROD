@csrf

<div class="space-y-6" x-data="{ preview: null }">
    {{-- Title --}}
    <div>
        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
            Title <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
            placeholder="e.g. The Great Gatsby"
            class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('title') border-rose-500 @enderror">
        @error('title')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Category & Author Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                Category <span class="text-rose-500">*</span>
            </label>
            <select name="category_id" id="category_id" required
                class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('category_id') border-rose-500 @enderror">
                <option value="">Choose category...</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('category_id', $book->category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="author" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Author</label>
            <input type="text" name="author" id="author" value="{{ old('author', $book->author ?? '') }}"
                placeholder="F. Scott Fitzgerald"
                class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('author') border-rose-500 @enderror">
            @error('author')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Price & Stock & ISBN Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
            <label for="price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                Price ($) <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="price" id="price" value="{{ old('price', $book->price ?? '0.00') }}" required
                min="0" step="0.01" placeholder="19.99"
                class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('price') border-rose-500 @enderror">
            @error('price')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                Stock Quantity <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="stock" id="stock" value="{{ old('stock', $book->stock ?? '10') }}" required
                min="0" step="1"
                class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('stock') border-rose-500 @enderror">
            @error('stock')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="isbn" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">ISBN Number</label>
            <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                placeholder="978-3-16-148410-0"
                class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('isbn') border-rose-500 @enderror">
            @error('isbn')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Synopsis / Description</label>
        <textarea name="description" id="description" rows="5"
            placeholder="Provide historical context or narrative overview..."
            class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 @error('description') border-rose-500 @enderror">{{ old('description', $book->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Cover Image with Live Preview --}}
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Cover Artwork</label>
        <div class="flex items-center space-x-6">
            {{-- Image Preview --}}
            <div class="w-24 h-32 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center shadow-sm">
                <template x-if="preview">
                    <img :src="preview" class="w-full h-full object-cover">
                </template>
                <template x-if="!preview">
                    @isset($book)
                        <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xs text-gray-400">No cover</span>
                    @endisset
                </template>
            </div>

            <div class="flex-1">
                <input type="file" name="cover_image" id="cover_image" accept="image/*"
                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }"
                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#1a1a2e] file:text-gold-400 hover:file:bg-[#252849] file:cursor-pointer">
                <p class="text-[11px] text-gray-400 mt-1">Recommended format: JPG/PNG/WebP, minimum 600x800px.</p>
                @error('cover_image')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Form Buttons --}}
    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
        <a href="{{ route('admin.books.index') }}"
            class="px-5 py-2.5 rounded-xl text-xs font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 transition">
            Cancel
        </a>
        <button type="submit"
            class="px-6 py-2.5 rounded-xl text-xs font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
            {{ isset($book) ? 'Save Changes' : 'Publish Book' }}
        </button>
    </div>
</div>
