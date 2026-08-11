@extends('admin.layout')

@section('title', 'Gestion des Hébergements')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Gestion des Hébergements</h2>
        <p class="text-sm text-gray-500">Gérez les hébergements (hôtels, riads, etc.) et leurs types de chambres</p>
    </div>
    <a href="{{ route('admin.accommodations.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
        <i class="fas fa-plus mr-2"></i>Nouvel Hébergement
    </a>
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

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Localisation</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Étoiles</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Types de Chambres</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Formules</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($accommodations as $accommodation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-semibold">{{ $accommodation->name }}</div>
                        <div class="text-xs text-gray-500">{{ $accommodation->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $accommodation->location ?? '-' }}</div>
                        @if($accommodation->address)
                            <div class="text-xs text-gray-500">{{ Str::limit($accommodation->address, 30) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($accommodation->stars)
                            <div class="flex items-center">
                                @for($i = 0; $i < $accommodation->stars; $i++)
                                    <i class="fas fa-star text-yellow-400"></i>
                                @endfor
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-wrap gap-1">
                            @forelse($accommodation->rooms as $room)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $room->room_type_name }}
                                    <span class="ml-1">({{ number_format($room->price_per_night, 2) }}€)</span>
                                </span>
                            @empty
                                <span class="text-gray-400 text-xs">Aucune chambre</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $accommodation->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $accommodation->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $accommodation->tourPricings()->count() }} formule(s)
                    </td>
                    <td class="px-6 py-4 text-sm text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.accommodations.translations', $accommodation) }}" class="text-purple-600 hover:text-purple-800" title="Traductions">
                                <i class="fas fa-language"></i>
                            </a>
                            <a href="{{ route('admin.accommodations.show', $accommodation) }}" class="text-blue-600 hover:text-blue-800" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.accommodations.edit', $accommodation) }}" class="text-indigo-600 hover:text-indigo-800" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.accommodations.destroy', $accommodation) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet hébergement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        Aucun hébergement trouvé. <a href="{{ route('admin.accommodations.create') }}" class="text-blue-600 hover:underline">Créer un hébergement</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($accommodations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $accommodations->links() }}
        </div>
    @endif
</div>
@endsection
