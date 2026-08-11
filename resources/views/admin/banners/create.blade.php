@extends('admin.layout')

@section('title', 'Nouvelle Bannière')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.banners.index') }}" class="text-indigo-600 hover:underline">← Retour</a>
    <h1 class="text-2xl font-bold mt-3 mb-6">Créer une bannière</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Images <span class="text-red-500">*</span></label>
            <input type="file" name="images[]" accept="image/*" multiple class="w-full border-gray-300 rounded-lg" required>
            <p class="text-xs text-gray-500 mt-1">Taille recommandée: 1600x600. Vous pouvez sélectionner plusieurs images.</p>
            <div id="image-preview" class="mt-4 grid grid-cols-3 gap-4"></div>
            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Image principale</label>
                <select name="primary_image_index" class="w-full border-gray-300 rounded-lg">
                    <option value="0">Première image</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Sélectionnez l'image principale (par défaut: la première)</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Lien (optionnel)</label>
            <input type="url" name="link_url" value="{{ old('link_url') }}" class="w-full border-gray-300 rounded-lg" placeholder="https://...">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ordre</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" class="w-full border-gray-300 rounded-lg">
            </div>

            <div class="flex items-center mt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-green-600 border-gray-300 rounded">
                <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">Activer</label>
            </div>
        </div>

        {{-- Traductions avec tabs --}}
        <div class="border border-gray-200 rounded-xl mt-6 overflow-hidden" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
            <div class="bg-gray-50 px-6 pt-4 pb-0">
                <h3 class="text-lg font-bold text-gray-900 mb-3">
                    <i class="fas fa-language mr-2 text-indigo-500"></i>Traductions
                </h3>
                <div class="flex space-x-1 border-b border-gray-200">
                    @foreach($availableLocales as $locale)
                        @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                        <button type="button"
                            @click="activeTab = '{{ $locale }}'"
                            :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors rounded-t-lg">
                            <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-1.5" style="font-size: 1rem;"></span>
                            <span>{{ strtoupper($locale) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="p-6">
                @foreach($availableLocales as $locale)
                    @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                    <div x-show="activeTab === '{{ $locale }}'" x-transition>
                        <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Titre ({{ $localeInfo['native'] }}) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="translations[{{ $loop->index }}][title]" 
                                       value="{{ old("translations.{$loop->index}.title") }}"
                                       class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500"
                                       required
                                       placeholder="Titre en {{ $localeInfo['native'] }}">
                                @error("translations.{$loop->index}.title")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sous-titre -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Sous-titre (optionnel)
                                </label>
                                <input type="text" 
                                       name="translations[{{ $loop->index }}][slug]" 
                                       value="{{ old("translations.{$loop->index}.slug") }}"
                                       class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Sous-titre en {{ $localeInfo['native'] }}">
                                @error("translations.{$loop->index}.slug")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Affiché sous le titre principal. Laissez vide pour ne rien afficher.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex space-x-4 mt-6">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">
                <i class="fas fa-save mr-2"></i>Créer la bannière
            </button>
            <a href="{{ route('admin.banners.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[name="images[]"]');
    const previewDiv = document.getElementById('image-preview');
    const primarySelect = document.querySelector('select[name="primary_image_index"]');
    
    fileInput.addEventListener('change', function(e) {
        previewDiv.innerHTML = '';
        primarySelect.innerHTML = '';
        
        const files = Array.from(e.target.files);
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-32 object-cover rounded-lg border-2 border-gray-200';
                previewDiv.appendChild(img);
                
                const option = document.createElement('option');
                option.value = index;
                option.textContent = `Image ${index + 1}`;
                if (index === 0) option.selected = true;
                primarySelect.appendChild(option);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endsection
