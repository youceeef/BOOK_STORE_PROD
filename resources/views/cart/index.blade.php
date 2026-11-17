<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ __('Your Shopping Bag') }}
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    {{ Cart::getContent()->count() }} {{ Str::plural('title', Cart::getContent()->count()) }} selected
                </p>
            </div>
            <a href="{{ route('books.index') }}" class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
                <span>←</span>
                <span>{{ __('Continue Browsing') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($cartItems->isEmpty())
            {{-- Empty Cart State --}}
            <div class="max-w-md mx-auto text-center py-16 px-6 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800">
                <h2 class="font-serif text-xl font-bold text-zinc-900 dark:text-white mb-1">{{ __('Your Bag is Empty') }}</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">
                    You haven't added any volumes to your bag yet.
                </p>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
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

            {{-- Shipping Tracker: Quiet architectural progress --}}
            <div class="mb-6 bg-white dark:bg-[#12131a] rounded-lg p-4 border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center justify-between text-xs mb-2">
                    @if ($subtotal >= $shippingThreshold)
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ __("You've unlocked FREE Tracked Delivery!") }}</span>
                    @else
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Add') }} <span class="font-semibold text-zinc-900 dark:text-white">${{ number_format($amountNeeded, 2) }}</span> {{ __('more to unlock') }} <span class="font-medium text-zinc-900 dark:text-white">{{ __('Free Tracked Delivery') }}</span></span>
                    @endif
                    <span class="text-zinc-500 font-mono text-[11px]">{{ $freeShippingProgress }}%</span>
                </div>
                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-zinc-900 dark:bg-white h-1.5 rounded-full transition-all duration-300"
                        style="width: {{ $freeShippingProgress }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
                x-data="{
                    loading: false,
                    async removeItem(url) {
                        if (this.loading) return;
                        this.loading = true;
                        try {
                            const res = await fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cartCount } }));
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: {
                                        type: 'info',
                                        message: data.message || 'Volume removed from shopping bag.'
                                    }
                                }));
                                setTimeout(() => window.location.reload(), 400);
                            } else {
                                window.location.reload();
                            }
                        } catch (e) {
                            window.location.reload();
                        }
                    },
                    async clearBag(url) {
                        if (this.loading) return;
                        this.loading = true;
                        try {
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: {
                                        type: 'info',
                                        message: data.message || 'Shopping bag cleared.'
                                    }
                                }));
                                setTimeout(() => window.location.reload(), 400);
                            } else {
                                window.location.reload();
                            }
                        } catch (e) {
                            window.location.reload();
                        }
                    }
                }">
                
                {{-- Left: Cart Items List (8 cols) --}}
                <div class="lg:col-span-8 space-y-4">
                    <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($cartItems as $item)
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                {{-- Item Cover & Title --}}
                                <div class="flex items-center space-x-4 rtl:space-x-reverse flex-1 min-w-0">
                                    <div class="w-16 h-22 flex-shrink-0 bg-zinc-100 dark:bg-zinc-900 rounded p-1 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                                        <img src="{{ asset($item->attributes->image ?: 'images/covers/default.png') }}"
                                            alt="{{ $item->name }}"
                                            class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('books.show', $item->attributes->slug ?? $item->id) }}"
                                            class="font-serif font-bold text-base text-zinc-900 dark:text-white hover:underline truncate block"
                                            title="{{ $item->name }}">
                                            {{ $item->name }}
                                        </a>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            ${{ number_format($item->price, 2) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Quantity Stepper & Update --}}
                                <div class="flex items-center space-x-4 rtl:space-x-reverse w-full sm:w-auto justify-between sm:justify-end">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                        class="flex items-center" x-data="{ qty: {{ $item->quantity }} }">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center border border-zinc-200 dark:border-zinc-800 rounded-md bg-zinc-50 dark:bg-zinc-900">
                                            <button type="button" @click="if(qty > 1) { qty--; $nextTick(() => $refs.submitBtn.click()) }"
                                                class="w-7 h-7 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:text-black dark:hover:text-white text-xs font-bold transition">
                                                -
                                            </button>
                                            <input type="number" name="quantity" x-model="qty" min="1" max="{{ $item->attributes->stock ?? 99 }}"
                                                class="w-8 text-center bg-transparent border-0 font-medium text-zinc-900 dark:text-white focus:ring-0 text-xs p-0"
                                                @change="$refs.submitBtn.click()">
                                            <button type="button" @click="if(qty < {{ $item->attributes->stock ?? 99 }}) { qty++; $nextTick(() => $refs.submitBtn.click()) }"
                                                class="w-7 h-7 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:text-black dark:hover:text-white text-xs font-bold transition">
                                                +
                                            </button>
                                        </div>
                                        <button type="submit" x-ref="submitBtn" class="hidden">Update</button>
                                    </form>

                                    {{-- Line Total --}}
                                    <div class="text-right rtl:text-left min-w-[5rem]">
                                        <span class="font-serif font-bold text-zinc-900 dark:text-white text-sm">
                                            ${{ number_format($item->getPriceSum(), 2) }}
                                        </span>
                                    </div>

                                    {{-- Delete Button (AJAX with toast notification, zero native popups) --}}
                                    <button type="button"
                                        @click="removeItem('{{ route('cart.destroy', $item->id) }}')"
                                        class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition"
                                        title="{{ __('Remove volume') }}">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Bottom Utility Buttons --}}
                    <div class="flex items-center justify-between text-xs pt-1">
                        <a href="{{ route('books.index') }}"
                            class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                            ← {{ __('Add more titles to bag') }}
                        </a>

                        <button type="button"
                            @click="clearBag('{{ route('cart.clear') }}')"
                            class="text-rose-600 dark:text-rose-400 hover:underline text-xs">
                            {{ __('Empty shopping bag') }}
                        </button>
                    </div>
                </div>

                {{-- Right: Order Summary & Coupon (4 cols) --}}
                <div class="lg:col-span-4 space-y-4">
                    {{-- Summary Card --}}
                    <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 space-y-4">
                        <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            {{ __('Summary & Checkout') }}
                        </h2>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Subtotal') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if ($discount > 0)
                                <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                    <span>{{ __('Discount') }} ({{ $couponCode }})</span>
                                    <span class="font-semibold">-${{ number_format($discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Tracked Delivery') }}</span>
                                <span class="font-medium {{ $subtotal >= $shippingThreshold ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                    {{ $subtotal >= $shippingThreshold ? 'FREE' : '$4.99' }}
                                </span>
                            </div>

                            @php
                                $shippingCost = ($subtotal >= $shippingThreshold) ? 0 : 4.99;
                                $orderGrandTotal = $finalTotal + $shippingCost;
                            @endphp

                            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3 flex justify-between items-baseline">
                                <span class="font-serif font-bold text-base text-zinc-900 dark:text-white">{{ __('Total') }}</span>
                                <span class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                                    ${{ number_format($orderGrandTotal, 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Proceed to Checkout Button --}}
                        <a href="{{ route('checkout.index') }}"
                            class="w-full py-2.5 px-4 rounded-md text-center text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition block">
                            {{ __('Proceed to Checkout') }}
                        </a>

                        <p class="text-center text-[10px] text-zinc-400">
                            256-Bit Encrypted Checkout
                        </p>
                    </div>

                    {{-- Coupon Voucher Card --}}
                    <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2.5">
                            {{ __('Promotional Voucher') }}
                        </h3>

                        @if (!session()->has('coupon'))
                            <form action="{{ route('coupon.store') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="{{ __('Enter coupon...') }}" required
                                    class="flex-1 text-xs bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-1.5 uppercase font-medium text-zinc-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-white bg-zinc-900 dark:bg-zinc-700 hover:bg-black transition">
                                    {{ __('Apply') }}
                                </button>
                            </form>
                            @error('coupon_code')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 text-[10px] text-zinc-400 flex items-center gap-1.5">
                                <span>{{ __('Try code:') }}</span>
                                <button type="button" onclick="document.querySelector('input[name=coupon_code]').value='WELCOME10'" class="font-mono text-zinc-600 dark:text-zinc-300 underline">WELCOME10</button>
                            </div>
                        @else
                            <div class="p-2.5 rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <span class="font-bold text-zinc-900 dark:text-white uppercase font-mono">{{ session('coupon')['code'] }}</span>
                                    <span class="text-[10px] text-emerald-600 font-medium">({{ __('Applied') }})</span>
                                </div>
                                <form action="{{ route('coupon.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 text-xs hover:underline">
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