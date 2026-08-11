@extends('admin.layout')

@section('title', 'Edit Addon')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.addons.index') }}" class="text-indigo-600 hover:underline">← Back to Addons</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Addon</h2>
</div>

@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.addons.update', $addon) }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl">
    @csrf
    @method('PUT')

    @php
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
    @endphp

    <div class="mb-6">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $addon->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <!-- Translations Section -->
    <div class="mb-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
        <div class="px-4 pt-3 pb-0">
            <h3 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-language mr-1"></i> Nom par langue</h3>
            <div class="flex space-x-1 border-b border-gray-200">
                @foreach($availableLocales as $locale)
                    @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                    <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="inline-flex items-center px-3 py-2 border-b-2 text-sm font-medium transition-colors">
                        <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-1" style="font-size: 1rem;"></span>
                        <span>{{ strtoupper($locale) }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="p-4">
            @foreach($availableLocales as $locale)
                @php
                    $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                    $translation = $addon->translations->where('locale', $locale)->first();
                @endphp
                <div x-show="activeTab === '{{ $locale }}'" x-transition>
                    <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nom ({{ $localeInfo['native'] }}) <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="translations[{{ $loop->index }}][name]"
                           value="{{ old("translations.{$loop->index}.name", $translation->name ?? $addon->name) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required
                           placeholder="Nom de l'addon en {{ $localeInfo['native'] }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-6">
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $addon->slug) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from name</p>
    </div>

    <div class="mb-6">
        <label for="pricing_type" class="block text-sm font-medium text-gray-700 mb-2">Pricing Type *</label>
        <select name="pricing_type" id="pricing_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="per_person" {{ old('pricing_type', $addon->pricing_type) === 'per_person' ? 'selected' : '' }}>Per Person</option>
            <option value="per_group" {{ old('pricing_type', $addon->pricing_type) === 'per_group' ? 'selected' : '' }}>Per Group</option>
            <option value="free" {{ old('pricing_type', $addon->pricing_type) === 'free' ? 'selected' : '' }}>Free</option>
        </select>
    </div>

    <div class="mb-6" id="base-price-section">
        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">Base Price (€) *</label>
        <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $addon->base_price) }}" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-sm text-gray-500">Used if no price tiers are configured</p>
    </div>

    <!-- Price Tiers Section (for per_person addons) -->
    <div class="mb-6" id="price-tiers-section" style="display: {{ old('pricing_type', $addon->pricing_type) === 'per_person' ? 'block' : 'none' }};">
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Price Tiers (by number of people)</h3>
                    <p class="text-sm text-gray-600">Configure tiered pricing based on number of people (e.g., 1-5 = 20€, 6-8 = 60€)</p>
                </div>
            </div>
            
            <div id="price-tiers-container" class="space-y-3">
                @if($addon->priceTiers->count() > 0)
                    @foreach($addon->priceTiers as $index => $tier)
                        <div class="price-tier-item bg-white border border-gray-200 rounded-lg p-4 relative">
                            <button type="button" class="remove-price-tier absolute top-2 right-2 text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50 {{ $addon->priceTiers->count() <= 1 ? 'hidden' : '' }}" title="Delete Tier">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Min People *</label>
                                    <input type="number" name="price_tiers[{{ $index }}][min_people]" value="{{ $tier->min_people }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" min="1" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max People *</label>
                                    <input type="number" name="price_tiers[{{ $index }}][max_people]" value="{{ $tier->max_people }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" min="1" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (€) *</label>
                                    <input type="number" name="price_tiers[{{ $index }}][price]" value="{{ $tier->price }}" step="0.01" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" required>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="price-tier-item bg-white border border-gray-200 rounded-lg p-4 relative">
                        <button type="button" class="remove-price-tier absolute top-2 right-2 text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50 hidden" title="Delete Tier">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Min People</label>
                                <input type="number" name="price_tiers[0][min_people]" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" min="1" placeholder="1">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Max People</label>
                                <input type="number" name="price_tiers[0][max_people]" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" min="1" placeholder="5">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Price (€)</label>
                                <input type="number" name="price_tiers[0][price]" step="0.01" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all py-2" placeholder="20.00">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <button type="button" id="add-price-tier" class="mt-4 w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Another Tier
            </button>
        </div>
    </div>

    <div class="mb-6">
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $addon->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Active</span>
        </label>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.addons.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Update Addon
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricingType = document.getElementById('pricing_type');
    const basePriceSection = document.getElementById('base-price-section');
    const basePriceInput = document.getElementById('base_price');
    const priceTiersSection = document.getElementById('price-tiers-section');

    function toggleSections() {
        if (pricingType.value === 'free') {
            basePriceSection.classList.add('hidden');
            priceTiersSection.style.display = 'none';
            basePriceInput.removeAttribute('required');
        } else if (pricingType.value === 'per_person') {
            basePriceSection.classList.remove('hidden');
            priceTiersSection.style.display = 'block';
            basePriceInput.setAttribute('required', 'required');
        } else {
            basePriceSection.classList.remove('hidden');
            priceTiersSection.style.display = 'none';
            basePriceInput.setAttribute('required', 'required');
        }
    }

    pricingType.addEventListener('change', toggleSections);
    toggleSections();

    // Price tiers management
    let priceTierIndex = {{ $addon->priceTiers->count() > 0 ? $addon->priceTiers->count() : 1 }};

    function updateDeleteButtonsVisibility() {
        const container = document.getElementById('price-tiers-container');
        const items = container.querySelectorAll('.price-tier-item');
        items.forEach((item) => {
            const deleteBtn = item.querySelector('.remove-price-tier');
            if (deleteBtn) {
                if (items.length > 1) {
                    deleteBtn.classList.remove('hidden');
                } else {
                    deleteBtn.classList.add('hidden');
                }
            }
        });
    }

    document.getElementById('add-price-tier').addEventListener('click', function() {
        const container = document.getElementById('price-tiers-container');
        const newItem = container.firstElementChild.cloneNode(true);
        newItem.querySelectorAll('input').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, '[' + priceTierIndex + ']');
                input.value = '';
            }
        });
        const deleteBtn = newItem.querySelector('.remove-price-tier');
        if (deleteBtn) {
            deleteBtn.classList.remove('hidden');
        }
        container.appendChild(newItem);
        priceTierIndex++;
        updateDeleteButtonsVisibility();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-price-tier')) {
            const item = e.target.closest('.price-tier-item');
            const container = document.getElementById('price-tiers-container');
            const items = container.querySelectorAll('.price-tier-item');
            
            if (items.length > 1) {
                item.remove();
                updateDeleteButtonsVisibility();
                container.querySelectorAll('.price-tier-item').forEach((item, index) => {
                    item.querySelectorAll('input').forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/price_tiers\[\d+\]/, 'price_tiers[' + index + ']');
                        }
                    });
                });
            }
        }
    });

    updateDeleteButtonsVisibility();
});
</script>
@endsection
