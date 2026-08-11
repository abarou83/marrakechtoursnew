@extends('admin.layout')

@section('title', 'Ajouter une Page')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux Pages
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-plus-circle mr-2"></i>Ajouter une nouvelle Page</h2>

        <form method="POST" action="{{ route('admin.pages.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Configuration générale -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Configuration</h3>
                    
                    <div class="space-y-4">
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
                            <p class="text-xs text-gray-500 mt-1">Plus le nombre est petit, plus la page apparaît en premier</p>
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center mt-4">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                            Activer cette page immédiatement
                        </label>
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
                                
                                <!-- Slug -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Slug (URL) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $translationIndex }}][slug]" 
                                           value="{{ old("translations.{$translationIndex}.slug") }}"
                                           placeholder="ex: confidentialite, conditions-generales"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">L'URL de la page sera : /page/[slug]</p>
                                    @error("translations.{$translationIndex}.slug")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Title -->
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

                                <!-- Content -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Contenu <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="translations[{{ $translationIndex }}][content]" 
                                              rows="10"
                                              class="w-full border-gray-300 rounded-lg px-4 py-3"
                                              required>{{ old("translations.{$translationIndex}.content") }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Vous pouvez utiliser du HTML pour formater le contenu</p>
                                    @error("translations.{$translationIndex}.content")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SEO Section -->
                                <div class="border-t border-gray-200 pt-6 mt-6">
                                    <h5 class="text-md font-bold text-gray-900 mb-4">
                                        <i class="fas fa-search mr-2 text-indigo-600"></i>Paramètres SEO
                                    </h5>
                                    
                                    <div class="space-y-4">
                                        <!-- Meta Title -->
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                Meta Title
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $translationIndex }}][meta_title]" 
                                                   value="{{ old("translations.{$translationIndex}.meta_title") }}"
                                                   placeholder="Titre pour les moteurs de recherche (max 60 caractères)"
                                                   maxlength="60"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                                            <p class="text-xs text-gray-500 mt-1">Recommandé: 50-60 caractères</p>
                                            @error("translations.{$translationIndex}.meta_title")
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Meta Description -->
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                Meta Description
                                            </label>
                                            <textarea name="translations[{{ $translationIndex }}][meta_description]" 
                                                      rows="3"
                                                      placeholder="Description pour les moteurs de recherche (max 160 caractères)"
                                                      maxlength="160"
                                                      class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$translationIndex}.meta_description") }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">Recommandé: 150-160 caractères</p>
                                            @error("translations.{$translationIndex}.meta_description")
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Meta Keywords -->
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                Meta Keywords
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $translationIndex }}][meta_keywords]" 
                                                   value="{{ old("translations.{$translationIndex}.meta_keywords") }}"
                                                   placeholder="Mots-clés séparés par des virgules"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                                            <p class="text-xs text-gray-500 mt-1">Ex: confidentialité, données, protection</p>
                                            @error("translations.{$translationIndex}.meta_keywords")
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Ajouter la Page
                </button>
                <a href="{{ route('admin.pages.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection

