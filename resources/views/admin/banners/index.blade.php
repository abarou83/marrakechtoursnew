@extends('admin.layout')

@section('title', 'Gestion des Bannières')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h1 class="text-lg font-semibold text-gray-900 mb-1">Gestion des bannières</h1>
        <p class="text-sm text-gray-500">Visualisez et organisez les visuels affichés sur votre site.</p>
    </div>
    <a href="{{ route('admin.banners.create') }}"
       class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
        <i class="fas fa-plus mr-2"></i>Nouvelle bannière
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-visible">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aperçu</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lien</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Active</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ordre</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($banners as $banner)
                @php
                    $translation = $banner->translate();
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @php
                            $displayImage = $banner->primaryImage ?? $banner->images->first() ?? null;
                            $imagePath = $displayImage ? $displayImage->path : $banner->image_path;
                            $imageCount = $banner->images->count();
                        @endphp
                        <div class="relative">
                            <div class="w-24 h-14 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ Storage::url($imagePath) }}" alt="{{ $translation ? $translation->title : 'Bannière' }}" class="w-full h-full object-cover">
                            </div>
                            @if($imageCount > 1)
                                <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $imageCount }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ $translation ? $translation->title : 'Aucune traduction' }}</div>
                        @if($translation?->subtitle)
                            <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($translation->subtitle, 60) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $translation->slug ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($banner->link_url)
                            <a href="{{ $banner->link_url }}" target="_blank" class="text-indigo-600 hover:underline break-all">
                                {{ \Illuminate\Support\Str::limit($banner->link_url, 40) }}
                            </a>
                        @else
                            <span class="text-sm text-gray-400">Aucun lien</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($banner->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Oui
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                Non
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $banner->order }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <x-admin.action-menu>
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                <i class="fas fa-edit text-indigo-500"></i>
                                Modifier
                            </a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Supprimer cette bannière ?');">
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Aucune bannière</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
