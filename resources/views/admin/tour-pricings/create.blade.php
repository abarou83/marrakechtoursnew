@extends('admin.layout')

@section('title', 'Create Pricing - ' . translate_model($tour, 'title'))

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.tour-pricings.index', $tour) }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Pricings
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Create New Pricing</h1>
                <p class="text-gray-600 text-sm md:text-base">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                    {{ translate_model($tour, 'title') }}
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-gray-100 rounded-lg p-4">
                    <i class="fas fa-euro-sign text-4xl text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
            <div>
                <h3 class="text-red-800 font-semibold mb-1">Validation Errors</h3>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif

<form action="{{ route('admin.tour-pricings.store', $tour) }}" method="POST" id="pricing-form">
    @csrf

    <!-- Basic Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="bg-gray-100 rounded-lg p-3 mr-4">
                <i class="fas fa-info-circle text-gray-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
                <p class="text-sm text-gray-500">Configure the pricing mode and season</p>
            </div>
        </div>

        @php
            $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
            $localesInfo = \App\Helpers\LanguageHelper::getAvailableLocales();
        @endphp

        <div class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading mr-2 text-gray-500"></i>Titre (par défaut)
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       placeholder="Ex: Visite privée VIP, Visite de groupe standard..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5 px-4">
                <p class="mt-1 text-xs text-gray-500">Donnez un titre à ce tarif pour le distinguer des autres tarifs du même type</p>
            </div>

            {{-- Translation Tabs --}}
            @if(count($availableLocales) > 0)
            <div x-data="{ activeLocaleTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-language mr-2 text-gray-500"></i>Traductions du titre
                </label>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="flex space-x-0 bg-gray-50 border-b border-gray-200">
                        @foreach($availableLocales as $locale)
                            @php $localeInfo = $localesInfo[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                            <button type="button"
                                @click="activeLocaleTab = '{{ $locale }}'"
                                :class="activeLocaleTab === '{{ $locale }}' ? 'border-b-2 border-indigo-500 text-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium transition-colors">
                                <span class="mr-1.5"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1rem;"></span></span>
                                <span>{{ strtoupper($locale) }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="p-4">
                        @foreach($availableLocales as $locale)
                            @php $localeInfo = $localesInfo[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                            <div x-show="activeLocaleTab === '{{ $locale }}'" x-transition>
                                <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                                <input type="text" 
                                       name="translations[{{ $loop->index }}][title]"
                                       value="{{ old("translations.{$loop->index}.title") }}"
                                       placeholder="Titre en {{ strtolower($localeInfo['native']) }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5 px-4">
                            </div>
                        @endforeach
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Saisissez le titre traduit dans chaque langue</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pricing_mode" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Pricing Mode *
                    </label>
                    <select name="pricing_mode" id="pricing_mode" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" required>
                        <option value="">Select pricing mode</option>
                        @php
                            $selectedMode = old('pricing_mode', request()->query('mode'));
                        @endphp
                        <option value="group" {{ $selectedMode === 'group' ? 'selected' : '' }}>Group (per person)</option>
                        <option value="private" {{ $selectedMode === 'private' ? 'selected' : '' }}>Private (per group)</option>
                    </select>
                </div>

                <div>
                    <label for="season" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Season *
                    </label>
                    <select name="season" id="season" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" required>
                        <option value="all" {{ old('season') === 'all' ? 'selected' : '' }}>All Seasons</option>
                        <option value="low" {{ old('season') === 'low' ? 'selected' : '' }}>Low Season</option>
                        <option value="normal" {{ old('season') === 'normal' ? 'selected' : '' }}>Normal Season</option>
                        <option value="high" {{ old('season') === 'high' ? 'selected' : '' }}>High Season</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="flex items-center cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-800"></div>
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
                    <i class="fas fa-toggle-on mr-2"></i>Active Pricing
                </span>
            </label>
        </div>
    </div>

    <!-- Group Pricing Section -->
    <div id="group-pricing-section" class="mb-6 hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-users text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Group Prices</h2>
                        <p class="text-sm text-gray-500">Configure per-person pricing categories</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Per Person</span>
            </div>

            <div id="group-prices-container" class="space-y-4">
                <div class="group-price-item bg-gray-50 border border-gray-200 rounded-lg p-5 relative">
                    <button type="button" class="remove-group-price-item absolute top-3 right-3 text-red-600 hover:text-red-800 transition-colors p-2 rounded-lg hover:bg-red-50 z-10 hidden" title="Supprimer la catégorie">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-tag mr-1 text-gray-500"></i>Category *
                            </label>
                            <select name="group_prices[0][category]" class="group-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true">
                                <option value="adult">Adult</option>
                                <option value="child">Child</option>
                                <option value="infant">Infant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-birthday-cake mr-1 text-gray-500"></i>Age Min
                            </label>
                            <input type="number" name="group_prices[0][age_min]" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="0" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-birthday-cake mr-1 text-gray-500"></i>Age Max
                            </label>
                            <input type="number" name="group_prices[0][age_max]" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="0" placeholder="+">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-euro-sign mr-1 text-gray-500"></i>Price (€) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                <input type="number" name="group_prices[0][price]" step="0.01" min="0" class="group-price-field w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-group-price" class="mt-4 w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Another Category
            </button>
        </div>
    </div>

    <!-- Private Pricing Section -->
    <div id="private-pricing-section" class="mb-6 hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-user-lock text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Private Prices</h2>
                        <p class="text-sm text-gray-500">Configure per-group pricing tiers</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Per Group</span>
            </div>

            <div id="private-prices-container" class="space-y-4">
                <div class="private-price-item bg-gray-50 border border-gray-200 rounded-lg p-5 relative">
                    <button type="button" class="remove-private-price-item absolute top-3 right-3 text-red-600 hover:text-red-800 transition-colors p-2 rounded-lg hover:bg-red-50 z-10 hidden" title="Supprimer le tier">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-users mr-1 text-gray-500"></i>Min People *
                            </label>
                            <input type="number" name="private_prices[0][min_people]" class="private-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="1" data-required="true" placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-users mr-1 text-gray-500"></i>Max People *
                            </label>
                            <input type="number" name="private_prices[0][max_people]" class="private-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="1" data-required="true" placeholder="10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-euro-sign mr-1 text-gray-500"></i>Price (€) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                <input type="number" name="private_prices[0][price]" step="0.01" min="0" class="private-price-field w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-private-price" class="mt-4 w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Another Tier
            </button>
        </div>
    </div>

    <!-- Accommodations Section -->
    <div id="accommodations-section" class="mb-6 hidden" x-data="accommodationsManager()">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-hotel text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Hébergements</h2>
                        <p class="text-sm text-gray-500">Attacher des hébergements à cette formule</p>
                    </div>
                </div>
                <button type="button" @click="openModal()" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>Ajouter Hébergement
                </button>
            </div>
            
            <!-- Attached Accommodations List -->
            <div id="attached-accommodations-container" class="space-y-3">
                <template x-if="attachedAccommodations.length === 0">
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="fas fa-hotel text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">Aucun hébergement attaché. Cliquez sur "Ajouter Hébergement" pour en ajouter.</p>
                    </div>
                </template>
                
                <template x-for="(accommodation, index) in attachedAccommodations" :key="accommodation.id">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <input type="hidden" :name="'accommodations[' + index + '][accommodation_id]'" :value="accommodation.id">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-200 rounded-lg p-2 mr-3">
                                <i class="fas fa-hotel text-gray-600"></i>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900" x-text="accommodation.name"></span>
                                <template x-if="accommodation.stars">
                                    <span class="ml-2 text-yellow-500" x-html="'★'.repeat(accommodation.stars)"></span>
                                </template>
                                <template x-if="accommodation.location">
                                    <span class="ml-2 text-sm text-gray-500" x-text="accommodation.location"></span>
                                </template>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 ml-4">
                            <label class="flex items-center text-sm text-gray-600">
                                <input type="checkbox" 
                                       :name="'accommodations[' + index + '][is_optional]'" 
                                       value="1"
                                       :checked="accommodation.is_optional"
                                       @change="accommodation.is_optional = $event.target.checked"
                                       class="h-4 w-4 text-gray-800 border-gray-300 rounded focus:ring-gray-500 mr-2">
                                Optionnel
                            </label>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Nuits:</span>
                                <input type="number" 
                                       :name="'accommodations[' + index + '][nights]'" 
                                       :value="accommodation.nights"
                                       @input="accommodation.nights = $event.target.value"
                                       min="1"
                                       class="w-16 text-sm border-gray-300 rounded-lg focus:border-gray-500 focus:ring-gray-500"
                                       placeholder="1">
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Ordre:</span>
                                <input type="number" 
                                       :name="'accommodations[' + index + '][display_order]'" 
                                       :value="accommodation.display_order"
                                       @input="accommodation.display_order = $event.target.value"
                                       min="0"
                                       class="w-16 text-sm border-gray-300 rounded-lg focus:border-gray-500 focus:ring-gray-500"
                                       placeholder="0">
                            </div>
                            <button type="button" @click="removeAccommodation(accommodation.id)" class="text-red-500 hover:text-red-700 p-2" title="Retirer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Modal for selecting accommodations -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Sélectionner un hébergement</h3>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <template x-for="accommodation in availableAccommodations" :key="accommodation.id">
                                <div @click="attachAccommodation(accommodation)" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors">
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 rounded-lg p-2 mr-3">
                                            <i class="fas fa-hotel text-gray-600"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900" x-text="accommodation.name"></span>
                                            <template x-if="accommodation.stars">
                                                <span class="ml-2 text-yellow-500" x-html="'★'.repeat(accommodation.stars)"></span>
                                            </template>
                                            <div class="text-sm text-gray-500" x-text="accommodation.location"></div>
                                        </div>
                                    </div>
                                    <i class="fas fa-plus text-gray-400"></i>
                                </div>
                            </template>
                            
                            <template x-if="availableAccommodations.length === 0">
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                    <p>Tous les hébergements sont déjà attachés !</p>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add-ons Section -->
    <div id="addons-section" class="mb-6 hidden" x-data="addonsManager()">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-puzzle-piece text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Add-ons</h2>
                        <p class="text-sm text-gray-500">Attach add-ons to this pricing</p>
                    </div>
                </div>
                <button type="button" @click="openModal()" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>Attach Add-on
                </button>
            </div>
            
            <!-- Attached Addons List -->
            <div id="attached-addons-container" class="space-y-3">
                <template x-if="attachedAddons.length === 0">
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="fas fa-puzzle-piece text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">No add-ons attached yet. Click "Attach Add-on" to add some.</p>
                    </div>
                </template>
                
                <template x-for="(addon, index) in attachedAddons" :key="addon.id">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <input type="hidden" :name="getInputName(index, 'addon_id')" :value="addon.id">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-200 rounded-lg p-2 mr-3">
                                <i class="fas fa-puzzle-piece text-gray-600"></i>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900" x-text="addon.name"></span>
                                <span class="ml-2 text-sm text-gray-500" x-text="'(' + addon.pricing_type_label + ')'"></span>
                                <span class="ml-2 text-sm font-semibold text-gray-700" x-text="'€' + addon.base_price"></span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 ml-4">
                            <label class="flex items-center text-sm text-gray-600" title="Included in base price">
                                <input type="checkbox" 
                                       :name="getInputName(index, 'is_included')" 
                                       value="1"
                                       :checked="addon.is_included"
                                       @change="addon.is_included = $event.target.checked"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-2">
                                <i class="fas fa-gift text-green-600 mr-1"></i>Included
                            </label>
                            <label class="flex items-center text-sm text-gray-600">
                                <input type="checkbox" 
                                       :name="getInputName(index, 'is_required')" 
                                       value="1"
                                       :checked="addon.is_required"
                                       @change="addon.is_required = $event.target.checked"
                                       class="h-4 w-4 text-gray-800 border-gray-300 rounded focus:ring-gray-500 mr-2">
                                Required
                            </label>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Override:</span>
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">€</span>
                                    <input type="number" 
                                           :name="getInputName(index, 'override_price')" 
                                           :value="addon.override_price"
                                           @input="addon.override_price = $event.target.value"
                                           step="0.01" 
                                           min="0" 
                                           class="w-24 pl-6 text-sm border-gray-300 rounded-lg focus:border-gray-500 focus:ring-gray-500"
                                           placeholder="Base">
                                </div>
                            </div>
                            <button type="button" @click="removeAddon(addon.id)" class="text-red-500 hover:text-red-700 p-2" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Modal for selecting addons -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Select Add-on to Attach</h3>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <template x-for="addon in availableAddons" :key="addon.id">
                                <div @click="attachAddon(addon)" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors">
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 rounded-lg p-2 mr-3">
                                            <i class="fas fa-puzzle-piece text-gray-600"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900" x-text="addon.name"></span>
                                            <div class="text-sm text-gray-500">
                                                <span x-text="addon.pricing_type_label"></span>
                                                <span class="mx-1">•</span>
                                                <span class="font-semibold" x-text="'€' + addon.base_price"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="fas fa-plus text-gray-400"></i>
                                </div>
                            </template>
                            
                            <template x-if="availableAddons.length === 0">
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                    <p>All add-ons are already attached!</p>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('admin.tour-pricings.index', $tour) }}" class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-semibold">
                <i class="fas fa-save mr-2"></i>Create Pricing
            </button>
        </div>
    </div>
</form>

@php
    $allAddonsJson = $allAddons->map(function($addon) {
        return [
            'id' => $addon->id,
            'name' => $addon->name,
            'pricing_type' => $addon->pricing_type,
            'pricing_type_label' => ucfirst(str_replace('_', ' ', $addon->pricing_type)),
            'base_price' => number_format($addon->base_price, 2),
        ];
    });
    
    $allAccommodationsJson = $allAccommodations->map(function($accommodation) {
        return [
            'id' => $accommodation->id,
            'name' => $accommodation->name,
            'location' => $accommodation->location,
            'stars' => $accommodation->stars,
        ];
    });
@endphp

<script>
/**
 * Alpine.js component for managing accommodations with modal
 */
function accommodationsManager() {
    return {
        showModal: false,
        // All available accommodations from database
        allAccommodations: @json($allAccommodationsJson),
        // Currently attached accommodations (empty for create)
        attachedAccommodations: [],
        
        get availableAccommodations() {
            const attachedIds = this.attachedAccommodations.map(a => a.id);
            return this.allAccommodations.filter(a => !attachedIds.includes(a.id));
        },
        
        openModal() {
            this.showModal = true;
        },
        
        closeModal() {
            this.showModal = false;
        },
        
        attachAccommodation(accommodation) {
            this.attachedAccommodations.push({
                ...accommodation,
                is_optional: true,
                nights: 1,
                display_order: this.attachedAccommodations.length
            });
            this.closeModal();
        },
        
        removeAccommodation(accommodationId) {
            this.attachedAccommodations = this.attachedAccommodations.filter(a => a.id !== accommodationId);
        }
    };
}

/**
 * Alpine.js component for managing addons with modal
 */
function addonsManager() {
    return {
        showModal: false,
        // All available addons from database
        allAddons: @json($allAddonsJson),
        // Currently attached addons (empty for create)
        attachedAddons: [],
        
        get availableAddons() {
            const attachedIds = this.attachedAddons.map(a => a.id);
            return this.allAddons.filter(a => !attachedIds.includes(a.id));
        },
        
        getInputName(index, field) {
            const mode = document.getElementById('pricing_mode').value;
            return mode + '_addons[' + index + '][' + field + ']';
        },
        
        openModal() {
            this.showModal = true;
        },
        
        closeModal() {
            this.showModal = false;
        },
        
        attachAddon(addon) {
            this.attachedAddons.push({
                ...addon,
                is_included: false,
                is_required: false,
                override_price: ''
            });
            this.closeModal();
        },
        
        removeAddon(addonId) {
            this.attachedAddons = this.attachedAddons.filter(a => a.id !== addonId);
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    const pricingMode = document.getElementById('pricing_mode');
    const groupSection = document.getElementById('group-pricing-section');
    const privateSection = document.getElementById('private-pricing-section');
    const addonsSection = document.getElementById('addons-section');
    const accommodationsSection = document.getElementById('accommodations-section');
    let groupPriceIndex = 1;
    let privatePriceIndex = 1;

    function toggleSections() {
        if (!pricingMode) return;
        const mode = pricingMode.value;
        
        if (mode === 'group') {
            if (groupSection) groupSection.classList.remove('hidden');
            if (privateSection) privateSection.classList.add('hidden');
            if (addonsSection) addonsSection.classList.remove('hidden');
            if (accommodationsSection) accommodationsSection.classList.remove('hidden');
            document.querySelectorAll('.group-price-field[data-required="true"]').forEach(f => {
                f.setAttribute('required', 'required');
                f.removeAttribute('readonly');
                f.classList.remove('opacity-50');
            });
            document.querySelectorAll('.private-price-field[data-required="true"]').forEach(f => {
                f.removeAttribute('required');
                f.setAttribute('readonly', 'readonly');
                f.classList.add('opacity-50');
            });
        } else if (mode === 'private') {
            if (groupSection) groupSection.classList.add('hidden');
            if (privateSection) privateSection.classList.remove('hidden');
            if (addonsSection) addonsSection.classList.remove('hidden');
            if (accommodationsSection) accommodationsSection.classList.remove('hidden');
            document.querySelectorAll('.private-price-field[data-required="true"]').forEach(f => {
                f.setAttribute('required', 'required');
                f.removeAttribute('readonly');
                f.classList.remove('opacity-50');
            });
            document.querySelectorAll('.group-price-field[data-required="true"]').forEach(f => {
                f.removeAttribute('required');
                f.setAttribute('readonly', 'readonly');
                f.classList.add('opacity-50');
            });
        } else {
            if (groupSection) groupSection.classList.add('hidden');
            if (privateSection) privateSection.classList.add('hidden');
            if (addonsSection) addonsSection.classList.add('hidden');
            if (accommodationsSection) accommodationsSection.classList.add('hidden');
        }
    }

    if (pricingMode) {
        pricingMode.addEventListener('change', toggleSections);
        toggleSections();
    }

    // Fonction pour mettre à jour la visibilité des boutons de suppression
    function updateDeleteButtonsVisibility() {
        const container = document.getElementById('group-prices-container');
        if (!container) return;
        const items = container.querySelectorAll('.group-price-item');
        items.forEach((item) => {
            const deleteBtn = item.querySelector('.remove-group-price-item');
            if (deleteBtn) {
                if (items.length > 1) {
                    deleteBtn.classList.remove('hidden');
                } else {
                    deleteBtn.classList.add('hidden');
                }
            }
        });
    }

    // Ajouter une nouvelle catégorie
    var addGroupPriceBtn = document.getElementById('add-group-price');
    if (addGroupPriceBtn) {
        addGroupPriceBtn.addEventListener('click', function() {
            const container = document.getElementById('group-prices-container');
            if (container && container.firstElementChild) {
                const newItem = container.firstElementChild.cloneNode(true);
                newItem.querySelectorAll('input, select').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[\d+\]/, '[' + groupPriceIndex + ']');
                        input.value = input.tagName === 'SELECT' ? input.options[0].value : '';
                    }
                });
                // Afficher le bouton de suppression sur le nouvel item
                const deleteBtn = newItem.querySelector('.remove-group-price-item');
                if (deleteBtn) {
                    deleteBtn.classList.remove('hidden');
                }
                container.appendChild(newItem);
                groupPriceIndex++;
                updateDeleteButtonsVisibility();
            }
        });
    }

    // Supprimer une catégorie
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-group-price-item')) {
            const item = e.target.closest('.group-price-item');
            const container = document.getElementById('group-prices-container');
            if (!container) return;
            const items = container.querySelectorAll('.group-price-item');
            
            // Ne pas supprimer s'il n'y a qu'un seul item
            if (items.length > 1) {
                item.remove();
                updateDeleteButtonsVisibility();
                // Réindexer les champs
                container.querySelectorAll('.group-price-item').forEach((item, index) => {
                    item.querySelectorAll('input, select').forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/group_prices\[\d+\]/, 'group_prices[' + index + ']');
                        }
                    });
                });
            }
        }
    });

    // Initialiser la visibilité des boutons au chargement
    updateDeleteButtonsVisibility();

    function updatePrivateDeleteButtonsVisibility() {
        const container = document.getElementById('private-prices-container');
        if (!container) return;
        const items = container.querySelectorAll('.private-price-item');
        items.forEach((item) => {
            const deleteBtn = item.querySelector('.remove-private-price-item');
            if (deleteBtn) {
                if (items.length > 1) {
                    deleteBtn.classList.remove('hidden');
                } else {
                    deleteBtn.classList.add('hidden');
                }
            }
        });
    }

    var addPrivatePriceBtn = document.getElementById('add-private-price');
    if (addPrivatePriceBtn) {
        addPrivatePriceBtn.addEventListener('click', function() {
            const container = document.getElementById('private-prices-container');
            if (container && container.firstElementChild) {
                const newItem = container.firstElementChild.cloneNode(true);
                newItem.querySelectorAll('input').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[\d+\]/, '[' + privatePriceIndex + ']');
                        input.value = '';
                    }
                });
                const deleteBtn = newItem.querySelector('.remove-private-price-item');
                if (deleteBtn) {
                    deleteBtn.classList.remove('hidden');
                }
                container.appendChild(newItem);
                privatePriceIndex++;
                updatePrivateDeleteButtonsVisibility();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-private-price-item')) {
            const item = e.target.closest('.private-price-item');
            const container = document.getElementById('private-prices-container');
            if (!container) return;
            const items = container.querySelectorAll('.private-price-item');

            if (items.length > 1) {
                item.remove();
                updatePrivateDeleteButtonsVisibility();
                container.querySelectorAll('.private-price-item').forEach((item, index) => {
                    item.querySelectorAll('input').forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/private_prices\[\d+\]/, 'private_prices[' + index + ']');
                        }
                    });
                });
            }
        }
    });

    updatePrivateDeleteButtonsVisibility();
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
