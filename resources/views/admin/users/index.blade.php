@extends('admin.layout')

@section('title', 'Utilisateurs admin')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Utilisateurs admin</h2>
        <p class="text-sm text-gray-500">Gérez les comptes administrateurs (accès back-office).</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-500">
            <i class="fas fa-users text-indigo-500"></i>
            {{ $users->total() }} utilisateurs
        </span>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Ajouter
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <p class="text-red-800">{{ session('error') }}</p>
    </div>
@endif

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="search" name="search" value="{{ $search }}"
               placeholder="Rechercher par nom ou email…"
               class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Effacer</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-visible">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Créé le</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">#{{ $user->id }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->role === 'admin' ? 'Administrateur' : 'Utilisateur' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth('admin')->id())
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm(@js(
                                              'Supprimer définitivement '.$user->name.' ?'
                                              .($user->bookings_count > 0 ? ' Ses réservations seront également supprimées.' : '')
                                          ))"
                                          class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                <x-admin.action-menu width="w-64">
                                @if($user->id !== auth('admin')->id())
                                    <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-gray-700 text-left">
                                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} text-gray-500 w-4 text-center"></i>
                                            <span>{{ $user->is_active ? 'Désactiver' : 'Activer' }}</span>
                                        </button>
                                    </form>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="px-4 py-2">
                                        @csrf
                                        @method('PATCH')
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Changer le rôle</p>
                                        <div class="space-y-1">
                                            <button type="submit" name="role" value="admin" class="flex w-full items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-50 text-sm text-left {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                                                <i class="fas fa-shield-halved w-4 text-center"></i>
                                                <span>Administrateur</span>
                                            </button>
                                            <button type="submit" name="role" value="user" class="flex w-full items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-50 text-sm text-left {{ $user->role === 'user' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                                                <i class="fas fa-user w-4 text-center"></i>
                                                <span>Utilisateur</span>
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="px-4 py-2.5 text-xs text-gray-400 italic">Compte connecté</div>
                                @endif
                            </x-admin.action-menu>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun utilisateur enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">{{ $users->links() }}</div>
    @endif
</div>
@endsection
