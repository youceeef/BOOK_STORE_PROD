<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
            Secure Checkout
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Stepper Progress Bar --}}
        <div class="max-w-xl mx-auto mb-10">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-0.5 bg-gray-200 -z-0"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1/2 h-0.5 bg-gold-500 -z-0"></div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-9 h-9 rounded-full bg-gold-500 text-white flex items-center justify-center font-bold text-xs shadow-md">
                        ✓
                    </div>
                    <span class="text-xs font-semibold text-gray-800 mt-2">Bag</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-9 h-9 rounded-full bg-[#1a1a2e] text-gold-400 border-2 border-gold-500 flex items-center justify-center font-bold text-xs shadow-md">
                        2
                    </div>
                    <span class="text-xs font-bold text-gold-600 mt-2">Payment</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-9 h-9 rounded-full bg-white text-gray-400 border border-gray-300 flex items-center justify-center font-bold text-xs">
                        3
                    </div>
                    <span class="text-xs font-medium text-gray-400 mt-2">Confirmation</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            {{-- Left Column: Payment & Shipping Form (7 cols) --}}
            <div class="lg:col-span-7 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-10">
                <form id="payment-form" action="{{ route('checkout.process') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment_method">

                    {{-- Section 1: Customer Details --}}
                    <div>
                        <h2 class="font-serif text-xl font-bold text-gray-900 mb-5 flex items-center">
                            <span class="w-7 h-7 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-xs font-bold mr-3">1</span>
                            Customer & Delivery Information
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="card-holder-name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Full Name</label>
                                <input type="text" id="card-holder-name" name="name" value="{{ auth()->user()->name }}" required
                                    class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Email Receipt Address</label>
                                <input type="email" id="email" value="{{ auth()->user()->email }}" readonly
                                    class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Delivery Address</label>
                                <input type="text" id="address" name="address" placeholder="124 Oxford Street, Flat 4B" required
                                    class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            </div>

                            <div>
                                <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">City</label>
                                <input type="text" id="city" name="city" placeholder="London" required
                                    class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            </div>

                            <div>
                                <label for="postal-code" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Postal Code</label>
                                <input type="text" id="postal-code" name="postal_code" placeholder="W1D 1BS" required
                                    class="w-full bg-[#faf9f7] border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Payment Method (Stripe Elements) --}}
                    <div class="pt-6 border-t border-gray-100">
                        <h2 class="font-serif text-xl font-bold text-gray-900 mb-5 flex items-center">
                            <span class="w-7 h-7 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-xs font-bold mr-3">2</span>
                            Payment Card Details
                        </h2>

                        <div class="bg-[#faf9f7] rounded-2xl p-5 border border-gray-200">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2.5">Credit or Debit Card</label>
                            <div id="card-element" class="bg-white p-3.5 rounded-xl border border-gray-200 focus-within:border-gold-500">
                                <!-- Stripe Elements Mount -->
                            </div>
                            <div id="card-errors" class="mt-2.5 text-xs text-rose-600 font-medium" role="alert"></div>
                        </div>

                        <div class="mt-4 flex items-center space-x-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Encrypted card processing via Stripe. Card details never touch our server.</span>
                        </div>
                    </div>

                    {{-- Submit CTA --}}
                    <button id="submit-button" type="submit"
                        class="w-full py-4 px-8 rounded-2xl text-base font-semibold text-[#1a1a2e] bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 shadow-xl transition-all duration-200 flex items-center justify-center space-x-2 active:scale-98 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Confirm & Pay ${{ number_format($cartTotal, 2) }}</span>
                        <svg id="spinner" class="animate-spin ml-2 h-5 w-5 text-[#1a1a2e] hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Right Column: Order Summary (5 cols) --}}
            <div class="lg:col-span-5 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                <h2 class="font-serif text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">
                    Items In Your Order ({{ $cartItems->count() }})
                </h2>

                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-1">
                    @foreach ($cartItems as $item)
                        <div class="py-3 flex items-center space-x-4">
                            <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-100">
                                <img src="{{ asset($item->attributes->image ?: 'images/covers/default.png') }}"
                                    alt="{{ $item->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $item->name }}</h3>
                                <p class="text-xs text-gray-400">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900 font-serif">
                                ${{ number_format($item->getPriceSum(), 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Price Breakdown --}}
                <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900">${{ number_format($cartSubtotal, 2) }}</span>
                    </div>

                    @if ($discount > 0)
                        <div class="flex justify-between text-emerald-600 font-medium">
                            <span>Coupon Discount ({{ $appliedCouponCode }})</span>
                            <span>-${{ number_format($discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-gray-600">
                        <span>Tracked Shipping</span>
                        <span class="text-emerald-600 font-medium">Complimentary</span>
                    </div>

                    <div class="border-t border-gray-100 pt-3 flex justify-between items-baseline">
                        <span class="font-serif text-lg font-bold text-gray-900">Total Due</span>
                        <span class="font-serif text-3xl font-bold text-gray-900">
                            ${{ number_format($cartTotal, 2) }}
                        </span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-[#faf9f7] border border-gray-100 text-xs text-gray-500 space-y-2">
                    <div class="font-bold text-gray-800 uppercase tracking-wider">Cancellation & Returns</div>
                    <p>Free cancellation before dispatch. 30-day money-back return policy on all physical books.</p>
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
                    color: '#1a1a2e',
                    fontFamily: 'Inter, system-ui, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '15px',
                    '::placeholder': { color: '#9ca3af' }
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
