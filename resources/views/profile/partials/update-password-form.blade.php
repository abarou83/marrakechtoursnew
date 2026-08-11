<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <div class="space-y-6">
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="text-red-500">*</span> Mot de passe actuel
            </label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   autocomplete="current-password"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('current_password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="text-red-500">*</span> Nouveau mot de passe
            </label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
            @error('password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="text-red-500">*</span> Confirmer le mot de passe
            </label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
        </div>
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" 
                class="px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg"
                style="background-color: {{ primary_color() }};">
            <i class="fas fa-key mr-2"></i>
            Enregistrer
        </button>

        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }"
               x-show="show"
               x-transition
               x-init="setTimeout(() => show = false, 3000)"
               class="text-sm text-green-600 font-semibold flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Mot de passe mis à jour avec succès !
            </p>
        @endif
    </div>
</form>
