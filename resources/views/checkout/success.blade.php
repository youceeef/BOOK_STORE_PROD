<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
            Order Confirmation
        </h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 text-center">
            
            {{-- Success Checkmark --}}
            <div class="w-14 h-14 mx-auto mb-5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mb-2">
                Thank You For Your Order
            </h2>

            <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto mb-6 leading-relaxed">
                Your payment was received securely. Our curators are hand-inspecting and packing your volumes for priority dispatch.
            </p>

            {{-- Tracking Info Box --}}
            <div class="bg-zinc-50 dark:bg-zinc-900/60 rounded-md p-4 border border-zinc-200 dark:border-zinc-800 max-w-md mx-auto mb-8 text-left text-xs text-zinc-600 dark:text-zinc-400 space-y-1.5">
                <div class="flex items-center space-x-2 rtl:space-x-reverse text-emerald-700 dark:text-emerald-400 font-medium">
                    <span>✓ Confirmation sent to {{ auth()->user()->email }}</span>
                </div>
                <p class="text-[11px] text-zinc-500">
                    You can track your fulfillment status and download receipts directly in your account.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 text-xs">
                <a href="{{ route('orders.index') }}"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-md font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                    View Order History
                </a>
                <button type="button" onclick="window.print()"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    Print Receipt
                </button>
                <a href="{{ route('books.index') }}"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-md font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">
                    Continue Browsing →
                </a>
            </div>

        </div>
    </div>
</x-app-layout>