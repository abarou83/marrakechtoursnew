@extends('admin.layout')

@section('title', 'Blocs de fonctionnalités')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Blocs de fonctionnalités</h2>
        <p class="text-sm text-gray-500">Gérez les 4 blocs de la section "Why book with us" sur la page d'accueil.</p>
    </div>
    <a href="{{ route('admin.feature-blocks.create') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
        <i class="fas fa-plus mr-2"></i>Nouveau bloc
    </a>
</div>

@php
    use App\Helpers\LanguageHelper;
    $locales = LanguageHelper::getAvailableLocales();
@endphp

{{-- Section Settings (Title, Description) - Multilingue avec Tabs --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6" x-data="{ activeSectionTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-cog mr-2 text-indigo-600"></i>Paramètres de la section
        </h3>
        
        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 -mx-6 px-6">
            <div class="flex space-x-1">
                @foreach($availableLocales as $locale)
                    @php
                        $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                    @endphp
                    <button 
                        @click="activeSectionTab = '{{ $locale }}'"
                        :class="activeSectionTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="inline-flex items-center px-4 py-2 border-b-2 font-semibold text-sm transition-colors duration-200">
                        <span class="text-xl mr-2"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                        <span>{{ $localeInfo['native'] }}</span>
                        <span class="ml-2 text-xs opacity-75">({{ strtoupper($locale) }})</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.feature-blocks.section-settings') }}" class="p-6">
        @csrf
        
        {{-- Tab Content --}}
        @foreach($availableLocales as $locale)
            @php
                $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                $translationIndex = $loop->index;
                $sectionTranslation = $sectionTranslations[$locale] ?? null;
            @endphp
            
            <div x-show="activeSectionTab === '{{ $locale }}'" x-transition class="space-y-4">
                <div class="flex items-center mb-4">
                    <span class="text-2xl mr-2"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                    <h4 class="font-bold text-gray-900">Paramètres pour {{ $localeInfo['native'] }}</h4>
                    <span class="ml-auto text-xs text-gray-500">{{ strtoupper($locale) }}</span>
                </div>
                
                <input type="hidden" name="translations[{{ $translationIndex }}][locale]" value="{{ $locale }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Titre</label>
                        <input type="text" 
                               name="translations[{{ $translationIndex }}][title]" 
                               value="{{ old("translations.{$translationIndex}.title", $sectionTranslation?->title ?? '') }}"
                               class="w-full border-gray-300 rounded-lg" 
                               placeholder="Why book with Viator?">
                        <p class="text-xs text-gray-500 mt-1">Titre principal de la section</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="translations[{{ $translationIndex }}][description]" 
                                  rows="3" 
                                  class="w-full border-gray-300 rounded-lg" 
                                  placeholder="Description optionnelle affichée sous le titre">{{ old("translations.{$translationIndex}.description", $sectionTranslation?->description ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Description optionnelle pour la section</p>
                    </div>
                </div>
            </div>
        @endforeach
        
        {{-- Container Background Color (Global, not translated) --}}
        <div class="border-t pt-6 mt-6">
            <h4 class="text-sm font-bold text-gray-900 mb-4">🎨 Couleur de fond du conteneur</h4>
            <div class="flex items-center gap-3">
                <input type="color" 
                       name="container_background_color" 
                       value="{{ old('container_background_color', $sectionSettings->container_background_color ?? '#F9FAFB') }}" 
                       class="w-16 h-10 border-gray-300 rounded-lg cursor-pointer">
                <input type="text" 
                       id="container_background_color_text" 
                       value="{{ old('container_background_color', $sectionSettings->container_background_color ?? '#F9FAFB') }}" 
                       pattern="^#[0-9A-Fa-f]{6}$"
                       placeholder="#F9FAFB"
                       class="flex-1 border-gray-300 rounded-lg px-3 py-2 font-mono text-sm"
                       onchange="document.querySelector('input[name=container_background_color]').value = this.value">
            </div>
            <p class="text-xs text-gray-500 mt-2">Couleur de fond du div qui contient les 4 blocs (format hex: #RRGGBB)</p>
        </div>
        
        <div class="flex justify-end pt-6 border-t mt-6">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                <i class="fas fa-save mr-2"></i>Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Synchroniser les champs couleur et texte
    document.addEventListener('DOMContentLoaded', function() {
        const containerColorInput = document.querySelector('input[name="container_background_color"]');
        const containerColorText = document.querySelector('#container_background_color_text');
        if (containerColorInput && containerColorText) {
            containerColorInput.addEventListener('input', function() {
                containerColorText.value = this.value;
            });
            containerColorText.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    containerColorInput.value = this.value;
                }
            });
        }
    });
</script>
@endpush

{{-- Feature Blocks List with Tabs by Language --}}
<div class="bg-gray-50 rounded-lg shadow overflow-visible" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200">
        <div class="flex space-x-1 px-6 pt-4">
            @foreach($availableLocales as $locale)
                @php
                    $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                @endphp
                <button 
                    @click="activeTab = '{{ $locale }}'"
                    :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-3 border-b-2 font-semibold text-sm transition-colors duration-200">
                    <span class="text-xl mr-2"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                    <span>{{ $localeInfo['native'] }}</span>
                    <span class="ml-2 text-xs opacity-75">({{ strtoupper($locale) }})</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Tab Content --}}
    @foreach($availableLocales as $locale)
        @php
            $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
        @endphp
        <div x-show="activeTab === '{{ $locale }}'" x-transition class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                    <h3 class="text-lg font-semibold text-gray-900">Blocs de fonctionnalités - {{ $localeInfo['native'] }}</h3>
                </div>
            </div>

            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ordre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Icône</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($featureBlocks as $block)
                        @php
                            $translation = $block->translations->where('locale', $locale)->first();
                            $displayTitle = $translation?->title ?? ($block->translations->first()?->title ?? '-');
                            $displayDescription = $translation?->description ?? ($block->translations->first()?->description ?? '-');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $block->order }}</td>
                            <td class="px-6 py-4">
                                <div class="icon-container inline-flex items-center justify-center w-12 h-12 rounded-lg" style="background-color: {{ primary_color() }}20;">
                                    @if($block->image_path)
                                        <img src="{{ Storage::url($block->image_path) }}" alt="Icon" class="h-8 w-8 object-contain">
                                    @else
                                        <i class="{{ $block->icon }} text-xl" style="color: {{ primary_color() }};"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $displayTitle }}
                                @if(!$translation)
                                    <span class="ml-2 text-xs text-yellow-600" title="Traduction manquante">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($displayDescription, 60) }}</td>
                            <td class="px-6 py-4">
                                @if($block->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-admin.action-menu>
                                    <a href="{{ route('admin.feature-blocks.edit', $block) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                        <i class="fas fa-edit text-indigo-500"></i>
                                        Modifier
                                    </a>
                                    <form method="POST" action="{{ route('admin.feature-blocks.toggle-active', $block) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                            <i class="fas fa-{{ $block->is_active ? 'eye-slash' : 'eye' }} text-yellow-500"></i>
                                            {{ $block->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.feature-blocks.destroy', $block) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bloc ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 hover:bg-gray-50 text-red-600">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </x-admin.action-menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Aucun bloc de fonctionnalité pour le moment.</p>
                                <a href="{{ route('admin.feature-blocks.create') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                                    Créer le premier bloc
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@endsection

