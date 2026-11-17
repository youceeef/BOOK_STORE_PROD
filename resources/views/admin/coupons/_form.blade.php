@csrf

<div class="space-y-5">
    {{-- Code --}}
    <div>
        <label for="code" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Coupon Code <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="code" id="code" value="{{ old('code', $coupon->code ?? '') }}" required
            maxlength="50" placeholder="e.g. WELCOME10, SUMMER25"
            class="w-full text-xs font-mono uppercase rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('code') border-rose-500 @enderror">
        @error('code')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Type & Value Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="type" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Discount Type <span class="text-rose-500">*</span>
            </label>
            <select name="type" id="type" required
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('type') border-rose-500 @enderror">
                <option value="">Select Type...</option>
                <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                <option value="percent" {{ old('type', $coupon->type ?? '') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
            </select>
            @error('type')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="value" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Discount Value <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="value" id="value" value="{{ old('value', $coupon->value ?? '') }}" required
                min="0" step="0.01" placeholder="e.g. 10.00 or 15"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('value') border-rose-500 @enderror">
            @error('value')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Minimum Amount & Usage Limit --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="min_amount" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Minimum Cart Total ($)
            </label>
            <input type="number" name="min_amount" id="min_amount" value="{{ old('min_amount', $coupon->min_amount ?? '') }}"
                min="0" step="0.01" placeholder="Leave empty for none"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('min_amount') border-rose-500 @enderror">
            @error('min_amount')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="usage_limit" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Total Redemptions Limit
            </label>
            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                min="1" step="1" placeholder="Leave empty for unlimited"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('usage_limit') border-rose-500 @enderror">
            @error('usage_limit')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Expiration Date --}}
    <div>
        <label for="expires_at" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Expiration Date (Optional)
        </label>
        <input type="datetime-local" name="expires_at" id="expires_at"
            value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition @error('expires_at') border-rose-500 @enderror">
        @error('expires_at')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse pt-5 border-t border-zinc-200 dark:border-zinc-800">
        <a href="{{ route('admin.coupons.index') }}"
            class="px-3.5 py-1.5 rounded-md text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
            Cancel
        </a>
        <button type="submit"
            class="px-4 py-2 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
            {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
        </button>
    </div>
</div>
