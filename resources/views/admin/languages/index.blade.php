@extends('admin.layout')

@section('title', 'Gestion des Langues')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-language mr-2"></i>Gestion des Langues</h1>
            <p class="text-gray-600 mt-2">Activez, désactivez ou ajoutez des langues pour votre site</p>
        </div>
        <a href="{{ route('admin.languages.create') }}" 
           class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Ajouter une langue
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Ordre
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Drapeau
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Code
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Nom
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Nom natif
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Par défaut
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($languages as $language)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $language->order }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($language->code) }} fis" style="font-size: 1.875rem;"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-bold bg-indigo-100 text-indigo-800 rounded-full uppercase">
                                {{ $language->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $language->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $language->native_name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($language->is_active)
                                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                    ✓ Activée
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">
                                    ✗ Désactivée
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($language->is_default)
                                <span class="px-3 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">
                                    ⭐ Par défaut
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.languages.set-default', $language) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Définir par défaut
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.action-menu>
                                <form method="POST" action="{{ route('admin.languages.toggle-active', $language) }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 {{ $language->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }}"
                                            {{ $language->is_default && $language->is_active ? 'disabled' : '' }}>
                                        @if($language->is_active)
                                            <i class="fas fa-times-circle"></i>
                                            Désactiver
                                        @else
                                            <i class="fas fa-check-circle"></i>
                                            Activer
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('admin.languages.edit', $language) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-edit text-indigo-500"></i>
                                    Modifier
                                </a>
                                @if(!$language->is_default)
                                    <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" onsubmit="return confirm('Supprimer cette langue ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </x-admin.action-menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <p class="text-gray-500">Aucune langue</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-2">💡 Comment ça marche ?</h3>
        <ul class="text-sm text-blue-800 space-y-2">
            <li>• <strong>Activée</strong> : La langue est disponible sur le site et dans le sélecteur</li>
            <li>• <strong>Désactivée</strong> : La langue est cachée mais les traductions sont conservées</li>
            <li>• <strong>Par défaut</strong> : La langue utilisée si aucune autre n'est sélectionnée</li>
            <li>• <strong>Ordre</strong> : Définit l'ordre d'affichage dans le sélecteur de langue</li>
        </ul>
    </div>
@endsection
