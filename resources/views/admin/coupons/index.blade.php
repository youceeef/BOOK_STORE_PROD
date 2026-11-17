<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-xs uppercase tracking-wider text-gold-600 font-bold block mb-1">Promotions & Vouchers</span>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Manage Coupons</h1>
            </div>
            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Coupon
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-[#faf9f7]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Coupon Code</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Min Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Expiry</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Redemptions</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($coupons as $coupon)
                        <tr class="hover:bg-[#faf9f7]/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-lg font-mono text-xs font-bold uppercase tracking-wider bg-gold-50 text-gold-700 border border-gold-200">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-serif font-bold text-sm text-gray-900">
                                {{ $coupon->type == 'fixed' ? '$' : '' }}{{ number_format($coupon->value, $coupon->type == 'fixed' ? 2 : 0) }}{{ $coupon->type == 'percent' ? '%' : '' }}
                                <span class="text-[11px] text-gray-400 font-sans font-normal ml-1">({{ ucfirst($coupon->type) }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                {{ $coupon->min_amount ? '$' . number_format($coupon->min_amount, 2) : 'No minimum' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                <span class="font-bold text-gray-900">{{ $coupon->usage_count }}</span> / {{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($coupon->isValid())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Valid
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Inactive / Limit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs space-x-2">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this coupon code?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-transparent transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                                No promotional vouchers created.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100">
            {{ $coupons->links() }}
        </div>
    </div>
</x-admin-layout>
