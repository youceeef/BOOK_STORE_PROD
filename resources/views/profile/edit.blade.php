<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
            Account Settings
        </h1>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        {{-- Profile Banner Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 text-center sm:text-left">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1a1a2e] to-[#2d3055] text-gold-400 border border-gold-500/30 flex items-center justify-center font-serif text-3xl font-bold shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="font-serif text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    @if (auth()->user()->is_admin)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gold-50 text-gold-600 border border-gold-200 self-center sm:self-auto">
                            Store Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 self-center sm:self-auto">
                            Active Reader
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    Member since {{ auth()->user()->created_at->format('F Y') }}
                </p>
            </div>
        </div>

        {{-- Profile Information Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Danger Zone Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-rose-100 bg-rose-50/10">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
