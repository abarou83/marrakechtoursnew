{{-- Contenu de l'onglet Modifier le tour: images + formulaire --}}
<!-- Images actuelles -->
    @if($tour->images->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-images mr-2"></i>Images actuelles</h3>
            <p class="text-sm text-gray-600 mb-4">Survolez une image pour la gérer (définir comme principale ou supprimer)</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($tour->images as $image)
                    <div class="relative group border-2 rounded-lg overflow-hidden {{ $image->is_primary ? 'border-yellow-400' : 'border-gray-200' }}">
                        <!-- Image -->
                        <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt }}" 
                             class="w-full h-32 object-cover">
                        
                        <!-- Badge Image Principale -->
                        @if($image->is_primary)
                            <div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                <i class="fas fa-star mr-1"></i>Principale
                            </div>
                        @endif
                        
                        <!-- Overlay avec actions (visible au survol) -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex space-x-2">
                                <!-- Bouton Définir comme principale -->
                                @if(!$image->is_primary)
                                    <form method="POST" action="{{ route('admin.tours.images.set-primary', $image) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow-lg transition"
                                                title="Définir comme image principale">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Bouton Supprimer -->
                                <form method="POST" action="{{ route('admin.tours.images.delete', $image) }}" 
                                      onsubmit="return confirm('Supprimer cette image ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-lg transition"
                                            title="Supprimer l'image">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Formulaire principal unique -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-edit mr-2"></i>Modifier le tour</h2>
        
        <form method="POST" action="{{ route('admin.tours.update', $tour) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                        <input type="text" name="title" value="{{ old('title', $tour->title) }}" 
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
                        <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50">
                            @foreach($categories as $category)
                                <label class="flex items-center py-2 px-3 hover:bg-white rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="category_ids[]" 
                                           value="{{ $category->id }}" 
                                           {{ in_array($category->id, old('category_ids', $selectedCategoryIds ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2">
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
                            @php
                                $tourTypeValue = $tour->type instanceof \BackedEnum
                                    ? $tour->type->value
                                    : ($tour->type ?? 'activity');
                            @endphp
                            <option value="activity" {{ old('type', $tourTypeValue) === 'activity' ? 'selected' : '' }}>Activity</option>
                            <option value="daytrip" {{ old('type', $tourTypeValue) === 'daytrip' ? 'selected' : '' }}>Day Trip</option>
                            <option value="excursion" {{ old('type', $tourTypeValue) === 'excursion' ? 'selected' : '' }}>Excursion</option>
                            <option value="circuit" {{ old('type', $tourTypeValue) === 'circuit' ? 'selected' : '' }}>Circuit (Multi-days)</option>
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
                            <option value="draft" {{ old('status', $tour->status) === 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status', $tour->status) === 'published' ? 'selected' : '' }}>Publié</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center mt-8">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tour->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
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
                            $translation = $translations[$locale] ?? null;
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
                                       value="{{ old("translations.{$translationIndex}.title", $translation?->title ?? '') }}"
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
                                <textarea id="tour-description-{{ $locale }}"
                                          name="translations[{{ $translationIndex }}][description]" 
                                          rows="5" 
                                          class="w-full border-gray-300 rounded-lg px-4 py-3 js-tour-description-editor" 
                                          required>{{ old("translations.{$translationIndex}.description", $translation?->description ?? '') }}</textarea>
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
                                          class="w-full border-gray-300 rounded-lg px-4 py-3 font-mono text-sm">{{ old("translations.{$translationIndex}.itinerary", $translation?->itinerary ?? '') }}</textarea>
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
                                           value="{{ old("translations.{$translationIndex}.location", $translation?->location ?? '') }}"
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
                                           value="{{ old("translations.{$translationIndex}.duration", $translation?->duration ?? '') }}"
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
                                               value="{{ old("translations.{$translationIndex}.meta_title", $translation?->meta_title ?? '') }}"
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
                                                  class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$translationIndex}.meta_description", $translation?->meta_description ?? '') }}</textarea>
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
                                                   value="{{ old("translations.{$translationIndex}.meta_keywords", $translation?->meta_keywords ?? '') }}"
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
                                                   value="{{ old("translations.{$translationIndex}.focus_keyword", $translation?->focus_keyword ?? '') }}"
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
                        <input type="number" name="capacity" value="{{ old('capacity', $tour->capacity) }}" 
                               class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                        @error('capacity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">
                            💡 Les tarifs se gèrent dans l'onglet 
                            <a href="{{ route('admin.tour-pricings.index', $tour) }}" class="text-purple-600 hover:underline font-semibold">
                                "Tarifs"
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Add New Images -->
            <div class="border-t pt-8 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">📸 Ajouter de nouvelles images</h3>
                
                <div>
                    <input type="file" name="images[]" multiple accept="image/*" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                    @error('images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">💡 Vous pouvez sélectionner plusieurs images à la fois</p>
                </div>
            </div>

            <!-- SEO Global -->
            <div class="border-t pt-8 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-searchengin mr-2"></i>SEO Global</h3>
                <p class="text-sm text-gray-600 mb-6">Paramètres SEO globaux (non traduits)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-link mr-1"></i> URL Canonique
                        </label>
                        <input type="url" name="canonical_url" value="{{ old('canonical_url', $tour->canonical_url) }}" 
                               placeholder="https://votresite.com/tours/{{ $tour->slug }}"
                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                        @error('canonical_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">URL officielle pour éviter le contenu dupliqué</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            🖼️ Image Open Graph (Réseaux sociaux)
                        </label>
                        <input type="url" name="og_image" value="{{ old('og_image', $tour->og_image) }}" 
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
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.tours.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
