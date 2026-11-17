<x-guest-layout>
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900 tracking-tight">Reset Password</h1>
        <p class="text-sm text-gray-500 mt-2">
            Enter your registered email address and we'll dispatch a secure recovery link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="reader@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full">
                {{ __('Send Reset Instructions') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-gray-200">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                ← Back to Sign In
            </a>
        </div>
    </form>
</x-guest-layout>
