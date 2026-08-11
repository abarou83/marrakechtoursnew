@extends('admin.layout')

@section('title', 'Modifier l\'Heure')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tour-dates.index', $tour) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux heures
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Modifier l'Heure de Départ pour : {{ $tour->title }}</h2>
        
        <form method="POST" action="{{ route('admin.tour-dates.update', [$tour, $tourDate]) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Date de la visite <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date" 
                       value="{{ old('date', $tourDate->start_at->format('Y-m-d')) }}" 
                       class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                @error('date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Heure de Départ <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="departure_time" 
                           value="{{ old('departure_time', $tourDate->start_at->format('H:i')) }}" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                    <p class="text-xs text-gray-500 mt-1">Heure de départ du tour</p>
                    @error('departure_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Heure de Fin (optionnel)
                    </label>
                    <input type="time" name="end_time" 
                           value="{{ old('end_time', $tourDate->end_at ? $tourDate->end_at->format('H:i') : '') }}" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500 mt-1">Heure de fin estimée du tour</p>
                    @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Capacité <span class="text-red-500">*</span>
                </label>
                <input type="number" name="capacity" value="{{ old('capacity', $tourDate->capacity) }}" 
                       min="1" class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                @error('capacity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                
                @php
                    $booked = $tourDate->bookings()->where('status', '!=', 'canceled')->sum('seats');
                @endphp
                @if($booked > 0)
                    <p class="text-sm text-orange-600 mt-1">
                        ⚠️ Attention : {{ $booked }} place(s) déjà réservée(s). Ne diminuez pas la capacité en dessous de ce nombre.
                    </p>
                @endif
            </div>

            <div class="flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
                <a href="{{ route('admin.tour-dates.index', $tour) }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection



