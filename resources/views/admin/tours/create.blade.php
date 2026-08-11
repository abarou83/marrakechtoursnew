@extends('admin.layout')

@section('title', 'Créer un Tour')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tours.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
            ← Retour aux tours
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-800 mb-2">Erreur lors de l'upload :</p>
                        <ul class="list-disc list-inside text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.tours.store') }}" enctype="multipart/form-data">
            @csrf
            
            @php
                use App\Helpers\LanguageHelper;
                $locales = LanguageHelper::getAvailableLocales();
            @endphp
            
            <!-- Basic Info -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-info-circle mr-2"></i>Informations de base</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Titre (pour le slug) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                        <p class="text-xs text-gray-500 mt-1">Utilisé uniquement pour générer le slug</p>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Catégories <span class="text-red-500">*</span>
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50" id="categories-container">
                            @foreach($categories as $category)
                                <label class="flex items-center py-2 px-3 hover:bg-white rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="category_ids[]" 
                                           value="{{ $category->id }}" 
                                           {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                                           class="category-checkbox w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2">
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sélectionnez une ou plusieurs catégories</p>
                        @error('category_ids')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('category_ids.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Type de tour <span class="text-red-500">*</span>
                        </label>
                        <select name="type" class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                            <option value="activity" {{ old('type') === 'activity' ? 'selected' : '' }}>Activity</option>
                            <option value="daytrip" {{ old('type') === 'daytrip' ? 'selected' : '' }}>Day Trip</option>
                            <option value="excursion" {{ old('type') === 'excursion' ? 'selected' : '' }}>Excursion</option>
                            <option value="circuit" {{ old('type') === 'circuit' ? 'selected' : '' }}>Circuit (Multi-days)</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="status" class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publié</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center mt-8">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-bold text-gray-700">Tour actif</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Désactiver pour masquer le tour</p>
                        @error('is_active')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Traductions avec Tabs -->
            <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-language mr-2"></i>Contenu multilingue
                    </h3>
                    
                    {{-- Tabs Navigation --}}
                    <div class="border-b border-gray-200 -mx-6 px-6">
                        <div class="flex space-x-1">
                            @foreach($availableLocales as $locale)
                                @php
                                    $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                                @endphp
                                <button 
                                    type="button"
                                    @click="activeTab = '{{ $locale }}'"
                                    :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="inline-flex items-center px-4 py-2 border-b-2 font-semibold text-sm transition-colors duration-200">
                                    <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-2" style="font-size: 1.25rem;"></span>
                                    <span>{{ $localeInfo['native'] }}</span>
                                    <span class="ml-2 text-xs opacity-75">({{ strtoupper($locale) }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                {{-- Tab Content --}}
                <div class="p-6">
                    @foreach($availableLocales as $locale)
                        @php
                            $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                            $translationIndex = $loop->index;
                        @endphp
                        
                        <div x-show="activeTab === '{{ $locale }}'" x-transition class="space-y-6">
                            <div class="flex items-center mb-4">
                                <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-2" style="font-size: 1.5rem;"></span>
                                <h4 class="font-bold text-gray-900">Contenu pour {{ $localeInfo['native'] }}</h4>
                                <span class="ml-auto text-xs text-gray-500">{{ strtoupper($locale) }}</span>
                            </div>
                            
                            <input type="hidden" name="translations[{{ $translationIndex }}][locale]" value="{{ $locale }}">
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Titre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="translations[{{ $translationIndex }}][title]" 
                                       value="{{ old("translations.{$translationIndex}.title") }}"
                                       class="w-full border-gray-300 rounded-lg px-4 py-3" 
                                       required>
                                @error("translations.{$translationIndex}.title")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea id="tour-description-create-{{ $locale }}"
                                          name="translations[{{ $translationIndex }}][description]" 
                                          rows="5" 
                                          class="w-full border-gray-300 rounded-lg px-4 py-3 js-tour-description-editor" 
                                          required>{{ old("translations.{$translationIndex}.description") }}</textarea>
                                @error("translations.{$translationIndex}.description")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Itinéraire
                                </label>
                                <textarea name="translations[{{ $translationIndex }}][itinerary]" 
                                          rows="8" 
                                          placeholder="Titre 1|Description de l'étape 1&#10;Titre 2|Description de l'étape 2&#10;Titre 3|Description de l'étape 3"
                                          class="w-full border-gray-300 rounded-lg px-4 py-3 font-mono text-sm">{{ old("translations.{$translationIndex}.itinerary") }}</textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    💡 Format: Une ligne par étape. Utilisez <code class="bg-gray-100 px-1 rounded">|</code> ou <code class="bg-gray-100 px-1 rounded"> - </code> pour séparer le titre et le texte.<br>
                                    Exemple: <code class="bg-gray-100 px-1 rounded">Accueil|Rencontre avec le guide à l'entrée principale</code> ou <code class="bg-gray-100 px-1 rounded">Accueil - Rencontre avec le guide</code>
                                </p>
                                @error("translations.{$translationIndex}.itinerary")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Lieu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $translationIndex }}][location]" 
                                           value="{{ old("translations.{$translationIndex}.location") }}"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3" 
                                           required>
                                    @error("translations.{$translationIndex}.location")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Durée <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $translationIndex }}][duration]" 
                                           value="{{ old("translations.{$translationIndex}.duration") }}"
                                           placeholder="Ex: 2 heures"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3" 
                                           required>
                                    @error("translations.{$translationIndex}.duration")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="border-t pt-6 mt-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4">SEO pour {{ $localeInfo['native'] }}</h5>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Meta Title
                                        </label>
                                        <input type="text" 
                                               name="translations[{{ $translationIndex }}][meta_title]" 
                                               value="{{ old("translations.{$translationIndex}.meta_title") }}"
                                               maxlength="60"
                                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                                        <p class="text-xs text-gray-500 mt-1">Max 60 caractères</p>
                                        @error("translations.{$translationIndex}.meta_title")
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Meta Description
                                        </label>
                                        <textarea name="translations[{{ $translationIndex }}][meta_description]" 
                                                  rows="3" 
                                                  maxlength="160"
                                                  class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$translationIndex}.meta_description") }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">Max 160 caractères</p>
                                        @error("translations.{$translationIndex}.meta_description")
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                Meta Keywords
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $translationIndex }}][meta_keywords]" 
                                                   value="{{ old("translations.{$translationIndex}.meta_keywords") }}"
                                                   placeholder="tour, paris, eiffel"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                                            @error("translations.{$translationIndex}.meta_keywords")
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                Focus Keyword
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $translationIndex }}][focus_keyword]" 
                                                   value="{{ old("translations.{$translationIndex}.focus_keyword") }}"
                                                   placeholder="tour paris eiffel"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                                            @error("translations.{$translationIndex}.focus_keyword")
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Capacity -->
            <div class="border-t pt-8 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-users mr-2"></i>Capacité</h3>
                
                <div class="max-w-md">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Capacité max <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity') }}" 
                               class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                        @error('capacity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">
                            💡 Les tarifs se gèrent dans la section "Tarifs" après la création du tour
                        </p>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="border-t pt-8 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">🖼️ Images</h3>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Ajouter des images
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                    @error('images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Vous pouvez sélectionner plusieurs images</p>
                </div>
            </div>

            <!-- SEO Global -->
            <div class="border-t pt-8 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-searchengin mr-2"></i>SEO Global</h3>
                <p class="text-sm text-gray-600 mb-6">Paramètres SEO globaux (non traduits)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            🔗 URL Canonique
                        </label>
                        <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" 
                               placeholder="https://votresite.com/tours/slug"
                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                        @error('canonical_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">URL officielle pour éviter le contenu dupliqué</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            🖼️ Image Open Graph (Réseaux sociaux)
                        </label>
                        <input type="url" name="og_image" value="{{ old('og_image') }}" 
                               placeholder="https://votresite.com/images/tour-image.jpg"
                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                        @error('og_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">URL de l'image affichée lors du partage (1200x630px)</p>
                    </div>
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 font-bold shadow-lg">
                    ✅ Créer le Tour
                </button>
                <a href="{{ route('admin.tours.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <script>
        // Validation: au moins une catégorie doit être sélectionnée
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.category-checkbox:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins une catégorie.');
                return false;
            }
        });
    </script>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editors = [];

                document.querySelectorAll('.js-tour-description-editor').forEach((textarea) => {
                    ClassicEditor.create(textarea).then((editor) => {
                        editors.push(editor);
                    }).catch((error) => {
                        console.error('CKEditor init error:', error);
                    });
                });

                const form = document.querySelector('form[action*="admin/tours"]');
                if (form) {
                    form.addEventListener('submit', function () {
                        editors.forEach((editor) => editor.updateSourceElement());
                    });
                }
            });
        </script>
    @endpush
@endsection


