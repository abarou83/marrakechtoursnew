@extends('admin.layout')

@section('title', 'Gestion des Menus')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Menus</h1>
                <p class="text-gray-600">Créez et gérez les menus de navigation multilingues de votre site</p>
            </div>
            <a href="{{ route('admin.menus.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Menu
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Menus List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @forelse($menus as $menu)
            <div class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-primary/10 text-primary font-bold">
                                    #{{ $menu->id }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $menu->name }}</h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        @php
                                            $locationLabels = \App\Models\Menu::locationLabels();
                                            $location = $menu->location ?? 'header';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $location === 'header' ? 'bg-blue-100 text-blue-800' : ($location === 'footer' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $locationLabels[$location] ?? $location }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-link mr-1"></i>
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $menu->slug }}</code>
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-list mr-1"></i>
                                            {{ $menu->all_items_count }} item(s)
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-sort-numeric-up mr-1"></i>
                                            Position: {{ $menu->position }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 mt-4">
                                <form method="POST" action="{{ route('admin.menus.toggle-active', $menu) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $menu->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                        <i class="fas {{ $menu->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1.5"></i>
                                        {{ $menu->is_active ? 'Actif' : 'Inactif' }}
                                    </button>
                                </form>
                                
                                @if($menu->is_active)
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        <i class="fas fa-eye mr-1"></i>
                                        @if(($menu->location ?? 'header') === 'header')
                                            Visible dans la navigation
                                        @elseif($menu->location === 'footer')
                                            Visible dans le footer (colonne)
                                        @else
                                            Visible dans le footer (bas de page)
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('admin.menus.show', $menu) }}" 
                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-blue-600 hover:bg-blue-50 transition" 
                               title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.menus.edit', $menu) }}" 
                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-purple-600 hover:bg-purple-50 transition" 
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" 
                                  class="inline" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce menu ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-red-600 hover:bg-red-50 transition" 
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-list text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun menu créé</h3>
                <p class="text-gray-600 mb-6">Commencez par créer votre premier menu de navigation</p>
                <a href="{{ route('admin.menus.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un Menu
                </a>
            </div>
        @endforelse
    </div>

    @if($menus->count() > 0)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">Information</h4>
                    <p class="text-sm text-blue-700">
                        <strong>Header :</strong> le menu actif avec la position la plus basse remplace la navigation principale (sinon les catégories s'affichent par défaut).<br>
                        <strong>Footer (colonne) :</strong> chaque menu actif devient une colonne du pied de page (titre + liens), comme sur le frontend actuel.<br>
                        <strong>Footer (barre du bas) :</strong> les liens s'affichent sous le copyright (ex: CGU, mentions légales).
                    </p>
                </div>
            </div>
        </div>
    @endif
@endsection
