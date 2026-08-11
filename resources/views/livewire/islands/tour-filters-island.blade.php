<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 island-filters">
    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
        <i class="fas fa-filter text-primary mr-2"></i> {{ __('Filter Tours') }}
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Recherche') }}</label>
            <input type="text" wire:model.live.debounce.400ms="q"
                   placeholder="{{ __('Désert, Ourika, quad...') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Category') }}</label>
            <select wire:model.live="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500">
                <option value="">{{ __('All categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ translate_model($category, 'name') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Location') }}</label>
            <input type="text" wire:model.live.debounce.400ms="location"
                   placeholder="Marrakech, Essaouira..."
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Min Price') }}</label>
            <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="0 €"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Max Price') }}</label>
            <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="1000 €"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500">
        </div>
    </div>

    @if($q || $category || $location || $minPrice || $maxPrice)
        <div class="mt-4 text-center">
            <button type="button" wire:click="resetFilters" class="text-sm text-primary hover:underline">
                {{ __('Reset filters') }}
            </button>
        </div>
    @endif
</div>
