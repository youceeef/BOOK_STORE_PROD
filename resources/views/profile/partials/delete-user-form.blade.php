<section class="space-y-4">
    <header class="pb-4 border-b border-zinc-100 dark:border-zinc-800">
        <h2 class="font-serif text-lg font-bold text-rose-600 dark:text-rose-400">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            {{ __('Permanently remove your account, past reading history, and stored preferences. This action cannot be undone.') }}
        </p>
    </header>

    <div>
        <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-4 py-2 rounded-md text-xs font-medium text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200 dark:border-rose-900 transition">
            {{ __('Delete Account Permanently') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-[#12131a] rounded-lg">
            @csrf
            @method('delete')

            <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white">
                {{ __('Confirm Account Deletion') }}
            </h2>

            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {{ __('Please verify your password to permanently delete your account and associated bookstore records.') }}
            </p>

            <div class="mt-5">
                <label for="delete_password" class="sr-only">{{ __('Password') }}</label>
                <input id="delete_password" name="password" type="password" placeholder="{{ __('Enter your password to confirm') }}"
                    class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-6 flex justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-medium text-white bg-rose-600 hover:bg-rose-700 transition shadow-xs">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
