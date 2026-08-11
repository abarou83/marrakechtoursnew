@extends('admin.layout')

@section('title', 'Clients')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Clients inscrits</h2>
        <p class="text-sm text-gray-500">Comptes créés sur le site (inscription, checkout ou Google).</p>
    </div>
    <div class="flex items-center gap-3 text-sm text-gray-500">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
            <i class="fas fa-user-friends text-indigo-500"></i>
            {{ $clients->total() }} clients
        </span>
    </div>
</div>

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="search"
               name="search"
               value="{{ $search }}"
               placeholder="Rechercher par nom, email ou téléphone…"
               class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
            @if($search)
                <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    Effacer
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-visible">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Inscription</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Réservations</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Inscrit le</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $client->name }}</p>
                                <p class="text-xs text-gray-500">#{{ $client->id }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <a href="mailto:{{ $client->email }}" class="hover:text-indigo-600">{{ $client->email }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="hover:text-indigo-600">{{ $client->phone }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($client->google_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fab fa-google mr-1"></i> Google
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    Email
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->bookings_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-admin.action-menu>
                                <a href="{{ route('admin.clients.show', $client) }}" class="flex w-full items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-eye"></i>
                                    Voir le détail
                                </a>
                                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Supprimer ce client et ses données associées ?')">
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            @if($search)
                                Aucun client trouvé pour « {{ $search }} ».
                            @else
                                Aucun client inscrit pour le moment.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection
