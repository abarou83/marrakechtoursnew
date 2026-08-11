@extends('admin.layout')

@section('title', 'Paramètres du Site')

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'informations' }">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cog mr-3 text-purple-600"></i>
                Paramètres du Site
            </h1>
            <p class="text-gray-600 mt-2">Gérez toutes les configurations de votre site</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-1" aria-label="Tabs">
            <button type="button"
                    @click="activeTab = 'informations'"
                    :class="activeTab === 'informations' ? 'bg-purple-100 text-purple-700 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-transparent'"
                    class="px-6 py-4 font-semibold text-sm rounded-t-lg border-b-2 transition-all flex items-center space-x-2">
                <i class="fas fa-building"></i>
                <span>Informations</span>
            </button>
            <button type="button"
                    @click="activeTab = 'personnalisation'"
                    :class="activeTab === 'personnalisation' ? 'bg-purple-100 text-purple-700 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-transparent'"
                    class="px-6 py-4 font-semibold text-sm rounded-t-lg border-b-2 transition-all flex items-center space-x-2">
                <i class="fas fa-palette"></i>
                <span>Personnalisation</span>
            </button>
            <button type="button"
                    @click="activeTab = 'maintenance'"
                    :class="activeTab === 'maintenance' ? 'bg-purple-100 text-purple-700 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-transparent'"
                    class="px-6 py-4 font-semibold text-sm rounded-t-lg border-b-2 transition-all flex items-center space-x-2">
                <i class="fas fa-tools"></i>
                <span>Maintenance</span>
            </button>
            <button type="button"
                    @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews' ? 'bg-purple-100 text-purple-700 border-purple-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-transparent'"
                    class="px-6 py-4 font-semibold text-sm rounded-t-lg border-b-2 transition-all flex items-center space-x-2">
                <i class="fas fa-star"></i>
                <span>Reviews Home</span>
            </button>
        </nav>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ============================================= --}}
        {{-- TAB 1: INFORMATIONS --}}
        {{-- ============================================= --}}
        <div x-show="activeTab === 'informations'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-lg shadow-lg p-8">
                
                {{-- Informations de la Société --}}
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-building mr-3 text-blue-600"></i>
                        Informations de la Société
                    </h3>
                    <p class="text-gray-600 mb-6">Configurez les informations de votre entreprise affichées sur le site</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nom de la société --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-store mr-2 text-blue-500"></i>Nom de la société
                            </label>
                            <input type="text" 
                                   name="company_name" 
                                   value="{{ site_setting('company_name', config('app.name')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Nom de votre entreprise">
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-500"></i>Email de contact
                            </label>
                            <input type="email" 
                                   name="company_email" 
                                   value="{{ site_setting('company_email', 'contact@example.com') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="contact@example.com">
                        </div>
                        
                        {{-- Téléphone --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>Téléphone
                            </label>
                            <input type="text" 
                                   name="company_phone" 
                                   value="{{ site_setting('company_phone', '+33 1 23 45 67 89') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="+33 1 23 45 67 89">
                        </div>
                        
                        {{-- Adresse --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Adresse
                            </label>
                            <input type="text" 
                                   name="company_address" 
                                   value="{{ site_setting('company_address', 'Paris, France') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="123 Rue Example, 75001 Paris">
                        </div>
                    </div>
                    
                    {{-- Réseaux sociaux --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-share-alt mr-2 text-blue-500"></i>
                            Réseaux Sociaux
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Facebook --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook
                                </label>
                                <input type="url" 
                                       name="social_facebook" 
                                       value="{{ site_setting('social_facebook') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://facebook.com/votrepage">
                            </div>
                            
                            {{-- Instagram --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram
                                </label>
                                <input type="url" 
                                       name="social_instagram" 
                                       value="{{ site_setting('social_instagram') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://instagram.com/votrepage">
                            </div>
                            
                            {{-- Twitter/X --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-x-twitter text-gray-900 mr-2"></i>Twitter / X
                                </label>
                                <input type="url" 
                                       name="social_twitter" 
                                       value="{{ site_setting('social_twitter') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://x.com/votrepage">
                            </div>
                            
                            {{-- LinkedIn --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-linkedin text-blue-700 mr-2"></i>LinkedIn
                                </label>
                                <input type="url" 
                                       name="social_linkedin" 
                                       value="{{ site_setting('social_linkedin') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://linkedin.com/company/votrepage">
                            </div>
                            
                            {{-- YouTube --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-youtube text-red-600 mr-2"></i>YouTube
                                </label>
                                <input type="url" 
                                       name="social_youtube" 
                                       value="{{ site_setting('social_youtube') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://youtube.com/votrepage">
                            </div>
                            
                            {{-- TikTok --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-tiktok text-gray-900 mr-2"></i>TikTok
                                </label>
                                <input type="url" 
                                       name="social_tiktok" 
                                       value="{{ site_setting('social_tiktok') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="https://tiktok.com/@votrepage">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Réécriture d'URL --}}
                <div class="pt-8 border-t-2 border-gray-200">
                    @php
                        $urlRewrite = site_setting('url_rewrite', '1');
                    @endphp
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-link mr-3 text-blue-600"></i>
                        Réécriture d'URL
                    </h3>
                    <p class="text-gray-600 mb-6">Contrôlez le format des URLs de votre site</p>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <label class="text-base font-semibold text-gray-900">
                                    <i class="fas fa-globe mr-2 text-blue-500"></i>
                                    Activer la réécriture d'URL (URLs propres)
                                </label>
                                <p class="text-sm text-gray-600 mt-1">Utiliser des URLs basées sur le slug au lieu de l'ID numérique</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="url_rewrite" value="1" {{ $urlRewrite && $urlRewrite !== '0' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <div class="text-sm font-semibold text-blue-700 mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Activé (par défaut)
                                </div>
                                <code class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded block mt-1">/tours/tour-de-la-tour-eiffel</code>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="text-sm font-semibold text-gray-700 mb-1">
                                    <i class="fas fa-times-circle mr-1"></i> Désactivé
                                </div>
                                <code class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded block mt-1">/tours/1</code>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Page d'Accueil --}}
                <div class="pt-8 border-t-2 border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-search mr-3 text-green-600"></i>
                        SEO - Page d'Accueil
                    </h3>
                    <p class="text-gray-600 mb-6">Optimisez le référencement de votre page d'accueil pour les moteurs de recherche</p>
                    
                    {{-- Multilingual SEO Tabs --}}
                    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeSeoTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
                        <div class="p-6 border-b border-gray-200">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-language mr-2"></i>Contenu SEO multilingue
                            </h4>
                            
                            {{-- Tabs Navigation --}}
                            <div class="border-b border-gray-200 -mx-6 px-6">
                                <div class="flex space-x-1">
                                    @foreach($availableLocales as $locale)
                                        @php
                                            $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                                        @endphp
                                        <button 
                                            type="button"
                                            @click="activeSeoTab = '{{ $locale }}'"
                                            :class="activeSeoTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
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
                                <div x-show="activeSeoTab === '{{ $locale }}'" x-transition x-cloak>
                                    <div class="space-y-6">
                                        {{-- Meta Title --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-heading mr-2 text-green-500"></i>Meta Title ({{ strtoupper($locale) }})
                                            </label>
                                            <input type="text" 
                                                   name="seo_home_title_{{ $locale }}" 
                                                   value="{{ site_setting('seo_home_title_' . $locale, site_setting('seo_home_title', config('app.name') . ' - Découvrez nos tours et excursions')) }}"
                                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="Titre de la page d'accueil"
                                                   maxlength="70"
                                                   id="seo_title_input_{{ $locale }}">
                                            <p class="text-xs text-gray-500 mt-1">Recommandé : 50-60 caractères. <span id="title-count-{{ $locale }}">0</span>/70</p>
                                        </div>
                                        
                                        {{-- Meta Description --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-align-left mr-2 text-green-500"></i>Meta Description ({{ strtoupper($locale) }})
                                            </label>
                                            <textarea name="seo_home_description_{{ $locale }}" 
                                                      rows="3"
                                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                      placeholder="Description de la page d'accueil pour les moteurs de recherche"
                                                      maxlength="160"
                                                      id="seo_desc_input_{{ $locale }}">{{ site_setting('seo_home_description_' . $locale, site_setting('seo_home_description', 'Découvrez nos tours et excursions uniques. Réservez votre prochaine aventure avec nous et créez des souvenirs inoubliables.')) }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">Recommandé : 150-160 caractères. <span id="desc-count-{{ $locale }}">0</span>/160</p>
                                        </div>
                                        
                                        {{-- Meta Keywords --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-tags mr-2 text-green-500"></i>Meta Keywords ({{ strtoupper($locale) }})
                                            </label>
                                            <input type="text" 
                                                   name="seo_home_keywords_{{ $locale }}" 
                                                   value="{{ site_setting('seo_home_keywords_' . $locale, site_setting('seo_home_keywords', 'tours, excursions, voyages, aventures, réservation')) }}"
                                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="mot-clé1, mot-clé2, mot-clé3">
                                            <p class="text-xs text-gray-500 mt-1">Séparez les mots-clés par des virgules</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- OG Image (not multilingual) --}}
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image mr-2 text-green-500"></i>Image de Partage (OG Image)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Cette image sera affichée lors du partage de votre site sur les réseaux sociaux</p>
                        
                        @php
                            $ogImage = site_setting('seo_home_og_image');
                        @endphp
                        @if($ogImage)
                            <div class="mb-4">
                                <img src="{{ Storage::url($ogImage) }}" alt="OG Image actuelle" class="h-32 rounded-lg border border-gray-300">
                                <p class="text-sm text-gray-500 mt-2">Image actuelle</p>
                            </div>
                        @endif
                        
                        <label for="seo_home_og_image" class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-upload mr-2"></i>
                            {{ $ogImage ? 'Remplacer l\'image' : 'Choisir une image' }}
                        </label>
                        <input type="file" name="seo_home_og_image" id="seo_home_og_image" accept="image/*" class="hidden">
                        <p class="text-xs text-gray-500 mt-2">Recommandé : 1200x630 pixels. Formats: JPG, PNG, WEBP</p>
                    </div>
                </div>

                {{-- Save Button for Tab --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 font-bold shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les Informations
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- TAB 2: PERSONNALISATION --}}
        {{-- ============================================= --}}
        <div x-show="activeTab === 'personnalisation'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-lg shadow-lg p-8">
                
                {{-- Couleurs --}}
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-palette mr-3 text-purple-600"></i>
                        Couleurs du Site
                    </h3>
                    <p class="text-gray-600 mb-6">Personnalisez les couleurs de votre site</p>
                    
                    <div class="border-2 border-gray-200 rounded-xl p-6 overflow-x-auto">
                        <div class="grid grid-cols-6 gap-4 min-w-[900px]">
                            {{-- Background Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-fill-drip mr-1" style="color: {{ $colors['background_color']->value ?? '#fdfbf7' }}"></i>
                                    Fond
                                </label>
                                <input type="color" 
                                       name="background_color" 
                                       id="background_color"
                                       value="{{ $colors['background_color']->value ?? '#fdfbf7' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('background')">
                                <input type="text" 
                                       id="background_color_text"
                                       value="{{ $colors['background_color']->value ?? '#fdfbf7' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('background')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg border" id="background_preview" style="background-color: {{ $colors['background_color']->value ?? '#fdfbf7' }}">
                                    <p class="text-gray-700 font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>

                            {{-- Primary Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-circle mr-1" style="color: {{ $colors['primary_color']->value ?? '#9333ea' }}"></i>
                                    Principale
                                </label>
                                <input type="color" 
                                       name="primary_color" 
                                       id="primary_color"
                                       value="{{ $colors['primary_color']->value ?? '#9333ea' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('primary')">
                                <input type="text" 
                                       id="primary_color_text"
                                       value="{{ $colors['primary_color']->value ?? '#9333ea' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('primary')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg" id="primary_preview" style="background-color: {{ $colors['primary_color']->value ?? '#9333ea' }}">
                                    <p class="text-white font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>

                            {{-- Secondary Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-circle mr-1" style="color: {{ $colors['secondary_color']->value ?? '#ec4899' }}"></i>
                                    Secondaire
                                </label>
                                <input type="color" 
                                       name="secondary_color" 
                                       id="secondary_color"
                                       value="{{ $colors['secondary_color']->value ?? '#ec4899' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('secondary')">
                                <input type="text" 
                                       id="secondary_color_text"
                                       value="{{ $colors['secondary_color']->value ?? '#ec4899' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('secondary')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg" id="secondary_preview" style="background-color: {{ $colors['secondary_color']->value ?? '#ec4899' }}">
                                    <p class="text-white font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>

                            {{-- Accent Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-circle mr-1" style="color: {{ $colors['accent_color']->value ?? '#f97316' }}"></i>
                                    Accent
                                </label>
                                <input type="color" 
                                       name="accent_color" 
                                       id="accent_color"
                                       value="{{ $colors['accent_color']->value ?? '#f97316' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('accent')">
                                <input type="text" 
                                       id="accent_color_text"
                                       value="{{ $colors['accent_color']->value ?? '#f97316' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('accent')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg" id="accent_preview" style="background-color: {{ $colors['accent_color']->value ?? '#f97316' }}">
                                    <p class="text-white font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>

                            {{-- Success Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-circle mr-1" style="color: {{ $colors['success_color']->value ?? '#22c55e' }}"></i>
                                    Succès
                                </label>
                                <input type="color" 
                                       name="success_color" 
                                       id="success_color"
                                       value="{{ $colors['success_color']->value ?? '#22c55e' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('success')">
                                <input type="text" 
                                       id="success_color_text"
                                       value="{{ $colors['success_color']->value ?? '#22c55e' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('success')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg" id="success_preview" style="background-color: {{ $colors['success_color']->value ?? '#22c55e' }}">
                                    <p class="text-white font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>

                            {{-- Border Color --}}
                            <div class="text-center">
                                <label class="block text-sm font-bold text-gray-900 mb-1">
                                    <i class="fas fa-border-all mr-1" style="color: {{ $colors['border_color']->value ?? '#e5e7eb' }}"></i>
                                    Bordure
                                </label>
                                <input type="color" 
                                       name="border_color" 
                                       id="border_color"
                                       value="{{ $colors['border_color']->value ?? '#e5e7eb' }}"
                                       class="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300 mx-auto block"
                                       onchange="updatePreview('border')">
                                <input type="text" 
                                       id="border_color_text"
                                       value="{{ $colors['border_color']->value ?? '#e5e7eb' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-lg font-mono text-xs mt-2"
                                       oninput="updateFromText('border')"
                                       placeholder="#000000">
                                <div class="mt-2 p-2 rounded-lg border-4" id="border_preview" style="border-color: {{ $colors['border_color']->value ?? '#e5e7eb' }}; background-color: #fff;">
                                    <p class="text-gray-700 font-semibold text-center text-xs">Aperçu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Presets --}}
                <div class="mb-10 pt-6 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-paint-roller mr-2 text-purple-600"></i>
                        Schémas Pré-définis
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <button type="button" onclick="applyPreset('default')" class="p-4 border-2 border-gray-300 rounded-lg hover:border-purple-500 transition">
                            <div class="flex space-x-1 mb-2">
                                <div class="w-6 h-6 rounded" style="background-color: #211951"></div>
                                <div class="w-6 h-6 rounded" style="background-color: #836FFF"></div>
                                <div class="w-6 h-6 rounded" style="background-color: #15F5BA"></div>
                            </div>
                            <p class="text-sm font-semibold">Défaut</p>
                        </button>

                        <button type="button" onclick="applyPreset('purple')" class="p-4 border-2 border-gray-300 rounded-lg hover:border-purple-500 transition">
                            <div class="flex space-x-1 mb-2">
                                <div class="w-6 h-6 rounded bg-purple-600"></div>
                                <div class="w-6 h-6 rounded bg-pink-500"></div>
                                <div class="w-6 h-6 rounded bg-orange-500"></div>
                            </div>
                            <p class="text-sm font-semibold">Violet</p>
                        </button>
                        
                        <button type="button" onclick="applyPreset('blue')" class="p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition">
                            <div class="flex space-x-1 mb-2">
                                <div class="w-6 h-6 rounded bg-blue-600"></div>
                                <div class="w-6 h-6 rounded bg-cyan-500"></div>
                                <div class="w-6 h-6 rounded bg-indigo-500"></div>
                            </div>
                            <p class="text-sm font-semibold">Bleu Océan</p>
                        </button>
                        
                        <button type="button" onclick="applyPreset('green')" class="p-4 border-2 border-gray-300 rounded-lg hover:border-green-500 transition">
                            <div class="flex space-x-1 mb-2">
                                <div class="w-6 h-6 rounded bg-green-600"></div>
                                <div class="w-6 h-6 rounded bg-emerald-500"></div>
                                <div class="w-6 h-6 rounded bg-teal-500"></div>
                            </div>
                            <p class="text-sm font-semibold">Vert Nature</p>
                        </button>
                        
                        <button type="button" onclick="applyPreset('red')" class="p-4 border-2 border-gray-300 rounded-lg hover:border-red-500 transition">
                            <div class="flex space-x-1 mb-2">
                                <div class="w-6 h-6 rounded bg-red-600"></div>
                                <div class="w-6 h-6 rounded bg-rose-500"></div>
                                <div class="w-6 h-6 rounded bg-pink-500"></div>
                            </div>
                            <p class="text-sm font-semibold">Rouge Passion</p>
                        </button>
                    </div>
                </div>

                {{-- Logos --}}
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-image mr-3 text-purple-600"></i>
                        Logos du Site
                    </h3>
                    <p class="text-gray-600 mb-6">Téléchargez vos logos et favicon personnalisés</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        {{-- Logo Principal --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-image mr-2 text-indigo-600"></i>
                                Logo Principal
                            </h4>
                            @if($logo)
                                <div class="mb-4">
                                    @php
                                        $isSvg = strtolower(pathinfo($logo, PATHINFO_EXTENSION)) === 'svg';
                                    @endphp
                                    <img src="{{ Storage::url($logo) }}" alt="Logo actuel" class="h-16 mx-auto" style="max-width: 150px;">
                                    <p class="text-xs text-gray-500 mt-2">Logo actuel</p>
                                </div>
                            @else
                                <div class="mb-4 h-16 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                            
                            <label for="logo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                                <i class="fas fa-upload mr-2"></i>
                                {{ $logo ? 'Remplacer' : 'Choisir' }}
                            </label>
                            <input type="file" name="logo" id="logo" accept="image/*,.svg" class="hidden" onchange="previewLogo(this)">
                            <p class="text-xs text-gray-500 mt-2">Max: 2MB</p>
                        </div>

                        {{-- Logo Petit --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-compress mr-2 text-green-500"></i>
                                Logo Petit
                            </h4>
                            @php
                                $logoSmall = site_setting('logo_small_path');
                            @endphp
                            @if($logoSmall)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($logoSmall) }}" alt="Logo petit actuel" class="h-12 w-12 mx-auto">
                                    <p class="text-xs text-gray-500 mt-2">Logo petit actuel</p>
                                </div>
                            @else
                                <div class="mb-4 h-16 flex items-center justify-center">
                                    <i class="fas fa-compress text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                            
                            <label for="logo_small" class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                                <i class="fas fa-upload mr-2"></i>
                                {{ $logoSmall ? 'Remplacer' : 'Choisir' }}
                            </label>
                            <input type="file" name="logo_small" id="logo_small" accept="image/*,.svg" class="hidden" onchange="previewLogoSmall(this)">
                            <p class="text-xs text-gray-500 mt-2">40x40px recommandé</p>
                        </div>

                        {{-- Favicon --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                Favicon
                            </h4>
                            @if($favicon)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($favicon) }}" alt="Favicon actuel" class="h-12 w-12 mx-auto">
                                    <p class="text-xs text-gray-500 mt-2">Favicon actuel</p>
                                </div>
                            @else
                                <div class="mb-4 h-16 flex items-center justify-center">
                                    <i class="fas fa-star text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                            
                            <label for="favicon" class="cursor-pointer inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                                <i class="fas fa-upload mr-2"></i>
                                {{ $favicon ? 'Remplacer' : 'Choisir' }}
                            </label>
                            <input type="file" name="favicon" id="favicon" accept="image/*,.ico" class="hidden" onchange="previewFavicon(this)">
                            <p class="text-xs text-gray-500 mt-2">32x32px recommandé</p>
                        </div>

                        {{-- Logo Footer --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-shoe-prints mr-2 text-purple-600"></i>
                                Logo Footer
                            </h4>
                            @if($footerLogo)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($footerLogo) }}" alt="Logo footer actuel" class="h-12 mx-auto" style="max-width: 150px;">
                                    <p class="text-xs text-gray-500 mt-2">Logo footer actuel</p>
                                </div>
                            @else
                                <div class="mb-4 h-16 flex items-center justify-center">
                                    <i class="fas fa-shoe-prints text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                            
                            <label for="footer_logo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-upload mr-2"></i>
                                {{ $footerLogo ? 'Remplacer' : 'Choisir' }}
                            </label>
                            <input type="file" name="footer_logo" id="footer_logo" accept="image/*,.svg" class="hidden" onchange="previewFooterLogo(this)">
                            <p class="text-xs text-gray-500 mt-2">Max: 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                    <button type="button" onclick="resetColors()" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                        <i class="fas fa-undo mr-2"></i>
                        Réinitialiser les Couleurs
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 font-bold shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer la Personnalisation
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- TAB 3: MAINTENANCE --}}
        {{-- ============================================= --}}
        <div x-show="activeTab === 'maintenance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-lg shadow-lg p-8">
                
                @php
                    $maintenanceMode = site_setting('maintenance_mode', false);
                    $maintenanceMessage = site_setting('maintenance_message', 'Our website is currently under maintenance for improvements. We will be back very soon. Thank you for your patience!');
                    $maintenanceBypassToken = site_setting('maintenance_bypass_token', '');
                @endphp

                <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-tools mr-3 text-orange-600"></i>
                    Mode Maintenance
                </h3>
                <p class="text-gray-600 mb-6">Activez le mode maintenance pour afficher une page de maintenance aux visiteurs</p>

                <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                    {{-- Toggle Maintenance --}}
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-orange-200">
                        <div>
                            <label class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-power-off mr-2 text-orange-500"></i>
                                Activer le mode maintenance
                            </label>
                            <p class="text-sm text-gray-600 mt-1">Les visiteurs non connectés verront la page de maintenance. Les <strong>administrateurs</strong> et les <strong>clients connectés</strong> (compte client) accèdent normalement au site.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ $maintenanceMode ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                    
                    {{-- Message de maintenance --}}
                    <div class="mb-6">
                        <label for="maintenance_message" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-comment-alt mr-2 text-orange-500"></i>
                            Message de maintenance
                        </label>
                        <textarea name="maintenance_message" 
                                  id="maintenance_message" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                  placeholder="Entrez le message à afficher aux visiteurs...">{{ $maintenanceMessage }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Ce message sera affiché sur la page de maintenance</p>
                    </div>
                    
                    {{-- Numéro WhatsApp --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-whatsapp mr-2 text-green-500"></i>
                            Numéro WhatsApp
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Ce numéro sera affiché sur la page de maintenance pour que les clients puissent vous contacter</p>
                        <input type="text" 
                               name="whatsapp_number" 
                               value="{{ site_setting('whatsapp_number', '+33123456789') }}"
                               class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="+33612345678">
                    </div>
                    
                    {{-- Token de bypass --}}
                    <div class="mb-6">
                        <label for="maintenance_bypass_token" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-key mr-2 text-orange-500"></i>
                            Token de bypass (optionnel)
                        </label>
                        <div class="flex gap-3">
                            <input type="text" 
                                   name="maintenance_bypass_token" 
                                   id="maintenance_bypass_token" 
                                   value="{{ $maintenanceBypassToken }}"
                                   class="flex-1 md:w-1/2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 font-mono"
                                   placeholder="ex: secret123">
                            <button type="button" onclick="generateBypassToken()" class="px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                                <i class="fas fa-random mr-2"></i>
                                Générer
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Partagez un lien du type <code class="bg-gray-100 px-1 rounded">?bypass=VOTRE_TOKEN</code> pour qu’un visiteur voie le site <strong>sans</strong> compte admin (la session est mémorisée). Vous pouvez aussi définir <code class="bg-gray-100 px-1 rounded">MAINTENANCE_BYPASS_TOKEN</code> dans le fichier <code class="bg-gray-100 px-1 rounded">.env</code> (prioritaire sur ce champ).</p>
                        @if(filled($maintenanceBypassToken))
                            <p class="text-xs text-green-800 mt-2 font-medium break-all">Exemple : <a href="{{ url('/?bypass=' . urlencode($maintenanceBypassToken)) }}" class="underline hover:text-green-900" target="_blank" rel="noopener">{{ url('/?bypass=' . urlencode($maintenanceBypassToken)) }}</a></p>
                        @endif
                    </div>
                    
                    {{-- Prévisualisation --}}
                    <div class="pt-6 border-t border-orange-200">
                        <a href="{{ url('/maintenance-preview') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-eye mr-2"></i>
                            Prévisualiser la page de maintenance
                        </a>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 hover:to-red-600 font-bold shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les Paramètres de Maintenance
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- TAB 4: REVIEWS HOME --}}
        {{-- ============================================= --}}
        <div x-show="activeTab === 'reviews'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-lg shadow-lg p-8">
                
                <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-star mr-3 text-yellow-600"></i>
                    Bloc Reviews Home
                </h3>
                <p class="text-gray-600 mb-6">Avis Google via l’API Places uniquement. <strong class="text-gray-800">Important :</strong> Google ne permet <strong>pas</strong> de récupérer la liste complète des avis (environ <strong>5 avis</strong> maximum par appel, sans pagination). La note globale et le nombre total d’avis viennent bien de tous les avis ; pour afficher un mur complet, utilisez l’API <strong>Google Business Profile</strong> (compte propriétaire de la fiche). Une synthèse textuelle (IA) peut s’afficher en complément quand Google la fournit.</p>

                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-xs text-gray-600">Pour Google : activez <strong>Places API (New)</strong> + facturation sur le projet Cloud, puis <code class="bg-white px-1 rounded">GOOGLE_PLACES_API_KEY</code> dans <code class="bg-white px-1 rounded">.env</code>. Après modification du <code class="bg-white px-1 rounded">.env</code>, exécutez <code class="bg-white px-1 rounded">php artisan config:clear</code>.</p>
                    <p class="text-xs text-gray-600 mt-1">Clé restreinte par « sites web » : les appels depuis PHP (serveur) sont souvent refusés — utilisez une restriction par <strong>IP du serveur</strong> ou une clé de test sans restriction HTTP.</p>
                    <p class="text-xs text-gray-600 mt-1">Diagnostic : <code class="bg-white px-1 rounded">php artisan google:test-place-reviews</code> puis consultez <code class="bg-white px-1 rounded">storage/logs/laravel.log</code> si échec.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Place ID Google (fiche établissement)
                    </label>
                    <input type="text"
                           name="reviews_home_place_id"
                           value="{{ site_setting('reviews_home_place_id', '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 font-mono text-sm"
                           placeholder="Ex. ChIJN1t_tDeuEmsRUsoyG83frY4">
                    <p class="text-xs text-gray-500 mt-1">Trouvable via l’URL Google Maps (paramètre <code class="bg-gray-100 px-1 rounded">!1s</code> ou outil Place ID).</p>
                </div>
                
                {{-- Multilingual Title Tabs --}}
                <div class="mb-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden" x-data="{ activeTitleTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-language mr-1"></i> Titre du bloc par langue</h4>
                        <div class="flex space-x-1 border-b border-gray-200 -mx-4 px-4">
                            @foreach($availableLocales as $locale)
                                @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                                <button type="button"
                                    @click="activeTitleTab = '{{ $locale }}'"
                                    :class="activeTitleTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="inline-flex items-center px-3 py-2 border-b-2 text-sm font-medium transition-colors">
                                    <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-1" style="font-size: 1rem;"></span>
                                    <span>{{ strtoupper($locale) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-4">
                        @foreach($availableLocales as $locale)
                            <div x-show="activeTitleTab === '{{ $locale }}'" x-transition x-cloak>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Titre du bloc ({{ strtoupper($locale) }})
                                </label>
                                <input type="text" 
                                       name="reviews_home_title_{{ $locale }}" 
                                       value="{{ site_setting('reviews_home_title_' . $locale, site_setting('reviews_home_title', 'What our travelers say')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="Titre du bloc d'avis">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Google Rating Section --}}
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-gray-50 rounded-lg border border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fab fa-google mr-2 text-blue-600"></i>
                        Note Google
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Rating Value --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>Note (ex: 4.9)
                            </label>
                            <input type="number" 
                                   name="reviews_home_google_rating" 
                                   step="0.1"
                                   min="0"
                                   max="5"
                                   value="{{ site_setting('reviews_home_google_rating', '4.9') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                   placeholder="4.9">
                        </div>

                        {{-- Rating Text --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-text-width mr-2 text-yellow-500"></i>Texte (ex: Top Rated Service)
                            </label>
                            <input type="text" 
                                   name="reviews_home_google_text" 
                                   value="{{ site_setting('reviews_home_google_text', 'Top Rated Service') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                   placeholder="Top Rated Service">
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 mb-2">Aperçu :</p>
                        <div class="flex items-center gap-3">
                            <img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" alt="Google" class="h-6">
                            <div class="flex items-center gap-2">
                                @php
                                    $rating = site_setting('reviews_home_google_rating', '4.9');
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                @endphp
                                @for($i = 0; $i < $fullStars; $i++)
                                    <i class="fas fa-star text-yellow-400"></i>
                                @endfor
                                @if($hasHalfStar)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @endif
                                @for($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                                    <i class="far fa-star text-gray-300"></i>
                                @endfor
                            </div>
                            <span class="font-bold text-gray-900">{{ $rating }}</span>
                            <span class="text-gray-400">|</span>
                            <span class="font-semibold text-gray-900">{{ site_setting('reviews_home_google_text', 'Top Rated Service') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Active Toggle --}}
                <div class="mb-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="reviews_home_active" value="1" {{ site_setting('reviews_home_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yellow-500"></div>
                        <span class="ml-3 text-sm font-semibold text-gray-700">Afficher le bloc Reviews sur la page d'accueil</span>
                    </label>
                </div>

                {{-- Save Button --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 font-bold shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les Paramètres Reviews
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
    const presets = {
        default: {
            background: '#fdfbf7',
            primary: '#211951',
            secondary: '#836FFF',
            accent: '#15F5BA',
            success: '#22c55e',
            border: '#e5e7eb'
        },
        purple: {
            background: '#faf5ff',
            primary: '#9333ea',
            secondary: '#ec4899',
            accent: '#f97316',
            success: '#22c55e',
            border: '#e5e7eb'
        },
        blue: {
            background: '#f0f9ff',
            primary: '#2563eb',
            secondary: '#06b6d4',
            accent: '#6366f1',
            success: '#10b981',
            border: '#dbeafe'
        },
        green: {
            background: '#f0fdf4',
            primary: '#059669',
            secondary: '#14b8a6',
            accent: '#0d9488',
            success: '#22c55e',
            border: '#d1fae5'
        },
        red: {
            background: '#fef2f2',
            primary: '#dc2626',
            secondary: '#f43f5e',
            accent: '#ec4899',
            success: '#22c55e',
            border: '#fee2e2'
        }
    };

    function updatePreview(type) {
        const colorInput = document.getElementById(type + '_color');
        const textInput = document.getElementById(type + '_color_text');
        const preview = document.getElementById(type + '_preview');
        
        textInput.value = colorInput.value;
        if (type === 'border') {
            preview.style.borderColor = colorInput.value;
        } else {
            preview.style.backgroundColor = colorInput.value;
        }
    }

    function updateFromText(type) {
        const colorInput = document.getElementById(type + '_color');
        const textInput = document.getElementById(type + '_color_text');
        const preview = document.getElementById(type + '_preview');
        
        let value = textInput.value.trim();
        
        // Auto-add # if missing
        if (value && !value.startsWith('#')) {
            value = '#' + value;
        }
        
        // Convert shorthand hex (e.g., #FFF to #FFFFFF)
        if (/^#[0-9A-Fa-f]{3}$/.test(value)) {
            value = '#' + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
        }
        
        // Validate hex color
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            textInput.value = value.toUpperCase();
            colorInput.value = value;
            if (type === 'border') {
                preview.style.borderColor = value;
            } else {
                preview.style.backgroundColor = value;
            }
        }
    }

    function applyPreset(presetName) {
        const preset = presets[presetName];
        
        ['background', 'primary', 'secondary', 'accent', 'success', 'border'].forEach(type => {
            const colorInput = document.getElementById(type + '_color');
            const textInput = document.getElementById(type + '_color_text');
            const preview = document.getElementById(type + '_preview');
            
            colorInput.value = preset[type];
            textInput.value = preset[type];
            if (type === 'border') {
                preview.style.borderColor = preset[type];
            } else {
                preview.style.backgroundColor = preset[type];
            }
        });
    }

    function resetColors() {
        applyPreset('default');
    }

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = input.closest('.border-dashed');
                let preview = container.querySelector('.logo-preview-img');
                if (!preview) {
                    const div = container.querySelector('.mb-4');
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu" class="h-16 mx-auto logo-preview-img" style="max-width: 150px;"><p class="text-xs text-gray-500 mt-2">Aperçu</p>';
                } else {
                    preview.src = e.target.result;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewLogoSmall(input) {
        previewLogo(input);
    }

    function previewFavicon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = input.closest('.border-dashed');
                const div = container.querySelector('.mb-4');
                div.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu" class="h-12 w-12 mx-auto"><p class="text-xs text-gray-500 mt-2">Aperçu</p>';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFooterLogo(input) {
        previewLogo(input);
    }

    function generateBypassToken() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';
        for (let i = 0; i < 16; i++) {
            token += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('maintenance_bypass_token').value = token;
    }

    // SEO character counters
    document.addEventListener('DOMContentLoaded', function() {
        // Handle multilingual SEO character counters
        @foreach($availableLocales as $locale)
            const titleInput{{ strtoupper($locale) }} = document.getElementById('seo_title_input_{{ $locale }}');
            const descInput{{ strtoupper($locale) }} = document.getElementById('seo_desc_input_{{ $locale }}');
            const titleCount{{ strtoupper($locale) }} = document.getElementById('title-count-{{ $locale }}');
            const descCount{{ strtoupper($locale) }} = document.getElementById('desc-count-{{ $locale }}');

            if (titleInput{{ strtoupper($locale) }} && titleCount{{ strtoupper($locale) }}) {
                titleCount{{ strtoupper($locale) }}.textContent = titleInput{{ strtoupper($locale) }}.value.length;
                titleInput{{ strtoupper($locale) }}.addEventListener('input', function() {
                    titleCount{{ strtoupper($locale) }}.textContent = this.value.length;
                });
            }

            if (descInput{{ strtoupper($locale) }} && descCount{{ strtoupper($locale) }}) {
                descCount{{ strtoupper($locale) }}.textContent = descInput{{ strtoupper($locale) }}.value.length;
                descInput{{ strtoupper($locale) }}.addEventListener('input', function() {
                    descCount{{ strtoupper($locale) }}.textContent = this.value.length;
                });
            }
        @endforeach

        // Legacy support for non-multilingual fields (if they exist)
        const titleInput = document.getElementById('seo_title_input');
        const descInput = document.getElementById('seo_desc_input');
        const titleCount = document.getElementById('title-count');
        const descCount = document.getElementById('desc-count');

        if (titleInput && titleCount) {
            titleCount.textContent = titleInput.value.length;
            titleInput.addEventListener('input', function() {
                titleCount.textContent = this.value.length;
            });
        }
        
        if (descInput && descCount) {
            descCount.textContent = descInput.value.length;
            descInput.addEventListener('input', function() {
                descCount.textContent = this.value.length;
            });
        }
        
        // OG Image preview
        const ogImageInput = document.getElementById('seo_home_og_image');
        if (ogImageInput) {
            ogImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const container = ogImageInput.closest('div');
                        let preview = container.querySelector('.og-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.className = 'og-preview mt-4';
                            container.appendChild(preview);
                        }
                        preview.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu OG Image" class="h-32 rounded-lg border border-gray-300"><p class="text-sm text-gray-500 mt-2">Aperçu</p>';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endsection
