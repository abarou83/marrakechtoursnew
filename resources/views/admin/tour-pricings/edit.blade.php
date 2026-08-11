@extends('admin.layout')

@section('title', 'Edit Pricing - ' . translate_model($tour, 'title'))

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
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Edit Pricing</h1>
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

@if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif

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

<form action="{{ route('admin.tour-pricings.update', [$tour, $pricing]) }}" method="POST" id="pricing-form">
    @csrf
    @method('PUT')

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
                       value="{{ old('title', $pricing->title) }}"
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
                            @php
                                $localeInfo = $localesInfo[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                                $existingTranslation = $pricing->translations->where('locale', $locale)->first();
                            @endphp
                            <div x-show="activeLocaleTab === '{{ $locale }}'" x-transition>
                                <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                                <input type="text" 
                                       name="translations[{{ $loop->index }}][title]"
                                       value="{{ old("translations.{$loop->index}.title", $existingTranslation->title ?? $pricing->title) }}"
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
                        <option value="group" {{ old('pricing_mode', $pricing->pricing_mode) === 'group' ? 'selected' : '' }}>Group (per person)</option>
                        <option value="private" {{ old('pricing_mode', $pricing->pricing_mode) === 'private' ? 'selected' : '' }}>Private (per group)</option>
                    </select>
                </div>

                <div>
                    <label for="season" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Season *
                    </label>
                    <select name="season" id="season" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" required>
                        <option value="all" {{ old('season', $pricing->season) === 'all' ? 'selected' : '' }}>All Seasons</option>
                        <option value="low" {{ old('season', $pricing->season) === 'low' ? 'selected' : '' }}>Low Season</option>
                        <option value="normal" {{ old('season', $pricing->season) === 'normal' ? 'selected' : '' }}>Normal Season</option>
                        <option value="high" {{ old('season', $pricing->season) === 'high' ? 'selected' : '' }}>High Season</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="flex items-center cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pricing->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-800"></div>
                </div>
                <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
                    <i class="fas fa-toggle-on mr-2"></i>Active Pricing
                </span>
            </label>
        </div>
    </div>

    <!-- Group Pricing Section -->
    <div id="group-pricing-section" class="mb-6 {{ $pricing->pricing_mode !== 'group' ? 'hidden' : '' }} transition-all duration-300">
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
                @if($pricing->pricing_mode === 'group' && $pricing->groupPrices->count() > 0)
                    @foreach($pricing->groupPrices as $index => $groupPrice)
                        <div class="group-price-item bg-gray-50 border border-gray-200 rounded-lg p-5 relative">
                            <button type="button" class="remove-group-price-item absolute top-3 right-3 text-red-600 hover:text-red-800 transition-colors p-2 rounded-lg hover:bg-red-50 z-10 {{ $pricing->groupPrices->count() <= 1 ? 'hidden' : '' }}" title="Supprimer la catégorie">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-tag mr-1 text-gray-500"></i>Category *
                                </label>
                                <select name="group_prices[{{ $index }}][category]" class="group-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true">
                                    <option value="adult" {{ $groupPrice->category === 'adult' ? 'selected' : '' }}>Adult</option>
                                    <option value="child" {{ $groupPrice->category === 'child' ? 'selected' : '' }}>Child</option>
                                    <option value="infant" {{ $groupPrice->category === 'infant' ? 'selected' : '' }}>Infant</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-birthday-cake mr-1 text-gray-500"></i>Age Min
                                </label>
                                <input type="number" name="group_prices[{{ $index }}][age_min]" value="{{ $groupPrice->age_min }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="0" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-birthday-cake mr-1 text-gray-500"></i>Age Max
                                </label>
                                <input type="number" name="group_prices[{{ $index }}][age_max]" value="{{ $groupPrice->age_max }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="0" placeholder="+">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-euro-sign mr-1 text-gray-500"></i>Price (€) *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                    <input type="number" name="group_prices[{{ $index }}][price]" value="{{ $groupPrice->price }}" step="0.01" min="0" class="group-price-field w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="group-price-item border border-gray-200 rounded-lg p-4 relative">
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
            @endif
        </div>
            <button type="button" id="add-group-price" class="mt-4 w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Another Category
            </button>
        </div>
    </div>

    <!-- Private Pricing Section -->
    <div id="private-pricing-section" class="mb-6 {{ $pricing->pricing_mode !== 'private' ? 'hidden' : '' }} transition-all duration-300">
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
                @if($pricing->pricing_mode === 'private' && $pricing->privatePrices->count() > 0)
                    @foreach($pricing->privatePrices as $index => $privatePrice)
                        <div class="private-price-item bg-gray-50 border border-gray-200 rounded-lg p-5 relative">
                        <button type="button" class="remove-private-price-item absolute top-3 right-3 text-red-600 hover:text-red-800 transition-colors p-2 rounded-lg hover:bg-red-50 z-10 {{ $pricing->privatePrices->count() <= 1 ? 'hidden' : '' }}" title="Supprimer le tier">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-users mr-1 text-gray-500"></i>Min People *
                                </label>
                                <input type="number" name="private_prices[{{ $index }}][min_people]" value="{{ $privatePrice->min_people }}" class="private-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="1" data-required="true" placeholder="1">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-users mr-1 text-gray-500"></i>Max People *
                                </label>
                                <input type="number" name="private_prices[{{ $index }}][max_people]" value="{{ $privatePrice->max_people }}" class="private-price-field w-full border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" min="1" data-required="true" placeholder="10">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-euro-sign mr-1 text-gray-500"></i>Price (€) *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                    <input type="number" name="private_prices[{{ $index }}][price]" value="{{ $privatePrice->price }}" step="0.01" min="0" class="private-price-field w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-500 transition-all py-2.5" data-required="true" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="private-price-item border border-gray-200 rounded-lg p-4 relative">
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
            @endif
        </div>
            <button type="button" id="add-private-price" class="mt-4 w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Another Tier
            </button>
        </div>
    </div>

    <!-- Add-ons Section -->
    <!-- Accommodations Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-hotel text-gray-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Hébergements</h2>
                    <p class="text-sm text-gray-500">Gérez les hébergements disponibles pour cette formule</p>
                </div>
            </div>
            <a href="{{ route('admin.tour-pricings.accommodations', $pricing) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold">
                <i class="fas fa-hotel mr-2"></i>Gérer les Hébergements
            </a>
        </div>
        
        @if($pricing->accommodations && $pricing->accommodations->count() > 0)
            <div class="space-y-3 mt-4">
                @foreach($pricing->accommodations as $accommodation)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $accommodation->name }}</h4>
                                    @if($accommodation->stars)
                                        <div class="flex items-center">
                                            @for($i = 0; $i < $accommodation->stars; $i++)
                                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($accommodation->pivot->is_optional)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Optionnel
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Inclus
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $accommodation->pivot->nights ?? 1 }} nuit(s)
                                    </span>
                                </div>
                                @if($accommodation->location)
                                    <p class="text-sm text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $accommodation->location }}
                                    </p>
                                @endif
                                
                                <!-- Types de chambres disponibles -->
                                @if($accommodation->activeRooms && $accommodation->activeRooms->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Chambres disponibles:</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($accommodation->activeRooms as $room)
                                                <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                                                    <span class="text-sm text-gray-700">{{ $room->room_type_name }}</span>
                                                    <span class="text-sm font-semibold text-blue-600">{{ number_format($room->price_per_night, 2) }}€/nuit</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button type="button" 
                                        onclick="detachAccommodation({{ $pricing->id }}, {{ $accommodation->id }}, '{{ $accommodation->name }}')"
                                        class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors" 
                                        title="Détacher l'hébergement">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4 text-center">
                <i class="fas fa-info-circle mr-2"></i>Aucun hébergement associé à cette formule. 
                <a href="{{ route('admin.tour-pricings.accommodations', $pricing) }}" class="text-blue-600 hover:underline ml-1">Cliquez ici pour en ajouter</a>
            </div>
        @endif
    </div>

    <!-- Addons Section -->
    <div id="addons-section" class="mb-6" x-data="addonsManager()">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-puzzle-piece text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Add-ons</h2>
                        <p class="text-sm text-gray-500">Manage add-ons for this pricing</p>
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
                <!-- Background overlay -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <!-- Modal panel -->
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
                <i class="fas fa-save mr-2"></i>Update Pricing
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
    $attachedAddonsJson = $pricing->addons->map(function($addon) {
        return [
            'id' => $addon->id,
            'name' => $addon->name,
            'pricing_type' => $addon->pricing_type,
            'pricing_type_label' => ucfirst(str_replace('_', ' ', $addon->pricing_type)),
            'base_price' => number_format($addon->base_price, 2),
            'is_included' => (bool) $addon->pivot->is_included,
            'is_required' => (bool) $addon->pivot->is_required,
            'override_price' => $addon->pivot->override_price ? number_format($addon->pivot->override_price, 2) : '',
        ];
    });
@endphp

<script>
/**
 * Alpine.js component for managing addons with modal
 */
function addonsManager() {
    return {
        showModal: false,
        pricingMode: '{{ $pricing->pricing_mode }}',
        // All available addons from database
        allAddons: @json($allAddonsJson),
        // Currently attached addons
        attachedAddons: @json($attachedAddonsJson),
        
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

/**
 * Tour Pricing Edit Form JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    const pricingMode = document.getElementById('pricing_mode');
    const groupSection = document.getElementById('group-pricing-section');
    const privateSection = document.getElementById('private-pricing-section');
    
    let groupPriceIndex = {{ $pricing->pricing_mode === 'group' ? $pricing->groupPrices->count() : 1 }};
    let privatePriceIndex = {{ $pricing->pricing_mode === 'private' ? $pricing->privatePrices->count() : 1 }};

    function toggleSections() {
        const mode = pricingMode.value;
        
        if (mode === 'group') {
            groupSection.classList.remove('hidden');
            privateSection.classList.add('hidden');
            document.querySelectorAll('.group-price-field[data-required="true"]').forEach(field => {
                field.setAttribute('required', 'required');
                field.disabled = false;
            });
            document.querySelectorAll('.private-price-field[data-required="true"]').forEach(field => {
                field.removeAttribute('required');
                field.disabled = true;
            });
        } else if (mode === 'private') {
            groupSection.classList.add('hidden');
            privateSection.classList.remove('hidden');
            document.querySelectorAll('.private-price-field[data-required="true"]').forEach(field => {
                field.setAttribute('required', 'required');
                field.disabled = false;
            });
            document.querySelectorAll('.group-price-field[data-required="true"]').forEach(field => {
                field.removeAttribute('required');
                field.disabled = true;
            });
        } else {
            groupSection.classList.add('hidden');
            privateSection.classList.add('hidden');
            document.querySelectorAll('.group-price-field[data-required="true"], .private-price-field[data-required="true"]').forEach(field => {
                field.removeAttribute('required');
                field.disabled = true;
            });
        }
    }

    pricingMode.addEventListener('change', toggleSections);
    toggleSections();

    // Ajouter une nouvelle catégorie
    document.getElementById('add-group-price').addEventListener('click', function() {
        const container = document.getElementById('group-prices-container');
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
    });

    // Fonction pour mettre à jour la visibilité des boutons de suppression
    function updateDeleteButtonsVisibility() {
        const container = document.getElementById('group-prices-container');
        const items = container.querySelectorAll('.group-price-item');
        items.forEach((item, index) => {
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

    // Supprimer une catégorie
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-group-price-item')) {
            const item = e.target.closest('.group-price-item');
            const container = document.getElementById('group-prices-container');
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

    document.getElementById('add-private-price').addEventListener('click', function() {
        const container = document.getElementById('private-prices-container');
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
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-private-price-item')) {
            const item = e.target.closest('.private-price-item');
            const container = document.getElementById('private-prices-container');
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

<!-- Formulaires de suppression d'hébergements (en dehors du formulaire principal) -->
@if($pricing->accommodations && $pricing->accommodations->count() > 0)
    @foreach($pricing->accommodations as $accommodation)
        <form id="detach-accommodation-{{ $accommodation->id }}" 
              action="{{ route('admin.tour-pricings.accommodations.detach', ['tourPricing' => $pricing->id, 'accommodation' => $accommodation->id]) }}" 
              method="POST" 
              style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif

<script>
function detachAccommodation(pricingId, accommodationId, accommodationName) {
    if (confirm('Êtes-vous sûr de vouloir détacher l\'hébergement "' + accommodationName + '" de cette formule ?')) {
        const form = document.getElementById('detach-accommodation-' + accommodationId);
        if (form) {
            form.submit();
        }
    }
}
</script>
@endsection
