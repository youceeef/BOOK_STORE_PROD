<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
            Account Settings
        </h1>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Profile Header Card --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-5 sm:p-6 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-5 rtl:sm:space-x-reverse text-center sm:text-left rtl:sm:text-right">
            <div class="w-14 h-14 rounded-md bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center font-serif text-xl font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="font-serif text-xl font-bold text-zinc-900 dark:text-white">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</p>
                    </div>
                    @if (auth()->user()->is_admin)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 self-center sm:self-auto">
                            Store Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 self-center sm:self-auto">
                            Active Reader
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-zinc-400 mt-1">
                    Member since {{ auth()->user()->created_at->format('F Y') }}
                </p>
            </div>
        </div>

        {{-- Profile Information Card --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-5 sm:p-6 border border-zinc-200 dark:border-zinc-800">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password Card --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-5 sm:p-6 border border-zinc-200 dark:border-zinc-800">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Danger Zone Card --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-5 sm:p-6 border border-rose-200 dark:border-rose-900/60 bg-rose-50/10">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>