<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Account On Hold · Activation Required</span>
        </div>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
            Verify Your Email Address
        </h1>
        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
            Welcome to BookStore. We have dispatched a temporary activation link to your registered email address. For your account security, this link is valid for <strong>60 minutes</strong>. Your account remains on hold until verified.
        </p>
        <div class="mt-3 inline-flex items-center px-3 py-1.5 rounded bg-zinc-100 dark:bg-zinc-800 text-xs font-mono text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
            ✉ {{ auth()->user()->email }}
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center space-x-2 rtl:space-x-reverse">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>A fresh 60-minute activation link has been dispatched to your email address.</span>
        </div>
    @endif

    {{-- Testing Sandbox Quick Verification Action --}}
    @if (isset($demoVerificationUrl))
        <div class="mb-6 p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/70 border border-zinc-200 dark:border-zinc-800 text-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <span>⚡</span> <span>Testing Sandbox Shortcut</span>
                </span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Live Simulator</span>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-[11px]">
                Testing on Railway or localhost without waiting for an inbox? Click here to activate this account directly:
            </p>
            <a href="{{ $demoVerificationUrl }}"
                class="inline-flex items-center justify-center w-full px-3 py-2 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                ✓ Activate Account Instantly
            </a>
        </div>
    @endif

    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-zinc-100 dark:border-zinc-800 text-xs">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit"
                class="w-full sm:w-auto px-4 py-2 rounded-md font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 transition">
                {{ __('Resend Activation Link') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center sm:text-right">
            @csrf
            <button type="submit" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition underline">
                {{ __('Sign Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
