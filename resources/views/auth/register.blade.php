<x-guest-layout>
    <div class="mb-6">
        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 block mb-1">Reader Onboarding</span>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
            Create Reader Account
        </h1>
        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
            Join our community of book lovers and enjoy curated access to fine literature and archival editions.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4"
        x-data="{
            password: '',
            passwordConfirmation: '',
            get hasLength() { return this.password.length >= 8; },
            get hasUpper() { return /[A-Z]/.test(this.password) && /[a-z]/.test(this.password); },
            get hasNumber() { return /[0-9!@#$%^&*(),.?\":{}|<>]/.test(this.password); },
            get isStrong() { return this.hasLength && this.hasUpper && this.hasNumber; },
            get isMatching() { return this.password && this.password === this.passwordConfirmation; }
        }">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                {{ __('Full Name') }}
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                placeholder="Alexander Pope"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            @error('name')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                {{ __('Email Address') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="reader@example.com"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password with Strength Indicator -->
        <div>
            <label for="password" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                {{ __('Password') }}
            </label>
            <input id="password" type="password" name="password" x-model="password" required autocomplete="new-password"
                placeholder="At least 8 characters"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            {{-- Dynamic Password Strength Meter --}}
            <div class="mt-2 space-y-1.5 bg-zinc-50 dark:bg-zinc-900/60 p-2.5 rounded-md border border-zinc-200 dark:border-zinc-800 text-[11px]" x-show="password.length > 0">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded-full flex items-center justify-center text-[9px] font-bold"
                        :class="hasLength ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'"
                        x-text="hasLength ? '✓' : '•'"></span>
                    <span :class="hasLength ? 'text-zinc-900 dark:text-white font-medium' : 'text-zinc-400'">At least 8 characters</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded-full flex items-center justify-center text-[9px] font-bold"
                        :class="hasUpper ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'"
                        x-text="hasUpper ? '✓' : '•'"></span>
                    <span :class="hasUpper ? 'text-zinc-900 dark:text-white font-medium' : 'text-zinc-400'">Mix of uppercase and lowercase letters</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded-full flex items-center justify-center text-[9px] font-bold"
                        :class="hasNumber ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'"
                        x-text="hasNumber ? '✓' : '•'"></span>
                    <span :class="hasNumber ? 'text-zinc-900 dark:text-white font-medium' : 'text-zinc-400'">At least one number or symbol</span>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                {{ __('Confirm Password') }}
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" x-model="passwordConfirmation" required autocomplete="new-password"
                placeholder="Repeat your password"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            @error('password_confirmation')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            <div x-show="passwordConfirmation.length > 0" class="mt-1 text-[11px]">
                <span x-show="isMatching" class="text-emerald-600 dark:text-emerald-400 font-medium">✓ Passwords match</span>
                <span x-show="!isMatching" class="text-rose-500 font-medium">Passwords do not match yet</span>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                {{ __('Create Reader Account') }}
            </button>
        </div>

        <div class="text-center pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500 dark:text-zinc-400">
            Already registered?
            <a href="{{ route('login') }}" class="font-semibold text-zinc-900 dark:text-white hover:underline ml-1">
                Sign in here
            </a>
        </div>
    </form>
</x-guest-layout>
