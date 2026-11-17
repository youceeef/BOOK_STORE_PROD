<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-0.5">{{ __('Reader Account') }}</p>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ __('Profile & Preferences') }}
                </h1>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse self-start sm:self-auto">
                <a href="{{ route('orders.index') }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    {{ __('Order History') }}
                </a>
                <a href="{{ route('wishlist.index') }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
                    {{ __('Saved Volumes') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Top Reader Profile Banner --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-6 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 text-center md:text-left rtl:md:text-right">
                
                {{-- Avatar Display --}}
                <div class="relative flex-shrink-0">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700 shadow-sm">
                    @else
                        <div class="w-20 h-20 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center font-serif text-2xl font-bold border-2 border-zinc-200 dark:border-zinc-700 shadow-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- User Info & Role --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                        <div>
                            <div class="flex items-center justify-center md:justify-start space-x-2 rtl:space-x-reverse">
                                <h2 class="font-serif text-xl font-bold text-zinc-900 dark:text-white truncate">
                                    {{ $user->name }}
                                </h2>
                                @if ($user->is_admin)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                        {{ __('Admin') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $user->email }}</p>
                        </div>

                        <div class="text-xs text-zinc-400">
                            {{ __('Member since') }} {{ $user->created_at->format('M Y') }}
                        </div>
                    </div>

                    @if ($user->bio)
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-3 leading-relaxed max-w-2xl italic">
                            &ldquo;{{ $user->bio }}&rdquo;
                        </p>
                    @endif

                    {{-- Activity Metric Strip --}}
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-3 gap-3 text-center md:text-left rtl:md:text-right">
                        <div>
                            <span class="font-serif text-lg font-bold text-zinc-900 dark:text-white block tabular-nums">
                                {{ $ordersCount ?? $user->orders()->count() }}
                            </span>
                            <span class="text-[11px] text-zinc-400">{{ __('Orders Placed') }}</span>
                        </div>
                        <div>
                            <span class="font-serif text-lg font-bold text-zinc-900 dark:text-white block tabular-nums">
                                {{ $wishlistCount ?? $user->wishlists()->count() }}
                            </span>
                            <span class="text-[11px] text-zinc-400">{{ __('Saved Volumes') }}</span>
                        </div>
                        <div>
                            <span class="font-serif text-lg font-bold text-zinc-900 dark:text-white block tabular-nums">
                                {{ $reviewsCount ?? $user->reviews()->count() }}
                            </span>
                            <span class="text-[11px] text-zinc-400">{{ __('Reviews Written') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Detailed Profile & Address Information --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-6 sm:p-8 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Security & Password Management --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-6 sm:p-8 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Browser Sessions & Device Security --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-6 sm:p-8 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            @include('profile.partials.logout-other-browser-sessions-form')
        </div>

        {{-- Danger Zone (Account Deletion) --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg p-6 sm:p-8 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>
