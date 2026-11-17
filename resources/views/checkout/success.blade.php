<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
            Order Confirmation
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-14 text-center">
            
            {{-- Success Icon Badge --}}
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border-2 border-emerald-200 shadow-sm">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-gold-50 text-gold-700 border border-gold-200 mb-3">
                Payment Authorized & Verified
            </span>

            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 mb-3">
                Thank You For Your Order
            </h2>

            <p class="text-sm sm:text-base text-gray-600 max-w-lg mx-auto mb-8 leading-relaxed font-light">
                Your payment was received securely. Our curators are hand-inspecting and carefully wrapping your volumes for priority dispatch.
            </p>

            {{-- Order Progress Timeline --}}
            <div class="max-w-md mx-auto mb-10 p-5 rounded-2xl bg-[#faf9f7] border border-gray-100">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 text-left">
                    Fulfillment Progress
                </div>
                <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold mb-1 shadow-sm">
                            ✓
                        </div>
                        <span class="font-semibold text-gray-900">Paid</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full bg-gold-500 text-white flex items-center justify-center font-bold mb-1 shadow-sm animate-pulse">
                            2
                        </div>
                        <span class="font-bold text-gold-700">Packing</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-medium mb-1">
                            3
                        </div>
                        <span class="text-gray-400">Shipped</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-medium mb-1">
                            4
                        </div>
                        <span class="text-gray-400">Delivered</span>
                    </div>
                </div>
            </div>

            {{-- Tracking Info Box --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-200 max-w-md mx-auto mb-8 text-left text-xs text-gray-600 space-y-2.5">
                <div class="flex items-center space-x-2 text-emerald-700 font-semibold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Confirmation & receipt sent to {{ auth()->user()->email }}</span>
                </div>
                <p class="text-gray-500 leading-relaxed">
                    You can inspect the fulfillment history, invoice details, and tracking updates anytime within your customer portal.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('orders.index') }}"
                    class="w-full sm:w-auto px-8 py-3.5 rounded-2xl font-semibold text-sm text-[#1a1a2e] bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-300 shadow-md transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>View Order History</span>
                </a>
                <button type="button" onclick="window.print()"
                    class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-medium text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Print Receipt</span>
                </button>
                <a href="{{ route('books.index') }}"
                    class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-medium text-sm text-gold-600 hover:text-gold-700 hover:bg-gold-50 transition">
                    Continue Browsing
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
