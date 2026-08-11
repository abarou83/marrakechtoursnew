@extends('admin.layout')

@section('title', 'Modifier la Catégorie')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
            ← Retour aux catégories
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Catégorie parente (optionnel)
                </label>
                <select name="parent_id" 
                        class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Aucune (catégorie principale)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Sélectionnez une catégorie parente pour créer une sous-catégorie</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Nom de la catégorie <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" 
                       class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Slug actuel: <span class="font-mono">{{ $category->slug }}</span></p>
            </div>

            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">🔍 SEO</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Meta Title
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('meta_title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Meta Description
                    </label>
                    <textarea name="meta_description" rows="3" 
                              class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('meta_description', $category->meta_description) }}</textarea>
                    @error('meta_description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection



