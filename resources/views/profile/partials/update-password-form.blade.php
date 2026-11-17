<section>
    <header class="pb-4 border-b border-zinc-100 dark:border-zinc-800">
        <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white">
            {{ __('Update Security Password') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            {{ __('Ensure your account uses a secure, long passphrase to safeguard your library and orders.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4 max-w-lg">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                {{ __('Current Password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                {{ __('New Password') }}
            </label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                {{ __('Confirm New Password') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <button type="submit"
                class="px-4 py-2.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs text-zinc-600 dark:text-zinc-300 font-medium flex items-center space-x-1 rtl:space-x-reverse">
                    <span>✓</span>
                    <span>{{ __('Password updated.') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
