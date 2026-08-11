<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="text-red-500">*</span> Nom complet
            </label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="text-red-500">*</span> Email
            </label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-primary hover:text-primary/80">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                Téléphone
            </label>
            <input id="phone" 
                   name="phone" 
                   type="tel" 
                   value="{{ old('phone', $user->phone ?? '') }}" 
                   autocomplete="tel"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            <input type="hidden" name="phone_country_code" id="phone_country_code" value="">
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                Adresse
            </label>
            <input id="address" 
                   name="address" 
                   type="text" 
                   value="{{ old('address', $user->address ?? '') }}" 
                   autocomplete="street-address"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                Ville
            </label>
            <input id="city" 
                   name="city" 
                   type="text" 
                   value="{{ old('city', $user->city ?? '') }}" 
                   autocomplete="address-level2"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('city')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">
                Code postal
            </label>
            <input id="postal_code" 
                   name="postal_code" 
                   type="text" 
                   value="{{ old('postal_code', $user->postal_code ?? '') }}" 
                   autocomplete="postal-code"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('postal_code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">
                Pays
            </label>
            <input id="country" 
                   name="country" 
                   type="text" 
                   value="{{ old('country', $user->country ?? '') }}" 
                   autocomplete="country-name"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('country')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" 
                class="px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg"
                style="background-color: {{ primary_color() }};">
            <i class="fas fa-save mr-2"></i>
            Enregistrer
        </button>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }"
               x-show="show"
               x-transition
               x-init="setTimeout(() => show = false, 3000)"
               class="text-sm text-green-600 font-semibold flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Enregistré avec succès !
            </p>
        @endif
    </div>
</form>
