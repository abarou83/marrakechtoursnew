@extends('admin.layout')

@section('title', 'Ajouter une Heure')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tour-dates.index', $tour) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux heures
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Ajouter une Heure de Départ pour : {{ $tour->title }}</h2>
        <p class="text-sm text-gray-600 mb-6">Vous pouvez ajouter une heure sans spécifier de date (la date d'aujourd'hui sera utilisée par défaut)</p>
        
        <form method="POST" action="{{ route('admin.tour-dates.store', $tour) }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Heure de Départ <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="departure_time" value="{{ old('departure_time') }}" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3 text-lg font-semibold" required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Heure de départ du tour (ex: 09:00, 14:30)
                    </p>
                    @error('departure_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Heure de Fin (optionnel)
                    </label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" 
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
                <input type="number" name="capacity" value="{{ old('capacity', $tour->capacity) }}" 
                       min="1" class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                @error('capacity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Capacité par défaut du tour : {{ $tour->capacity }} places</p>
            </div>

            <div class="flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-clock mr-2"></i>Ajouter l'Heure
                </button>
                <a href="{{ route('admin.tour-dates.index', $tour) }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection




