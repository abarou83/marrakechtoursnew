@extends('admin.layout')

@section('title', 'Traductions du Tour')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tours.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux tours
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2"><i class="fas fa-language mr-2"></i>Traductions : {{ $tour->title }}</h2>
            <p class="text-gray-600">Gérez les traductions de ce tour dans toutes les langues supportées</p>
        </div>

        <form method="POST" action="{{ route('admin.tours.translations.update', $tour) }}">
            @csrf

            @php
                use App\Helpers\LanguageHelper;
                $locales = LanguageHelper::getAvailableLocales();
            @endphp

            <div class="space-y-8">
                @foreach($availableLocales as $locale)
                    @php
                        $translation = $tour->translations->where('locale', $locale)->first();
                        $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                    @endphp

                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-indigo-300 transition">
                        <div class="flex items-center mb-4">
                            <span class="text-3xl mr-3"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.875rem;"></span></span>
                            <h3 class="text-xl font-bold text-gray-900">{{ $localeInfo['native'] }}</h3>
                            <span class="ml-auto text-sm text-gray-500">{{ strtoupper($locale) }}</span>
                        </div>

                        <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">

                        <div class="space-y-4">
                            <!-- Titre -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Titre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="translations[{{ $loop->index }}][title]" 
                                       value="{{ old("translations.{$loop->index}.title", $translation->title ?? $tour->title) }}"
                                       class="w-full border-gray-300 rounded-lg px-4 py-3"
                                       required>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="translations[{{ $loop->index }}][description]" 
                                          rows="4"
                                          class="w-full border-gray-300 rounded-lg px-4 py-3"
                                          required>{{ old("translations.{$loop->index}.description", $translation->description ?? $tour->description) }}</textarea>
                            </div>

                            <!-- Itinerary -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Itinéraire
                                </label>
                                <textarea name="translations[{{ $loop->index }}][itinerary]"
                                          rows="6"
                                          class="w-full border-gray-300 rounded-lg px-4 py-3"
                                          placeholder="Jour 1: ...\nJour 2: ...\n...">{{ old("translations.{$loop->index}.itinerary", $translation->itinerary ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Astuce: une ligne par étape (Jour 1, Jour 2, ...).</p>
                            </div>

                            <!-- Location & Duration -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Lieu / Location
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $loop->index }}][location]" 
                                           value="{{ old("translations.{$loop->index}.location", $translation->location ?? $tour->location) }}"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Durée / Duration
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $loop->index }}][duration]" 
                                           value="{{ old("translations.{$loop->index}.duration", $translation->duration ?? $tour->duration) }}"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                                </div>
                            </div>

                            <!-- SEO Section -->
                            <div class="border-t-2 border-gray-200 pt-4 mt-4">
                                <h4 class="text-md font-bold text-gray-800 mb-3"><i class="fas fa-searchengin mr-2"></i>SEO Avancé</h4>
                                
                                <div class="space-y-4">
                                    <!-- Meta Title & Focus Keyword -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                <i class="fas fa-hashtag mr-1"></i>Meta Title
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $loop->index }}][meta_title]" 
                                                   value="{{ old("translations.{$loop->index}.meta_title", $translation->meta_title ?? '') }}"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3"
                                                   maxlength="60"
                                                   placeholder="Max 60 caractères">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                <i class="fas fa-key mr-1"></i>Mot-clé principal
                                            </label>
                                            <input type="text" 
                                                   name="translations[{{ $loop->index }}][focus_keyword]" 
                                                   value="{{ old("translations.{$loop->index}.focus_keyword", $translation->focus_keyword ?? '') }}"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3"
                                                   maxlength="100"
                                                   placeholder="ex: tour paris eiffel">
                                        </div>
                                    </div>

                                    <!-- Meta Description -->
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            <i class="fas fa-file-alt mr-1"></i>Meta Description
                                        </label>
                                        <textarea name="translations[{{ $loop->index }}][meta_description]" 
                                                  rows="2"
                                                  maxlength="160"
                                                  placeholder="Max 160 caractères"
                                                  class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$loop->index}.meta_description", $translation->meta_description ?? '') }}</textarea>
                                    </div>

                                    <!-- Meta Keywords -->
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            <i class="fas fa-tags mr-1"></i>Meta Keywords
                                        </label>
                                        <input type="text" 
                                               name="translations[{{ $loop->index }}][meta_keywords]" 
                                               value="{{ old("translations.{$loop->index}.meta_keywords", $translation->meta_keywords ?? '') }}"
                                               class="w-full border-gray-300 rounded-lg px-4 py-3"
                                               placeholder="mot1, mot2, mot3">
                                    </div>

                                    <!-- Canonical URL & OG Image -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                🔗 URL Canonique
                                            </label>
                                            <input type="url" 
                                                   name="translations[{{ $loop->index }}][canonical_url]" 
                                                   value="{{ old("translations.{$loop->index}.canonical_url", $translation->canonical_url ?? '') }}"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3"
                                                   placeholder="https://example.com/tour">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                🖼️ Image Open Graph
                                            </label>
                                            <input type="url" 
                                                   name="translations[{{ $loop->index }}][og_image]" 
                                                   value="{{ old("translations.{$loop->index}.og_image", $translation->og_image ?? '') }}"
                                                   class="w-full border-gray-300 rounded-lg px-4 py-3"
                                                   placeholder="https://example.com/image.jpg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer toutes les traductions
                </button>
                <a href="{{ route('admin.tours.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection

