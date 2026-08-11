@extends('admin.layout')

@section('title', 'Devis')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Devis</h2>
        <p class="text-sm text-gray-500">Gérez les demandes de devis de vos clients.</p>
    </div>
    <div class="flex items-center gap-3 text-sm text-gray-500">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
            <i class="fas fa-file-invoice text-blue-500"></i>
            {{ $quotes->total() }} devis
        </span>
    </div>
</div>

{{-- Filtres --}}
<div class="mb-6 bg-white rounded-lg shadow p-4 border border-gray-200">
    <form method="GET" action="{{ route('admin.quotes.index') }}" class="flex items-end gap-4">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, message..." class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Statut</label>
            <select name="status" class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tous les statuts</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm">
            <i class="fas fa-search mr-2"></i>Filtrer
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.quotes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
                <i class="fas fa-times mr-2"></i>Réinitialiser
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-visible">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Réf.</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tour</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Message</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($quotes as $quote)
                <tr class="hover:bg-gray-50 {{ $quote->status === 'pending' ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 text-sm text-gray-600">#{{ $quote->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-semibold">{{ $quote->name }}</div>
                        <div class="text-xs text-gray-500">{{ $quote->email }}</div>
                        @if($quote->phone)
                            <div class="text-xs text-gray-500">{{ $quote->phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($quote->tour)
                            <div class="font-semibold text-gray-900">{{ translate_model($quote->tour, 'title') }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                        @if($quote->message)
                            <div class="truncate" title="{{ $quote->message }}">
                                {{ Str::limit($quote->message, 60) }}
                            </div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $quote->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $quote->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'viewed' => 'bg-blue-100 text-blue-800',
                                'contacted' => 'bg-indigo-100 text-indigo-800',
                                'accepted' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'viewed' => 'Vue',
                                'contacted' => 'Contactée',
                                'accepted' => 'Acceptée',
                                'rejected' => 'Refusée',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$quote->status] ?? ucfirst($quote->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.action-menu>
                            <a href="{{ route('admin.quotes.show', $quote) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                <i class="fas fa-eye text-indigo-500"></i>
                                Voir les détails
                            </a>
                            <form method="POST" action="{{ route('admin.quotes.updateStatus', $quote) }}" class="px-4 py-2">
                                @csrf
                                @method('PATCH')
                                <div class="text-xs font-semibold text-gray-400 uppercase mb-2">Changer le statut</div>
                                <div class="space-y-2">
                                    @foreach(['pending' => 'En attente', 'viewed' => 'Vue', 'contacted' => 'Contactée', 'accepted' => 'Acceptée', 'rejected' => 'Refusée'] as $statusValue => $label)
                                        <button type="submit" name="status" value="{{ $statusValue }}" class="flex w-full items-center gap-2 px-3 py-2 rounded hover:bg-gray-50 {{ $quote->status === $statusValue ? 'text-indigo-600 font-semibold' : '' }}">
                                            <i class="fas fa-circle text-xs"></i>
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.quotes.destroy', $quote) }}" onsubmit="return confirm('Supprimer ce devis ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </x-admin.action-menu>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Aucun devis trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($quotes->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $quotes->links() }}
        </div>
    @endif
</div>
@endsection

