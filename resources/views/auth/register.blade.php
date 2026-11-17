<x-guest-layout>
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900 tracking-tight">Create Account</h1>
        <p class="text-sm text-gray-500 mt-2">
            Join our community of book lovers and enjoy curated access.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Alexander Pope" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="alexander@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="At least 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5" />
            <x-text-input id="password_confirmation" class="block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Repeat your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-3">
            <x-primary-button class="w-full">
                {{ __('Create Free Account') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-600">
                Already registered?
                <a href="{{ route('login') }}" class="font-bold text-gold-600 hover:text-gold-700 ml-1">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
