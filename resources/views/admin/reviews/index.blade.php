@extends('admin.layout')

@section('title', 'Avis')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Avis des Tours</h2>
        <p class="text-sm text-gray-500">Gérez les avis laissés par les visiteurs sur vos tours.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reviews.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm">
            <i class="fas fa-plus mr-2"></i>Ajouter un avis
        </a>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-500">
            <i class="fas fa-star text-yellow-500"></i>
            {{ $reviews->total() }} avis
        </span>
    </div>
</div>

{{-- Filtres --}}
<div class="mb-6 bg-white rounded-lg shadow p-4 border border-gray-200">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex items-end gap-4">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tour, utilisateur, commentaire..." class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Statut</label>
            <select name="status" class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tous les avis</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm">
            <i class="fas fa-search mr-2"></i>Filtrer
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
                <i class="fas fa-times mr-2"></i>Réinitialiser
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tour</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Utilisateur</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Note</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Commentaire</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 {{ !$review->is_approved ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 text-sm text-gray-600">#{{ $review->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if($review->tour)
                            <div class="font-semibold">{{ translate_model($review->tour, 'title') }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if($review->user)
                            <div class="font-semibold">{{ $review->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $review->user->email }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-{{ $i <= $review->rating ? 'yellow' : 'gray' }}-400 text-xs"></i>
                            @endfor
                            <span class="ml-2 font-semibold text-gray-700">{{ $review->rating }}/5</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                        @if($review->comment)
                            <div class="truncate" title="{{ $review->comment }}">{{ \Illuminate\Support\Str::limit($review->comment, 60) }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $review->created_at->format('d/m/Y') }}
                        <div class="text-xs text-gray-500">{{ $review->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($review->is_approved)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Approuvé
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>En attente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.reviews.edit', $review) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-star text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg font-semibold mb-2">Aucun avis trouvé</p>
                        <p class="text-sm">Commencez par ajouter un avis ou ajustez vos filtres.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($reviews->hasPages())
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
@endif
@endsection

