@extends('admin.layout')

@section('title', 'Ajouter une Langue')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.languages.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux langues
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">➕ Ajouter une nouvelle langue</h2>

        <form method="POST" action="{{ route('admin.languages.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Code de la langue <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           value="{{ old('code') }}"
                           placeholder="ex: it, pt, zh"
                           class="w-full border-gray-300 rounded-lg px-4 py-3 uppercase"
                           maxlength="10"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Code ISO 639-1 (2 lettres) recommandé</p>
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
                           value="{{ old('name') }}"
                           placeholder="ex: Italian, Portuguese"
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
                           value="{{ old('native_name') }}"
                           placeholder="ex: Italiano, Português"
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
                           value="{{ old('flag') }}"
                           placeholder="ex: 🇮🇹 🇵🇹"
                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                           maxlength="10">
                    <p class="text-xs text-gray-500 mt-1">
                        Copiez un emoji drapeau depuis 
                        <a href="https://emojipedia.org/flags/" target="_blank" class="text-blue-600 hover:underline">emojipedia.org</a>
                    </p>
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
                           value="{{ old('order', 0) }}"
                           min="0"
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500 mt-1">Plus le nombre est petit, plus la langue apparaît en premier</p>
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
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 text-green-600 border-gray-300 rounded">
                    <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                        Activer cette langue immédiatement
                    </label>
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Ajouter la langue
                </button>
                <a href="{{ route('admin.languages.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection



