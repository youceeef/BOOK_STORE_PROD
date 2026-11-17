<x-guest-layout>
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900 tracking-tight">Sign In</h1>
        <p class="text-sm text-gray-500 mt-2">
            Welcome back. Please enter your credentials to access your library.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="reader@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-input-label for="password" :value="__('Password')" class="text-xs font-semibold uppercase tracking-wider text-gray-700" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-gold-600 hover:text-gold-700 font-medium" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gold-500 shadow-sm focus:ring-gold-500" name="remember">
                <span class="ms-2 text-xs text-gray-600">{{ __('Keep me signed in') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Sign In to BookStore') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-600">
                New to BookStore?
                <a href="{{ route('register') }}" class="font-bold text-gold-600 hover:text-gold-700 ml-1">
                    Create an account
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
