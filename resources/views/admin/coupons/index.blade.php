<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-zinc-400 dark:text-zinc-500 font-semibold block mb-0.5">Promotions & Vouchers</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">Coupons & Discounts</h1>
            </div>
            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center px-3.5 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                <svg class="w-3.5 h-3.5 mr-1.5 rtl:mr-0 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Coupon
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Coupon Code</th>
                            <th class="px-5 py-3.5 font-semibold">Discount</th>
                            <th class="px-5 py-3.5 font-semibold">Min Amount</th>
                            <th class="px-5 py-3.5 font-semibold">Expiry</th>
                            <th class="px-5 py-3.5 font-semibold">Redemptions</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($coupons as $coupon)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded font-mono text-xs font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                                        {{ $coupon->code }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-serif font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ $coupon->type == 'fixed' ? '$' : '' }}{{ number_format($coupon->value, $coupon->type == 'fixed' ? 2 : 0) }}{{ $coupon->type == 'percent' ? '%' : '' }}
                                    <span class="text-[11px] text-zinc-400 font-sans font-normal ml-1">({{ ucfirst($coupon->type) }})</span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                    {{ $coupon->min_amount ? '$' . number_format($coupon->min_amount, 2) : 'No minimum' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-400">
                                    {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600 dark:text-zinc-400 font-mono">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $coupon->usage_count }}</span> / {{ $coupon->usage_limit ?? '∞' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($coupon->isValid())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Valid
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Expired / Limit
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs space-x-1.5 rtl:space-x-reverse">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this coupon?');">
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
                                <td colspan="7" class="px-6 py-12 text-center text-xs text-zinc-400">
                                    No promotional coupons created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
