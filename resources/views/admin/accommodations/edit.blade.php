@extends('admin.layout')

@section('title', 'Modifier un Hébergement')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.accommodations.index') }}" class="text-blue-600 hover:underline">← Retour aux Hébergements</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Modifier l'Hébergement: {{ $accommodation->name }}</h2>
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

<form action="{{ route('admin.accommodations.update', $accommodation) }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-4xl">
    @csrf
    @method('PUT')
    @php
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'Hébergement *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $accommodation->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $accommodation->slug) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
            <input type="text" name="location" id="location" value="{{ old('location', $accommodation->location) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="ex: Marrakech, Casablanca">
        </div>

        <div>
            <label for="stars" class="block text-sm font-medium text-gray-700 mb-2">Nombre d'Étoiles</label>
            <select name="stars" id="stars" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Aucune évaluation</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('stars', $accommodation->stars) == $i ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'étoiles' : 'étoile' }}</option>
                @endfor
            </select>
        </div>

        <div class="md:col-span-2">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse Complète</label>
            <input type="text" name="address" id="address" value="{{ old('address', $accommodation->address) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="ex: Avenue Mohamed VI, Guéliz, 40000 Marrakech">
        </div>

        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Description de l'hébergement...">{{ old('description', $accommodation->description) }}</textarea>
        </div>
    </div>

    <!-- Translations Section -->
    <div class="mb-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
        <div class="px-4 pt-3 pb-0">
            <h3 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-language mr-1"></i> Traductions</h3>
            <div class="flex space-x-1 border-b border-gray-200">
                @foreach($availableLocales as $locale)
                    @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                    <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="inline-flex items-center px-3 py-2 border-b-2 text-sm font-medium transition-colors">
                        <span class="mr-1"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1rem;"></span></span>
                        <span>{{ strtoupper($locale) }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="p-4">
            @foreach($availableLocales as $locale)
                @php
                    $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                    $translation = $accommodation->translations->where('locale', $locale)->first();
                @endphp
                <div x-show="activeTab === '{{ $locale }}'" x-transition>
                    <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom ({{ $localeInfo['native'] }}) *</label>
                            <input type="text" name="translations[{{ $loop->index }}][name]"
                                   value="{{ old("translations.{$loop->index}.name", $translation?->name ?? $accommodation->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description ({{ $localeInfo['native'] }})</label>
                            <textarea name="translations[{{ $loop->index }}][description]" rows="2"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old("translations.{$loop->index}.description", $translation?->description ?? $accommodation->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Localisation ({{ $localeInfo['native'] }})</label>
                            <input type="text" name="translations[{{ $loop->index }}][location]"
                                   value="{{ old("translations.{$loop->index}.location", $translation?->location ?? $accommodation->location) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Types de Chambres -->
    <div class="mb-6">
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Types de Chambres</h3>
                    <p class="text-sm text-gray-600">Configurez les types de chambres disponibles (Simple, Double, Twin, Triple) avec leurs prix par nuit</p>
                </div>
                <button type="button" id="add-room" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fas fa-plus mr-2"></i>Ajouter une Chambre
                </button>
            </div>
            
            <div id="rooms-container" class="space-y-3">
                <!-- Les chambres existantes seront chargées ici -->
            </div>
        </div>
    </div>

    <div class="mb-6">
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $accommodation->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-700">Actif</span>
        </label>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.accommodations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Annuler
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
            Enregistrer les Modifications
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomsContainer = document.getElementById('rooms-container');
    const addRoomBtn = document.getElementById('add-room');
    let roomIndex = 0;

    const roomTypes = {
        'single': 'Chambre Simple',
        'double': 'Chambre Double',
        'twin': 'Chambre Twin',
        'triple': 'Chambre Triple'
    };

    // Charger les chambres existantes
    @php
        $roomsData = $accommodation->rooms->map(function($room) {
            return [
                'id' => $room->id,
                'room_type' => $room->room_type,
                'price_per_night' => $room->price_per_night,
                'max_occupancy' => $room->max_occupancy,
                'description' => $room->description,
                'is_active' => $room->is_active,
            ];
        })->values()->all();
    @endphp
    const existingRooms = @json($roomsData);

    function createRoomField(roomData = {}) {
        const roomId = roomData.id || '';
        const roomType = roomData.room_type || 'single';
        const price = roomData.price_per_night || '';
        const occupancy = roomData.max_occupancy || '1';
        const description = roomData.description || '';
        const isActive = roomData.is_active !== undefined ? roomData.is_active : true;

        const roomDiv = document.createElement('div');
        roomDiv.className = 'room-item bg-white border border-gray-200 rounded-lg p-4 relative';
        
        roomDiv.innerHTML = `
            <button type="button" class="remove-room absolute top-2 right-2 text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50" title="Supprimer">
                <i class="fas fa-trash-alt text-sm"></i>
            </button>
            ${roomId ? `<input type="hidden" name="rooms[${roomIndex}][id]" value="${roomId}">` : ''}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type de Chambre *</label>
                    <select name="rooms[${roomIndex}][room_type]" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-2" required>
                        ${Object.entries(roomTypes).map(([value, label]) => 
                            `<option value="${value}" ${roomType === value ? 'selected' : ''}>${label}</option>`
                        ).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Prix par Nuit (€) *</label>
                    <input type="number" name="rooms[${roomIndex}][price_per_night]" value="${price}" step="0.01" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-2" placeholder="50.00" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Occupation Max *</label>
                    <input type="number" name="rooms[${roomIndex}][max_occupancy]" value="${occupancy}" min="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-2" placeholder="2" required>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="rooms[${roomIndex}][is_active]" value="1" ${isActive ? 'checked' : ''} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                    </label>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description (optionnel)</label>
                <textarea name="rooms[${roomIndex}][description]" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500" placeholder="Description de la chambre...">${description}</textarea>
            </div>
        `;
        
        roomsContainer.appendChild(roomDiv);
        roomIndex++;
    }

    // Charger les chambres existantes
    if (existingRooms && existingRooms.length > 0) {
        existingRooms.forEach(room => {
            createRoomField(room);
        });
    } else {
        // Si aucune chambre, créer une chambre par défaut
        createRoomField();
    }

    addRoomBtn.addEventListener('click', function() {
        createRoomField();
    });

    // Supprimer une chambre
    roomsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-room')) {
            const roomItem = e.target.closest('.room-item');
            const items = roomsContainer.querySelectorAll('.room-item');
            
            if (items.length > 1) {
                roomItem.remove();
                // Réindexer
                roomIndex = 0;
                roomsContainer.querySelectorAll('.room-item').forEach((item) => {
                    item.querySelectorAll('input, select, textarea').forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/rooms\[\d+\]/, `rooms[${roomIndex}]`);
                        }
                    });
                    roomIndex++;
                });
            } else {
                alert('Vous devez avoir au moins une chambre.');
            }
        }
    });
});
</script>
@endsection
