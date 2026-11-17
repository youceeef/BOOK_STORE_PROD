<section class="space-y-4" x-data="{ confirmingLogout: false }">
    <header class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
        <h2 class="font-serif text-base font-bold text-zinc-900 dark:text-white">
            {{ __('Active Browser Sessions & Device Security') }}
        </h2>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
            {{ __('Manage your active sessions across other browsers and mobile devices. You can terminate sessions on all other devices with your password.') }}
        </p>
    </header>

    @if (session('status') === 'other-sessions-terminated')
        <div class="p-3 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center space-x-2 rtl:space-x-reverse">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ __('All other browser sessions have been successfully terminated.') }}</span>
        </div>
    @endif

    {{-- Current Session Card --}}
    <div class="flex items-center space-x-3 rtl:space-x-reverse p-3.5 rounded-lg bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 text-xs">
        <div class="w-8 h-8 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-zinc-900 dark:text-white">This Device</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                    Active Now
                </span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                Current authenticated web session.
            </p>
        </div>
    </div>

    <div>
        <button type="button" @click="confirmingLogout = true"
            class="px-3.5 py-2 rounded-md text-xs font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
            {{ __('Log Out Other Browser Sessions') }}
        </button>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="confirmingLogout" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60"
        style="display: none;">
        <div @click.outside="confirmingLogout = false"
            class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-6 max-w-md w-full shadow-xl space-y-4 text-xs">
            <div>
                <h3 class="font-serif text-lg font-bold text-zinc-900 dark:text-white">
                    {{ __('Log Out Other Browser Sessions') }}
                </h3>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ __('Please enter your current password to confirm you would like to log out of your other browser sessions across all devices.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('profile.logout-other-devices') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="session-password" class="block font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        {{ __('Current Password') }}
                    </label>
                    <input id="session-password" type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md px-3 py-2 text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-400">
                    @error('password', 'logoutOtherDevices')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="confirmingLogout = false"
                        class="px-3 py-2 rounded-md font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-md font-semibold bg-rose-600 hover:bg-rose-700 text-white transition shadow-xs">
                        {{ __('Log Out Other Sessions') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
