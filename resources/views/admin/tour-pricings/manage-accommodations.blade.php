@extends('admin.layout')

@section('title', 'Gérer les Hébergements - ' . $tourPricing->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tour-pricings.index', $tourPricing->tour) }}" class="text-blue-600 hover:underline">← Retour aux Formules</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Gérer les Hébergements pour: {{ $tourPricing->title }}</h2>
    <p class="text-sm text-gray-600 mt-1">Tour: {{ $tourPricing->tour->title }}</p>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Hébergements associés -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hébergements Associés</h3>
        
        @if($tourPricing->accommodations->count() > 0)
            <div class="space-y-4">
                @foreach($tourPricing->accommodations as $accommodation)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $accommodation->name }}</h4>
                                @if($accommodation->location)
                                    <p class="text-sm text-gray-600">{{ $accommodation->location }}</p>
                                @endif
                                @if($accommodation->stars)
                                    <div class="mt-1">
                                        @for($i = 0; $i < $accommodation->stars; $i++)
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <form action="{{ route('admin.tour-pricings.accommodations.detach', ['tourPricing' => $tourPricing->id, 'accommodation' => $accommodation->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir détacher cet hébergement ?');" title="Détacher">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Types de chambres disponibles -->
                        @if($accommodation->rooms->count() > 0)
                            <div class="mt-3 space-y-2">
                                <p class="text-xs font-semibold text-gray-700 uppercase">Chambres disponibles:</p>
                                @foreach($accommodation->activeRooms as $room)
                                    <div class="flex items-center justify-between text-sm bg-gray-50 p-2 rounded">
                                        <span class="text-gray-700">{{ $room->room_type_name }}</span>
                                        <span class="font-semibold text-blue-600">{{ number_format($room->price_per_night, 2) }}€/nuit</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Paramètres -->
                        <form action="{{ route('admin.tour-pricings.accommodations.update', ['tourPricing' => $tourPricing->id, 'accommodation' => $accommodation->id]) }}" method="POST" class="mt-3 pt-3 border-t border-gray-200">
                            @csrf
                            @method('PUT')
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_optional" value="1" {{ $accommodation->pivot->is_optional ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Optionnel (sinon inclus par défaut)</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <label for="nights_{{ $accommodation->id }}" class="text-sm text-gray-700 font-semibold">Nombre de nuits:</label>
                                    <input type="number" name="nights" id="nights_{{ $accommodation->id }}" value="{{ $accommodation->pivot->nights ?? 1 }}" min="1" class="w-20 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div class="flex items-center gap-2">
                                    <label for="display_order_{{ $accommodation->id }}" class="text-sm text-gray-700">Ordre d'affichage:</label>
                                    <input type="number" name="display_order" id="display_order_{{ $accommodation->id }}" value="{{ $accommodation->pivot->display_order ?? 0 }}" min="0" class="w-20 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <button type="submit" class="ml-auto px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">Mettre à jour</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">Aucun hébergement associé à cette formule.</p>
        @endif
    </div>

    <!-- Ajouter un hébergement -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter un Hébergement</h3>
        
        <form action="{{ route('admin.tour-pricings.accommodations.attach', $tourPricing) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="accommodation_id" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un hébergement *</label>
                <select name="accommodation_id" id="accommodation_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Choisir un hébergement --</option>
                    @foreach($allAccommodations as $accommodation)
                        @if(!$tourPricing->accommodations->contains($accommodation->id))
                            <option value="{{ $accommodation->id }}">{{ $accommodation->name }} @if($accommodation->location)({{ $accommodation->location }})@endif</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_optional" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Optionnel (sinon inclus par défaut)</span>
                </label>
            </div>

            <div class="mb-4">
                <label for="nights" class="block text-sm font-medium text-gray-700 mb-2">Nombre de nuits *</label>
                <input type="number" name="nights" id="nights" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <p class="mt-1 text-xs text-gray-500">Le nombre de nuits d'hébergement pour cette formule</p>
            </div>

            <div class="mb-4">
                <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Ordre d'affichage</label>
                <input type="number" name="display_order" id="display_order" value="0" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Plus le nombre est élevé, plus l'hébergement apparaîtra en premier</p>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-plus mr-2"></i>Associer l'Hébergement
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.accommodations.create') }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-external-link-alt mr-2"></i>Créer un nouvel hébergement
            </a>
        </div>
    </div>
</div>
@endsection
