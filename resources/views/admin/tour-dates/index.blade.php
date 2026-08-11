@extends('admin.layout')

@section('title', 'Gestion des Heures')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tours.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux tours
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Heures de départ pour : {{ $tour->title }}</h2>
                <p class="text-sm text-gray-600 mt-1">Gérez les heures de départ des tours</p>
            </div>
            <a href="{{ route('admin.tour-dates.create', $tour) }}" 
               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                <i class="fas fa-plus mr-2"></i>Ajouter une Heure
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($tourDates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Heure de Départ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Heure de Fin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Capacité
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Réservations
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Disponibilité
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tourDates as $tourDate)
                            @php
                                $booked = $tourDate->bookings()->where('status', '!=', 'canceled')->sum('seats');
                                $available = $tourDate->capacity - $booked;
                                $isPast = $tourDate->start_at->isPast();
                            @endphp
                            <tr class="{{ $isPast ? 'bg-gray-50 opacity-75' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600">
                                        <i class="fas fa-clock mr-1"></i>{{ $tourDate->start_at->format('H:i') }}
                                    </div>
                                    @if($isPast)
                                        <span class="text-xs text-red-600">(Passé)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">
                                        {{ $tourDate->end_at ? $tourDate->end_at->format('H:i') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $tourDate->capacity }} places
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm {{ $booked > 0 ? 'text-orange-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $booked }} réservée(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold {{ $available > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $available }} disponible(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.tour-dates.edit', [$tour, $tourDate]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-bold">
                                            <i class="fas fa-edit mr-1"></i>Modifier
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.tour-dates.destroy', [$tour, $tourDate]) }}" 
                                              class="inline"
                                              onsubmit="return confirm('Supprimer cette heure de départ ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 font-bold">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $tourDates->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clock text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg font-semibold mb-2">Aucune heure configurée</p>
                <p class="text-gray-500 text-sm mb-6">Commencez par ajouter une heure de départ pour ce tour.</p>
                <a href="{{ route('admin.tour-dates.create', $tour) }}" 
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Ajouter une Heure
                </a>
            </div>
        @endif
    </div>
@endsection
