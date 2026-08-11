@extends('admin.layout')

@section('title', 'Modifier une Promotion')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <a href="{{ route('admin.tour-promotions.index', $tour) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                        ← Retour aux promotions
                    </a>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2"><i class="fas fa-edit mr-2"></i>Modifier : {{ $promotion->name }}</h3>
                </div>

                <form method="POST" action="{{ route('admin.tour-promotions.update', [$tour, $promotion]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nom -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                            Nom de la promotion <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $promotion->name) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="ex: Early Bird Discount, Summer Sale" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  placeholder="Détails de l'offre...">{{ old('description', $promotion->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de réduction et Valeur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="discount_type" class="block text-sm font-bold text-gray-700 mb-2">
                                Type de réduction <span class="text-red-500">*</span>
                            </label>
                            <select name="discount_type" id="discount_type" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Montant fixe (EUR)</option>
                            </select>
                            @error('discount_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_value" class="block text-sm font-bold text-gray-700 mb-2">
                                Valeur de la réduction <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="discount_value" id="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   placeholder="20 pour 20% ou 10.00 pour 10€" required>
                            <p class="text-xs text-gray-500 mt-1">Ex: 20 pour -20% ou 10.00 pour -10 EUR</p>
                            @error('discount_value')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">
                                Date de début <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">
                                Date de fin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Utilisation et Limite -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="usage_limit" class="block text-sm font-bold text-gray-700 mb-2">
                                Limite d'utilisation (optionnel)
                            </label>
                            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   min="1" placeholder="Ex: 50 réservations maximum">
                            <p class="text-xs text-gray-500 mt-1">Laisser vide pour "illimité"</p>
                            @error('usage_limit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Utilisations actuelles
                            </label>
                            <div class="px-4 py-3 bg-gray-100 rounded-lg">
                                <span class="text-2xl font-bold text-indigo-600">{{ $promotion->used_count }}</span>
                                <span class="text-sm text-gray-600">
                                    / {{ $promotion->usage_limit ?? '∞' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="badge_text" class="block text-sm font-bold text-gray-700 mb-2">
                                Texte du badge
                            </label>
                            <input type="text" name="badge_text" id="badge_text" value="{{ old('badge_text', $promotion->badge_text) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   placeholder="ex: -20%, PROMO, HOT DEAL" maxlength="50">
                            @error('badge_text')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="badge_color" class="block text-sm font-bold text-gray-700 mb-2">
                                Couleur du badge
                            </label>
                            <select name="badge_color" id="badge_color" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Aucune</option>
                                <option value="red" {{ old('badge_color', $promotion->badge_color) == 'red' ? 'selected' : '' }}>🔴 Rouge</option>
                                <option value="orange" {{ old('badge_color', $promotion->badge_color) == 'orange' ? 'selected' : '' }}>🟠 Orange</option>
                                <option value="yellow" {{ old('badge_color', $promotion->badge_color) == 'yellow' ? 'selected' : '' }}>🟡 Jaune</option>
                                <option value="green" {{ old('badge_color', $promotion->badge_color) == 'green' ? 'selected' : '' }}>🟢 Vert</option>
                                <option value="blue" {{ old('badge_color', $promotion->badge_color) == 'blue' ? 'selected' : '' }}>🔵 Bleu</option>
                                <option value="purple" {{ old('badge_color', $promotion->badge_color) == 'purple' ? 'selected' : '' }}>🟣 Violet</option>
                                <option value="pink" {{ old('badge_color', $promotion->badge_color) == 'pink' ? 'selected' : '' }}>💗 Rose</option>
                            </select>
                            @error('badge_color')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Active -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                            <label for="is_active" class="ml-2 text-sm font-bold text-gray-700">
                                ✓ Promotion active
                            </label>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.tour-promotions.index', $tour) }}" 
                           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-lg">
                            <i class="fas fa-save mr-2"></i>Mettre à jour la promotion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

