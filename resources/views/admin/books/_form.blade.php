@csrf

<div class="space-y-6" x-data="{ preview: null }">
    {{-- Title --}}
    <div>
        <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
            Title <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
            placeholder="e.g. The Great Gatsby"
            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('title') border-rose-500 @enderror">
        @error('title')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Category & Author Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                Category / Genre <span class="text-rose-500">*</span>
            </label>
            <select name="category_id" id="category_id" required
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('category_id') border-rose-500 @enderror">
                <option value="">Choose category...</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('category_id', $book->category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="author" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">Author</label>
            <input type="text" name="author" id="author" value="{{ old('author', $book->author ?? '') }}"
                placeholder="e.g. F. Scott Fitzgerald"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('author') border-rose-500 @enderror">
            @error('author')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Price & Stock & ISBN Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
            <label for="price" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                Price ($) <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="price" id="price" value="{{ old('price', $book->price ?? '0.00') }}" required
                min="0" step="0.01" placeholder="19.99"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('price') border-rose-500 @enderror">
            @error('price')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                Stock Quantity <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="stock" id="stock" value="{{ old('stock', $book->stock ?? '10') }}" required
                min="0" step="1"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('stock') border-rose-500 @enderror">
            @error('stock')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="isbn" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">ISBN Number</label>
            <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                placeholder="978-3-16-148410-0"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('isbn') border-rose-500 @enderror">
            @error('isbn')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">Synopsis / Description</label>
        <textarea name="description" id="description" rows="4"
            placeholder="Provide historical context, themes, or publisher overview..."
            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('description') border-rose-500 @enderror">{{ old('description', $book->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sample Excerpt / First Chapter ("Look Inside" Reader) --}}
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="sample_excerpt" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                Sample Excerpt / First Chapter (Look Inside Reader)
            </label>
            <span class="text-[11px] text-zinc-400">Powers the interactive reader modal</span>
        </div>
        <textarea name="sample_excerpt" id="sample_excerpt" rows="6"
            placeholder="Paste Chapter 1 or opening prose excerpt here for reader previews..."
            class="w-full text-xs font-serif leading-relaxed rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('sample_excerpt') border-rose-500 @enderror">{{ old('sample_excerpt', $book->sample_excerpt ?? '') }}</textarea>
        @error('sample_excerpt')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Cover Artwork with Live Preview --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">Cover Artwork</label>
        <div class="flex items-center space-x-5 rtl:space-x-reverse">
            {{-- Image Preview Box --}}
            <div class="w-20 h-28 rounded-md overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center flex-shrink-0">
                <template x-if="preview">
                    <img :src="preview" class="w-full h-full object-cover">
                </template>
                <template x-if="!preview">
                    @isset($book)
                        <img src="{{ $book->cover_image ? asset($book->cover_image) : asset('images/covers/default.png') }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-[11px] text-zinc-400">No cover</span>
                    @endisset
                </template>
            </div>

            <div class="flex-1">
                <input type="file" name="cover_image" id="cover_image" accept="image/*"
                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }"
                    class="block w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-800 dark:file:text-zinc-200 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 file:cursor-pointer transition">
                <p class="text-[11px] text-zinc-400 mt-1">Recommended format: JPG, PNG, or WebP. Optimal ratio 3:4.</p>
                @error('cover_image')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse pt-6 border-t border-zinc-200 dark:border-zinc-800">
        <a href="{{ route('admin.books.index') }}"
            class="px-3.5 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
            Cancel
        </a>
        <button type="submit"
            class="px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
            {{ isset($book) ? 'Save Changes' : 'Publish Volume' }}
        </button>
    </div>
</div>
