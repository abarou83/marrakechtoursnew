@extends('admin.layout')

@section('title', 'Détails du Menu')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.menus.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
            ← Retour aux menus
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $menu->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">Slug: {{ $menu->slug }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.menus.edit', $menu) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <form method="POST" action="{{ route('admin.menus.toggle-active', $menu) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 {{ $menu->is_active ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white rounded-lg font-semibold">
                        {{ $menu->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-600">Emplacement</div>
                <div class="text-lg font-bold text-gray-900">{{ \App\Models\Menu::locationLabels()[$menu->location ?? 'header'] ?? 'Header' }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-600">Position</div>
                <div class="text-2xl font-bold text-gray-900">{{ $menu->position }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-600">Items</div>
                <div class="text-2xl font-bold text-gray-900">{{ $menu->allItems->count() }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-600">Statut</div>
                <div class="text-2xl font-bold {{ $menu->is_active ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $menu->is_active ? 'Actif' : 'Inactif' }}
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Items du Menu</h3>
            @if($menu->allItems->count() > 0)
                <div class="space-y-2">
                    @foreach($menu->allItems->sortBy('order') as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    @if($item->icon)
                                        <i class="{{ $item->icon }} text-primary"></i>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $item->label }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($item->link_type === 'category' && $item->category)
                                                Catégorie: {{ translate_model($item->category, 'name') }}
                                            @elseif($item->link_type === 'page' && $item->page)
                                                Page: {{ translate_model($item->page, 'title') }}
                                            @elseif($item->link_type === 'tour' && $item->tour)
                                                Tour: {{ translate_model($item->tour, 'title') }}
                                            @elseif(in_array($item->link_type, ['external', 'custom', 'internal']))
                                                Lien personnalisé: {{ $item->link_url }}
                                            @else
                                                Lien: {{ $item->link_url }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <span class="text-sm text-gray-500">Ordre: {{ $item->order }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun item dans ce menu</p>
            @endif
        </div>
    </div>
@endsection






