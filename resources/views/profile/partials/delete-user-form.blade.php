<div x-data="{ showModal: false }">
    <button type="button"
            @click="showModal = true"
            class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all duration-300 hover:shadow-lg">
        <i class="fas fa-trash-alt mr-2"></i>
        Supprimer mon compte
    </button>

    <!-- Modal -->
    <div x-show="showModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-red-900 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                    Confirmer la suppression
                </h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                @csrf
                @method('delete')

                <p class="text-gray-700">
                    Êtes-vous sûr de vouloir supprimer votre compte ? Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.
                </p>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Mot de passe
                    </label>
                    <input id="password"
                           name="password"
                           type="password"
                           placeholder="Entrez votre mot de passe"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-red-500 focus:outline-none">
                    @error('password', 'userDeletion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button"
                            @click="showModal = false"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
