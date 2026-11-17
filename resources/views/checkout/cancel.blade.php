<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
            {{ __('Payment Cancelled') }}
        </h1>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 shadow-xs">

            {{-- Restrained Status Icon --}}
            <div class="w-12 h-12 mx-auto mb-5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <h2 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-2">Checkout Incomplete</h2>
            <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mb-8 max-w-md mx-auto leading-relaxed">
                Your checkout session was interrupted and you were not charged. Your reserved items remain in your bag so you can resume whenever convenient.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center text-xs">
                <a href="{{ route('cart.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-md font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                    Return to Bag
                </a>
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                    Browse Catalog
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
