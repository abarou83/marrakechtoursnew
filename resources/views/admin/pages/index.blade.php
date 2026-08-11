@extends('admin.layout')

@section('title', 'Gestion des Pages')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-file-alt mr-2"></i>Gestion des Pages</h1>
            <p class="text-gray-600 mt-2">Gérez les pages statiques de votre site (Confidentialité, Conditions, etc.)</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" 
           class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Ajouter une Page
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Ordre
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Slug
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Titre
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pages as $page)
                    @php
                        $translation = $page->translate();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $page->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $translation ? $translation->slug : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $translation ? $translation->title : 'Aucune traduction' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($page->is_active)
                                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                    ✓ Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">
                                    ✗ Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-admin.action-menu>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-edit text-indigo-500"></i>
                                    Modifier
                                </a>
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette page ?');">
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
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Aucune page pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

