@extends('admin.layout')

@section('title', 'Tours')

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Liste des tours</h2>
            <p class="text-sm text-gray-500">Gérez tous vos tours touristiques</p>
        </div>
        <div class="flex flex-col lg:items-end gap-3">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.tours.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouveau Tour
                </a>
                <form method="POST" action="{{ route('admin.tours.import-example') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow transition-all">
                        Importer le JSON exemple
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.tours.import-json') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="file"
                       name="json_file"
                       accept=".json,application/json,text/plain"
                       class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white"
                       required>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2.5 bg-gray-900 text-white rounded-lg hover:bg-black font-semibold">
                    Importer un fichier JSON
                </button>
            </form>
        </div>
    </div>

    @if($errors->has('import'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('import') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-visible">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lieu</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Durée</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Prix</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tours as $tour)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">#{{ $tour->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($tour->images->first())
                                    <img src="{{ Storage::url($tour->images->first()->path) }}" 
                                         alt="{{ $tour->title }}" 
                                         class="w-12 h-12 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-image text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $tour->title }}
                                            <a href="{{ route('tours.show', $tour->url_key) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="inline-flex align-middle ml-1 text-blue-600 hover:text-blue-800"
                                               title="Prévisualiser sur le site">
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $tour->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $typeLabels = [
                                    'daytrip' => ['label' => 'Day Trip', 'color' => 'blue'],
                                    'day_trip' => ['label' => 'Day Trip', 'color' => 'blue'],
                                    'activity' => ['label' => 'Activity', 'color' => 'green'],
                                    'excursion' => ['label' => 'Excursion', 'color' => 'purple'],
                                    'circuit' => ['label' => 'Circuit', 'color' => 'orange'],
                                    'multi_day' => ['label' => 'Circuit', 'color' => 'orange'],
                                    'group' => ['label' => 'Group', 'color' => 'indigo'],
                                    'private' => ['label' => 'Private', 'color' => 'pink'],
                                    'shared' => ['label' => 'Shared', 'color' => 'teal'],
                                ];
                                $typeEnum = $tour->type instanceof \App\Enums\TourType
                                    ? $tour->type
                                    : \App\Enums\TourType::tryFromValue(is_string($tour->type ?? null) ? $tour->type : null);
                                $typeKey = $typeEnum?->value ?? 'activity';
                                $typeInfo = $typeLabels[$typeKey] ?? $typeLabels['activity'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $typeInfo['color'] }}-100 text-{{ $typeInfo['color'] }}-800">
                                {{ $typeInfo['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $tour->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tour->location }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tour->duration }}</td>
                        <td class="px-6 py-4">
                            @php
                                $baseAmount = null;
                                $defaultPricing = $tour->defaultPricing();
                                if ($defaultPricing) {
                                    $baseAmount = (float)$defaultPricing->price_min;
                                } elseif (!is_null($tour->price)) {
                                    $baseAmount = (float)$tour->price;
                                } else {
                                    // If no explicit price, try min of all pricings
                                    // Get first active group pricing for normal season, or any active pricing
                                $p = $tour->pricings()
                                    ->where('pricing_mode', 'group')
                                    ->where('season', 'normal')
                                    ->where('is_active', true)
                                    ->with('groupPrices')
                                    ->first() 
                                    ?? $tour->pricings()->active()->with(['groupPrices', 'privatePrices'])->first();
                                    $baseAmount = $p ? (float)$p->price_min : 0.0;
                                }
                                $amountAdmin = \App\Helpers\CurrencyHelper::convert($baseAmount);
                                $formattedAdmin = number_format($amountAdmin, 2, ',', ' ');
                                $symbolAdmin = optional(\App\Helpers\CurrencyHelper::current())->symbol ?: '';
                            @endphp
                            <span class="text-sm font-semibold text-gray-900">{{ $formattedAdmin }} {{ $symbolAdmin }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($tour->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Publié
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Brouillon
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tours.edit', $tour) }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.tours.destroy', $tour) }}"
                                  onsubmit="return confirm('Supprimer ce tour ?')"
                                  class="inline-flex ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <p class="text-gray-500">Aucun tour</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($tours->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $tours->links() }}
            </div>
        @endif
    </div>
@endsection
