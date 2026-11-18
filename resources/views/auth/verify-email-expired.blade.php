<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 mb-3">
            <span>✕</span>
            <span>Link Expired</span>
        </div>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
            Verification Link Expired
        </h1>
        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
            @if ($isExpired)
                For your account's protection, email activation links are temporary and expire after <strong>60 minutes</strong>. Because this window has passed, your account remains on hold.
            @else
                This verification link is invalid or has already been used. If your account is not yet active, you may dispatch a fresh verification link below.
            @endif
        </p>
    </div>

    @if (session('mail_error'))
        <div class="mb-5 p-3.5 rounded-md bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/70 text-xs text-amber-900 dark:text-amber-200">
            <div class="font-semibold mb-1 flex items-center gap-1.5 text-amber-950 dark:text-amber-100">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Mail Delivery Notice</span>
            </div>
            <p class="leading-relaxed text-[11px] text-amber-800 dark:text-amber-300">
                Could not connect to external mail server: <code class="text-[10px] bg-amber-100 dark:bg-amber-900/60 px-1 py-0.5 rounded text-amber-900 dark:text-amber-200 break-all">{{ session('mail_error') }}</code>
            </p>
        </div>
    @endif

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center space-x-2 rtl:space-x-reverse">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>A fresh 60-minute verification link has been dispatched to {{ session('resent_email', $user->email) }}.</span>
        </div>
    @endif

    <div class="space-y-4">
        <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 text-xs space-y-2">
            <span class="text-zinc-400 block text-[11px]">Account Awaiting Activation:</span>
            <span class="font-mono font-medium text-zinc-900 dark:text-white block text-sm">
                {{ $user->email }}
            </span>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $user->email }}">

            <button type="submit"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                {{ __('Resend Fresh 60-Minute Verification Link') }}
            </button>
        </form>

        <div class="text-center pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('login') }}" class="font-medium hover:text-zinc-900 dark:hover:text-white transition">
                ← Return to Sign In
            </a>
        </div>
    </div>
</x-guest-layout>
