<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Your Shopping Bag') }}
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ Cart::getContent()->count() }} {{ Str::plural('distinct title', Cart::getContent()->count()) }} selected
                </p>
            </div>
            <a href="{{ route('books.index') }}" class="text-xs sm:text-sm font-semibold text-gold-600 dark:text-gold-400 hover:text-gold-700 transition flex items-center gap-1">
                <span>←</span>
                <span>{{ __('Continue Browsing') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($cartItems->isEmpty())
            {{-- Empty Cart State --}}
            <div class="max-w-xl mx-auto text-center py-20 px-6 bg-white dark:bg-darkcard rounded-3xl border border-gray-100 dark:border-darkborder shadow-sm">
                <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-gold-50 dark:bg-navy-900 text-gold-500 flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('Your Bag is Empty') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto leading-relaxed">
                    It looks like you haven't selected any volumes yet. Discover timeless reads in our curated catalog.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-7 py-3.5 rounded-2xl text-sm font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-lg transition">
                    {{ __('Explore The Library') }}
                </a>
            </div>
        @else
            @php
                $subtotal = Cart::getSubTotal(false);
                $shippingThreshold = 35.00;
                $freeShippingProgress = min(100, round(($subtotal / $shippingThreshold) * 100));
                $amountNeeded = max(0, $shippingThreshold - $subtotal);

                $discount = 0;
                $couponCode = null;
                if (session()->has('coupon')) {
                    $couponData = session('coupon');
                    $couponModel = \App\Models\Coupon::where('code', $couponData['code'])->first();
                    if ($couponModel && $couponModel->isValid() && (!$couponModel->min_amount || $subtotal >= $couponModel->min_amount)) {
                        $discount = $couponModel->calculateDiscount($subtotal);
                        $couponCode = $couponModel->code;
                    } else {
                        session()->forget('coupon');
                    }
                }
                $finalTotal = max(0, $subtotal - $discount);
            @endphp

            {{-- Free Shipping Tracker Banner --}}
            <div class="mb-8 bg-white dark:bg-darkcard rounded-2xl p-5 border border-gray-100 dark:border-darkborder shadow-sm">
                <div class="flex items-center justify-between text-xs sm:text-sm font-medium mb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🚚</span>
                        @if ($subtotal >= $shippingThreshold)
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">{{ __("You've unlocked FREE Tracked Delivery!") }}</span>
                        @else
                            <span class="text-gray-700 dark:text-gray-300">{{ __('Add') }} <span class="font-bold text-gray-900 dark:text-white">${{ number_format($amountNeeded, 2) }}</span> {{ __('more to unlock') }} <span class="font-bold text-gold-600 dark:text-gold-400">{{ __('Free Tracked Delivery') }}</span></span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $freeShippingProgress }}%</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-navy-950 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-gold-400 to-gold-500 h-2.5 rounded-full transition-all duration-500"
                        style="width: {{ $freeShippingProgress }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                {{-- Left: Cart Items List (8 cols) --}}
                <div class="lg:col-span-8 space-y-4">
                    <div class="bg-white dark:bg-darkcard rounded-3xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden divide-y divide-gray-100 dark:divide-darkborder">
                        @foreach ($cartItems as $item)
                            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:bg-[#faf9f7]/60 dark:hover:bg-navy-900/30 transition duration-150">
                                {{-- Item Cover & Title --}}
                                <div class="flex items-center space-x-5 rtl:space-x-reverse flex-1 min-w-0">
                                    <div class="w-20 h-28 flex-shrink-0 rounded-xl overflow-hidden shadow-md bg-gray-50 dark:bg-navy-950 border border-gray-100 dark:border-navy-800 book-depth-shadow">
                                        <img src="{{ asset($item->attributes->image ?: 'images/covers/default.png') }}"
                                            alt="{{ $item->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('books.show', $item->attributes->slug ?? $item->id) }}"
                                            class="font-serif font-bold text-base sm:text-lg text-gray-900 dark:text-gray-100 hover:text-gold-600 dark:hover:text-gold-400 transition truncate block"
                                            title="{{ $item->name }}">
                                            {{ $item->name }}
                                        </a>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Edition price: <span class="font-semibold text-gray-700 dark:text-gray-300">${{ number_format($item->price, 2) }}</span>
                                        </p>
                                        @if ($item->attributes->stock && $item->attributes->stock <= 5)
                                            <span class="inline-block text-[10px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800 mt-2">
                                                {{ __('Only :count Left', ['count' => $item->attributes->stock]) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quantity Stepper & Update --}}
                                <div class="flex items-center space-x-6 rtl:space-x-reverse w-full sm:w-auto justify-between sm:justify-end">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                        class="flex items-center" x-data="{ qty: {{ $item->quantity }} }">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center border border-gray-200 dark:border-navy-800 rounded-xl bg-[#faf9f7] dark:bg-navy-950 p-0.5 shadow-sm">
                                            <button type="button" @click="if(qty > 1) { qty--; $nextTick(() => $refs.submitBtn.click()) }"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white shadow-sm font-bold text-sm active:scale-95 transition">
                                                -
                                            </button>
                                            <input type="number" name="quantity" x-model="qty" min="1" max="{{ $item->attributes->stock ?? 99 }}"
                                                class="w-12 text-center bg-transparent border-0 font-bold text-gray-900 dark:text-gray-100 focus:ring-0 text-sm p-0"
                                                @change="$refs.submitBtn.click()">
                                            <button type="button" @click="if(qty < {{ $item->attributes->stock ?? 99 }}) { qty++; $nextTick(() => $refs.submitBtn.click()) }"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white shadow-sm font-bold text-sm active:scale-95 transition">
                                                +
                                            </button>
                                        </div>
                                        <button type="submit" x-ref="submitBtn" class="hidden">Update</button>
                                    </form>

                                    {{-- Line Total --}}
                                    <div class="text-right rtl:text-left min-w-[5.5rem]">
                                        <span class="font-serif text-lg font-bold text-gray-900 dark:text-gray-100 block">
                                            ${{ number_format($item->getPriceSum(), 2) }}
                                        </span>
                                    </div>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition"
                                            title="Remove volume"
                                            onclick="return confirm('Remove this volume from your bag?');">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Bottom Utility Buttons --}}
                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('books.index') }}"
                            class="inline-flex items-center text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gold-600 dark:hover:text-gold-400 transition">
                            ← {{ __('Add more titles to bag') }}
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to empty your entire bag?');">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                {{ __('Empty shopping bag') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: Order Summary & Coupon (4 cols) --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- Summary Card --}}
                    <div class="bg-white dark:bg-darkcard rounded-3xl shadow-sm border border-gray-100 dark:border-darkborder p-6 sm:p-8 space-y-6">
                        <h2 class="font-serif text-xl font-bold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-darkborder pb-4">
                            {{ __('Summary & Checkout') }}
                        </h2>

                        <div class="space-y-3.5 text-sm">
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>{{ __('Subtotal') }}</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if ($discount > 0)
                                <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                    <span>{{ __('Discount') }} ({{ $couponCode }})</span>
                                    <span class="font-semibold">-${{ number_format($discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>{{ __('Tracked Delivery') }}</span>
                                <span class="font-semibold {{ $subtotal >= $shippingThreshold ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ $subtotal >= $shippingThreshold ? 'FREE' : '$4.99' }}
                                </span>
                            </div>

                            @php
                                $shippingCost = ($subtotal >= $shippingThreshold) ? 0 : 4.99;
                                $orderGrandTotal = $finalTotal + $shippingCost;
                            @endphp

                            <div class="border-t border-gray-100 dark:border-darkborder pt-4 flex justify-between items-baseline">
                                <div>
                                    <span class="font-serif text-lg font-bold text-gray-900 dark:text-gray-100 block">{{ __('Total') }}</span>
                                    <span class="text-[10px] text-gray-400">All local taxes included</span>
                                </div>
                                <span class="font-serif text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($orderGrandTotal, 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Proceed to Checkout Button --}}
                        <a href="{{ route('checkout.index') }}"
                            class="w-full py-4 px-6 rounded-2xl text-center font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 shadow-lg transition-all duration-200 block text-base active:scale-98">
                            {{ __('Proceed to Checkout') }}
                        </a>

                        <div class="text-center text-[11px] text-gray-400 flex items-center justify-center space-x-1.5 rtl:space-x-reverse pt-1">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>{{ __('Encrypted 256-Bit SSL Checkout') }}</span>
                        </div>
                    </div>

                    {{-- Coupon Voucher Card --}}
                    <div class="bg-white dark:bg-darkcard rounded-3xl shadow-sm border border-gray-100 dark:border-darkborder p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                {{ __('Promotional Voucher') }}
                            </h3>
                            <span class="text-[10px] text-gold-600 dark:text-gold-400 font-semibold">{{ __('Special Offers') }}</span>
                        </div>

                        @if (!session()->has('coupon'))
                            <form action="{{ route('coupon.store') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="{{ __('Enter coupon...') }}" required
                                    class="flex-1 text-xs bg-[#faf9f7] dark:bg-navy-950 border border-gray-200 dark:border-navy-800 rounded-xl px-4 py-2.5 uppercase font-medium text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                                <button type="submit"
                                    class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#1a1a2e] hover:bg-[#252849] transition shadow-sm border border-navy-800">
                                    {{ __('Apply') }}
                                </button>
                            </form>
                            @error('coupon_code')
                                <p class="text-xs text-rose-600 mt-2">{{ $message }}</p>
                            @enderror

                            {{-- Suggestion Pills for Testing / Demo --}}
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-800 text-[11px] text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-1.5">
                                <span class="text-gray-400">{{ __('Try code:') }}</span>
                                <button type="button" onclick="document.querySelector('input[name=coupon_code]').value='WELCOME10'"
                                    class="px-2 py-0.5 rounded bg-gray-100 dark:bg-navy-900 hover:bg-gold-50 dark:hover:bg-gold-950/40 hover:text-gold-700 dark:hover:text-gold-400 text-gray-700 dark:text-gray-300 font-mono text-[10px] font-semibold transition border border-gray-200 dark:border-navy-800">
                                    WELCOME10
                                </button>
                                <button type="button" onclick="document.querySelector('input[name=coupon_code]').value='SAVE5'"
                                    class="px-2 py-0.5 rounded bg-gray-100 dark:bg-navy-900 hover:bg-gold-50 dark:hover:bg-gold-950/40 hover:text-gold-700 dark:hover:text-gold-400 text-gray-700 dark:text-gray-300 font-mono text-[10px] font-semibold transition border border-gray-200 dark:border-navy-800">
                                    SAVE5
                                </button>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wide">
                                        {{ session('coupon')['code'] }}
                                    </span>
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 bg-white/70 dark:bg-navy-950/70 px-1.5 py-0.5 rounded font-medium">{{ __('Applied') }}</span>
                                </div>
                                <form action="{{ route('coupon.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold transition">
                                        {{ __('Remove') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        @endif
    </div>
</x-app-layout>