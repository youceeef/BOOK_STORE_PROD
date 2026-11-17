<section x-data="{
        preview: null,
        removeAvatar: false,
        onFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.removeAvatar = false;
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.preview = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }">
    <header class="pb-4 border-b border-zinc-100 dark:border-zinc-800">
        <h2 class="font-serif text-lg font-bold text-zinc-900 dark:text-white">
            {{ __('Personal & Shipping Details') }}
        </h2>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            {{ __('Manage your literary profile, profile picture, and default delivery address.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Hidden remove avatar flag --}}
        <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">

        {{-- 1. Profile Picture (Avatar) --}}
        <div>
            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                {{ __('Profile Picture') }}
            </label>

            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                {{-- Live Image Preview Thumbnail --}}
                <div class="w-16 h-16 rounded-full overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center flex-shrink-0">
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!preview && !removeAvatar">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-serif font-bold text-lg text-zinc-700 dark:text-zinc-300">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </template>
                    <template x-if="removeAvatar">
                        <span class="font-serif font-bold text-lg text-zinc-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </template>
                </div>

                <div class="space-y-1.5 flex-1">
                    <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg,image/webp"
                        @change="onFileChange($event)"
                        class="block w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-800 dark:file:text-zinc-200 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 file:cursor-pointer transition">

                    <div class="flex items-center space-x-3 rtl:space-x-reverse text-[11px] text-zinc-400">
                        <span>{{ __('Recommended: Square JPG, PNG, or WebP up to 2MB') }}</span>
                        @if ($user->avatar)
                            <button type="button"
                                @click="removeAvatar = !removeAvatar; preview = null;"
                                class="text-rose-600 dark:text-rose-400 hover:underline font-medium">
                                <span x-text="removeAvatar ? '{{ __('Keep current picture') }}' : '{{ __('Remove picture') }}'"></span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        {{-- 2. Basic Contact Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-zinc-100 dark:border-zinc-800">
            <div>
                <label for="name" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    {{ __('Full Name') }} <span class="text-rose-500">*</span>
                </label>
                <input id="name" name="name" type="text"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                    class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    {{ __('Email Address') }} <span class="text-rose-500">*</span>
                </label>
                <input id="email" name="email" type="email"
                    value="{{ old('email', $user->email) }}" required autocomplete="username"
                    class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                <x-input-error class="mt-1" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 text-xs">
                        <p class="text-amber-700 dark:text-amber-400">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="underline ml-1 font-semibold hover:text-amber-900">
                                {{ __('Resend verification email') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1 font-medium text-xs text-zinc-700 dark:text-zinc-300">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <label for="phone" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    {{ __('Phone Number') }}
                </label>
                <input id="phone" name="phone" type="tel" placeholder="+1 (555) 019-2834"
                    value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                    class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
            </div>

            <div>
                <label for="country" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    {{ __('Country / Region') }}
                </label>
                <input id="country" name="country" type="text" placeholder="United States"
                    value="{{ old('country', $user->country ?? 'United States') }}"
                    class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                <x-input-error class="mt-1" :messages="$errors->get('country')" />
            </div>
        </div>

        {{-- 3. Literary Bio / Reading Interests --}}
        <div>
            <label for="bio" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                {{ __('Reader Biography & Interests') }}
            </label>
            <textarea id="bio" name="bio" rows="3"
                placeholder="{{ __('Share your favorite genres, eras, or literary interests...') }}"
                class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition leading-relaxed">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-1" :messages="$errors->get('bio')" />
            <span class="text-[11px] text-zinc-400">{{ __('Displayed optionally on your reader reviews and community notes.') }}</span>
        </div>

        {{-- 4. Default Delivery & Shipping Address --}}
        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
            <div>
                <h3 class="font-serif text-sm font-bold text-zinc-900 dark:text-white">
                    {{ __('Default Shipping Address') }}
                </h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ __('Used to pre-fill your delivery details at checkout for faster ordering.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="address_line1" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                        {{ __('Street Address') }}
                    </label>
                    <input id="address_line1" name="address_line1" type="text" placeholder="124 Oxford Street"
                        value="{{ old('address_line1', $user->address_line1) }}" autocomplete="address-line1"
                        class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                    <x-input-error class="mt-1" :messages="$errors->get('address_line1')" />
                </div>

                <div class="sm:col-span-2">
                    <label for="address_line2" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                        {{ __('Apartment, Suite, Unit (Optional)') }}
                    </label>
                    <input id="address_line2" name="address_line2" type="text" placeholder="Suite 4B, 2nd Floor"
                        value="{{ old('address_line2', $user->address_line2) }}" autocomplete="address-line2"
                        class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                    <x-input-error class="mt-1" :messages="$errors->get('address_line2')" />
                </div>

                <div>
                    <label for="city" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                        {{ __('City') }}
                    </label>
                    <input id="city" name="city" type="text" placeholder="London"
                        value="{{ old('city', $user->city) }}" autocomplete="address-level2"
                        class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                    <x-input-error class="mt-1" :messages="$errors->get('city')" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="state" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            {{ __('State / Region') }}
                        </label>
                        <input id="state" name="state" type="text" placeholder="Greater London"
                            value="{{ old('state', $user->state) }}" autocomplete="address-level1"
                            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                        <x-input-error class="mt-1" :messages="$errors->get('state')" />
                    </div>

                    <div>
                        <label for="postal_code" class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            {{ __('Postal Code') }}
                        </label>
                        <input id="postal_code" name="postal_code" type="text" placeholder="W1D 1BS"
                            value="{{ old('postal_code', $user->postal_code) }}" autocomplete="postal-code"
                            class="w-full text-xs rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3.5 py-2.5 focus:border-zinc-900 dark:focus:border-white focus:ring-1 focus:ring-zinc-900 dark:focus:ring-white transition">
                        <x-input-error class="mt-1" :messages="$errors->get('postal_code')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between pt-5 border-t border-zinc-100 dark:border-zinc-800">
            <button type="submit"
                class="px-4 py-2.5 rounded-md text-xs font-medium text-white bg-zinc-900 hover:bg-black dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition shadow-xs">
                {{ __('Save Profile Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs text-zinc-600 dark:text-zinc-300 font-medium flex items-center space-x-1 rtl:space-x-reverse">
                    <span>✓</span>
                    <span>{{ __('Profile details updated.') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
