@extends('admin.layout')

@section('title', 'Catégories')

@section('content')
    <div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Liste des catégories</h2>
            <p class="text-sm text-gray-500">Gérez les catégories de tours</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle Catégorie
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-visible">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tours</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Meta Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    {{-- Main Category --}}
                    <tr class="hover:bg-gray-50 bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">#{{ $category->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-folder text-purple-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 font-mono">{{ $category->slug }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $category->tours_count }} tour(s)
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ Str::limit($category->meta_title, 40) ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-admin.action-menu>
                                <a href="{{ route('admin.categories.translations', $category) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-language text-purple-500"></i>
                                    Traductions
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-edit text-indigo-500"></i>
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?')">
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
                    {{-- Subcategories --}}
                    @foreach($category->children as $subcategory)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $subcategory->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center pl-8">
                                    <i class="fas fa-arrow-right text-gray-400 mr-2 text-xs"></i>
                                    <i class="fas fa-folder-open text-blue-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">{{ $subcategory->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500 font-mono">{{ $subcategory->slug }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $subcategory->tours_count }} tour(s)
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ Str::limit($subcategory->meta_title, 40) ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <x-admin.action-menu>
                                    <a href="{{ route('admin.categories.translations', $subcategory) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                        <i class="fas fa-language text-purple-500"></i>
                                        Traductions
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $subcategory) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                        <i class="fas fa-edit text-indigo-500"></i>
                                        Modifier
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $subcategory) }}" onsubmit="return confirm('Supprimer cette sous-catégorie ?')">
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
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-gray-500">Aucune catégorie</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
