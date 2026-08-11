@extends('admin.layout')

@section('title', 'Modifier la Langue')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.languages.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux langues
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-edit mr-2"></i>Modifier la langue : {{ $language->native_name }}</h2>

        <form method="POST" action="{{ route('admin.languages.update', $language) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Code de la langue <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           value="{{ old('code', $language->code) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3 uppercase"
                           maxlength="10"
                           required>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Nom en anglais <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $language->name) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Native Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Nom natif <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="native_name" 
                           value="{{ old('native_name', $language->native_name) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                           required>
                    @error('native_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Flag -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Drapeau (Emoji)
                    </label>
                    <input type="text" 
                           name="flag" 
                           value="{{ old('flag', $language->flag) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                           maxlength="10">
                    @error('flag')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Ordre d'affichage
                    </label>
                    <input type="number" 
                           name="order" 
                           value="{{ old('order', $language->order) }}"
                           min="0"
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                    @error('order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active"
                           value="1"
                           {{ old('is_active', $language->is_active) ? 'checked' : '' }}
                           {{ $language->is_default ? 'disabled' : '' }}
                           class="w-5 h-5 text-green-600 border-gray-300 rounded">
                    <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                        Langue activée
                        @if($language->is_default)
                            <span class="text-xs text-gray-500">(La langue par défaut ne peut pas être désactivée)</span>
                        @endif
                    </label>
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.languages.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection



