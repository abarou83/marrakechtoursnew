@extends('admin.layout')

@section('title', 'Détails de l\'Hébergement')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.accommodations.index') }}" class="text-blue-600 hover:underline">← Retour aux Hébergements</a>
    <div class="flex items-center justify-between mt-2">
        <h2 class="text-2xl font-bold text-gray-900">{{ $accommodation->name }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.accommodations.edit', $accommodation) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informations principales -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations Générales</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $accommodation->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $accommodation->slug }}</dd>
                </div>
                @if($accommodation->location)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Localisation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $accommodation->location }}</dd>
                </div>
                @endif
                @if($accommodation->address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $accommodation->address }}</dd>
                </div>
                @endif
                @if($accommodation->stars)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Étoiles</dt>
                    <dd class="mt-1">
                        <div class="flex items-center">
                            @for($i = 0; $i < $accommodation->stars; $i++)
                                <i class="fas fa-star text-yellow-400"></i>
                            @endfor
                        </div>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $accommodation->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $accommodation->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </dd>
                </div>
                @if($accommodation->description)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $accommodation->description }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Types de Chambres -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Types de Chambres</h3>
            @if($accommodation->rooms->count() > 0)
                <div class="space-y-3">
                    @foreach($accommodation->rooms as $room)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $room->room_type_name }}</h4>
                                    <p class="text-sm text-gray-600">Occupation max: {{ $room->max_occupancy }} personne(s)</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($room->price_per_night, 2) }}€</div>
                                    <div class="text-xs text-gray-500">par nuit</div>
                                </div>
                            </div>
                            @if($room->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $room->description }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $room->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $room->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Aucune chambre configurée.</p>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Formules associées -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Formules Associées</h3>
            @if($accommodation->tourPricings->count() > 0)
                <ul class="space-y-2">
                    @foreach($accommodation->tourPricings as $pricing)
                        <li class="flex items-center justify-between text-sm">
                            <div>
                                <a href="{{ route('admin.tour-pricings.edit', ['tour' => $pricing->tour_id, 'pricing' => $pricing->id]) }}" class="text-blue-600 hover:underline">
                                    {{ $pricing->tour->title ?? 'Tour #' . $pricing->tour_id }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $pricing->title }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">Aucune formule associée.</p>
            @endif
        </div>
    </div>
</div>
@endsection
