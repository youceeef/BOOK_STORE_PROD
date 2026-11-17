<x-guest-layout>
    <div class="mb-6">
        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 block mb-1">Account Recovery</span>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
            Forgot Password?
        </h1>
        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
            Enter your registered email address and we'll dispatch a secure, time-sensitive recovery link to reset your credentials.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 p-3 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center space-x-2 rtl:space-x-reverse">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Demo Sandbox Instant Reset Link Shortcut --}}
    @if (session('demo_reset_url'))
        <div class="mb-6 p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/70 border border-zinc-200 dark:border-zinc-800 text-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <span>⚡</span> <span>Demo Sandbox Shortcut</span>
                </span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Live Simulator</span>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-[11px]">
                Testing on Railway or local development without an external inbox? You can click the recovery link directly:
            </p>
            <a href="{{ session('demo_reset_url') }}"
                class="inline-flex items-center justify-center w-full px-3 py-2 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                Open Password Reset Form
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                {{ __('Email Address') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="reader@example.com"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                {{ __('Send Reset Instructions') }}
            </button>
        </div>

        <div class="text-center pt-3 border-t border-zinc-100 dark:border-zinc-800">
            <a href="{{ route('login') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">
                ← Back to Sign In
            </a>
        </div>
    </form>
</x-guest-layout>
