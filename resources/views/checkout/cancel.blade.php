<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Cancelled') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-10">

                {{-- Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="p-4 rounded-full bg-red-100 text-red-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-3">Payment Not Completed</h1>
                <p class="text-gray-600 mb-8">
                    Your payment was cancelled and you have not been charged.
                    Your cart items are still saved — you can try again whenever you're ready.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('cart.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150">
                        Return to Cart
                    </a>
                    <a href="{{ route('books.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                        Browse Books
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
