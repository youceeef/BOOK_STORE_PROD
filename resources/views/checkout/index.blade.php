<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
            {{ __('Secure Checkout') }}
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Stepper Progress: Clean, minimal line --}}
        <div class="max-w-md mx-auto mb-8">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-0.5 bg-zinc-200 dark:bg-zinc-800 -z-0"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1/2 h-0.5 bg-zinc-900 dark:bg-zinc-100 -z-0"></div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-7 h-7 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 flex items-center justify-center font-bold text-xs">
                        ✓
                    </div>
                    <span class="text-[11px] font-medium text-zinc-800 dark:text-zinc-200 mt-1.5">Bag</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-7 h-7 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 border-2 border-zinc-900 dark:border-white flex items-center justify-center font-bold text-xs">
                        2
                    </div>
                    <span class="text-[11px] font-bold text-zinc-900 dark:text-white mt-1.5">Payment</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-7 h-7 rounded-full bg-white dark:bg-[#12131a] text-zinc-400 border border-zinc-300 dark:border-zinc-700 flex items-center justify-center font-bold text-xs">
                        3
                    </div>
                    <span class="text-[11px] font-medium text-zinc-400 mt-1.5">Confirmation</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Left Column: Payment & Shipping Form (7 cols) --}}
            <div class="lg:col-span-7 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
                <form id="payment-form" action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment_method">

                    {{-- Customer Details --}}
                    <div>
                        <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white mb-4">
                            1. Customer & Delivery Address
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="sm:col-span-2">
                                <label for="card-holder-name" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Full Name</label>
                                <input type="text" id="card-holder-name" name="name" value="{{ auth()->user()->name }}" required
                                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="email" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email Receipt Address</label>
                                <input type="email" id="email" value="{{ auth()->user()->email }}" readonly
                                    class="w-full bg-zinc-100 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-500 cursor-not-allowed">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Delivery Address</label>
                                <input type="text" id="address" name="address" placeholder="124 Oxford Street, Flat 4B" required
                                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            </div>

                            <div>
                                <label for="city" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">City</label>
                                <input type="text" id="city" name="city" placeholder="London" required
                                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            </div>

                            <div>
                                <label for="postal-code" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">Postal Code</label>
                                <input type="text" id="postal-code" name="postal_code" placeholder="W1D 1BS" required
                                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details (Stripe Elements) --}}
                    <div class="pt-5 border-t border-zinc-100 dark:border-zinc-800">
                        <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white mb-4">
                            2. Card Details
                        </h2>

                        <div class="bg-zinc-50 dark:bg-zinc-900/60 rounded-md p-4 border border-zinc-200 dark:border-zinc-800">
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-2">Credit or Debit Card</label>
                            <div id="card-element" class="bg-white dark:bg-[#12131a] p-3 rounded border border-zinc-200 dark:border-zinc-800">
                                <!-- Stripe Elements Mount -->
                            </div>
                            <div id="card-errors" class="mt-2 text-xs text-rose-600 font-medium" role="alert"></div>
                        </div>

                        <p class="mt-3 text-[11px] text-zinc-400">
                            Encrypted card processing via Stripe. Card details never touch our server.
                        </p>
                    </div>

                    {{-- Submit CTA --}}
                    <button id="submit-button" type="submit"
                        class="w-full py-3 px-6 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Confirm & Pay ${{ number_format($cartTotal, 2) }}</span>
                        <svg id="spinner" class="animate-spin ml-2 h-4 w-4 text-white dark:text-zinc-900 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Right Column: Order Summary (5 cols) --}}
            <div class="lg:col-span-5 bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 space-y-4">
                <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    Items In Order ({{ $cartItems->count() }})
                </h2>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800 max-h-72 overflow-y-auto pr-1">
                    @foreach ($cartItems as $item)
                        <div class="py-2.5 flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="w-10 h-14 rounded bg-zinc-100 dark:bg-zinc-900 flex-shrink-0 border border-zinc-200 dark:border-zinc-800 p-1 flex items-center justify-center">
                                <img src="{{ asset($item->attributes->image ?: 'images/covers/default.png') }}"
                                    alt="{{ $item->name }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-xs font-semibold text-zinc-900 dark:text-white truncate">{{ $item->name }}</h3>
                                <p class="text-[11px] text-zinc-400">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <span class="text-xs font-serif font-bold text-zinc-900 dark:text-white">
                                ${{ number_format($item->getPriceSum(), 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Price Breakdown --}}
                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-white">${{ number_format($cartSubtotal, 2) }}</span>
                    </div>

                    @if ($discount > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium">
                            <span>Coupon Discount ({{ $appliedCouponCode }})</span>
                            <span>-${{ number_format($discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Tracked Shipping</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">Complimentary</span>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2.5 flex justify-between items-baseline">
                        <span class="font-serif text-sm font-bold text-zinc-900 dark:text-white">Total Due</span>
                        <span class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                            ${{ number_format($cartTotal, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stripe.js Setup --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ $stripeKey }}");
        const clientSecret = "{{ $intentClientSecret }}";

        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#18181b',
                    fontFamily: 'Inter, system-ui, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '14px',
                    '::placeholder': { color: '#a1a1aa' }
                },
                invalid: {
                    color: '#e11d48',
                    iconColor: '#e11d48'
                }
            }
        });

        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');
        const cardErrors = document.getElementById('card-errors');
        const paymentMethodInput = document.getElementById('payment_method');

        cardElement.on('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            setLoading(true);

            const cardHolderName = document.getElementById('card-holder-name').value;

            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardHolderName
                        }
                    }
                }
            );

            if (error) {
                cardErrors.textContent = error.message;
                setLoading(false);
            } else {
                if (setupIntent.status === 'succeeded') {
                    paymentMethodInput.value = setupIntent.payment_method;
                    form.submit();
                } else {
                    cardErrors.textContent = 'Setup did not succeed. Status: ' + setupIntent.status;
                    setLoading(false);
                }
            }
        });

        function setLoading(isLoading) {
            if (isLoading) {
                submitButton.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.textContent = 'Processing Payment...';
            } else {
                submitButton.disabled = false;
                spinner.classList.add('hidden');
                buttonText.textContent = 'Confirm & Pay ${{ number_format($cartTotal, 2) }}';
            }
        }
    </script>
</x-app-layout>