@csrf

<div class="space-y-5">
    {{-- Name --}}
    <div>
        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
            Category Name <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" required autofocus
            placeholder="e.g. Science & Philosophy"
            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('name') border-rose-500 @enderror">
        @error('name')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Active Status --}}
    <div class="p-3.5 rounded-md bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
        <label class="flex items-start space-x-3 rtl:space-x-reverse cursor-pointer">
            <input type="checkbox" name="active" id="active" value="1"
                {{ old('active', $category->active ?? true) ? 'checked' : '' }}
                class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white focus:ring-zinc-900 dark:focus:ring-white h-4 w-4 mt-0.5">
            <div>
                <span class="text-xs font-semibold text-zinc-900 dark:text-white block">Active in Storefront</span>
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400">When enabled, this category and its volumes are accessible in browse navigation and filters.</span>
            </div>
        </label>
        @error('active')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse pt-5 border-t border-zinc-200 dark:border-zinc-800">
        <a href="{{ route('admin.categories.index') }}"
            class="px-3.5 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
            Cancel
        </a>
        <button type="submit"
            class="px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
            {{ isset($category) ? 'Update Category' : 'Create Category' }}
        </button>
    </div>
</div>
