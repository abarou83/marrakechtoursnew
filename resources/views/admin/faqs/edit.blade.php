@extends('admin.layout')

@section('title', 'Modifier la FAQ')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.faqs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Retour aux FAQs
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-edit mr-2"></i>Modifier la FAQ</h2>

        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Configuration générale -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Configuration</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Ordre d'affichage
                            </label>
                            <input type="number" 
                                   name="order" 
                                   value="{{ old('order', $faq->order) }}"
                                   min="0"
                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                            <p class="text-xs text-gray-500 mt-1">Plus le nombre est petit, plus la FAQ apparaît en premier</p>
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center mt-4">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active"
                               value="1"
                               {{ old('is_active', $faq->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                            Activer cette FAQ
                        </label>
                    </div>
                </div>

                <!-- Traductions -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Traductions</h3>
                    
                    @foreach($availableLocales as $locale)
                        @php
                            $translation = $faq->translations->where('locale', $locale)->first();
                        @endphp
                        <div class="mb-6 pb-6 border-b border-gray-100 last:border-0 last:pb-0 last:mb-0">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">{{ strtoupper($locale) }}</h4>
                            
                            <div class="space-y-4">
                                <!-- Question -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Question <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="translations[{{ $loop->index }}][question]" 
                                           value="{{ old("translations.{$loop->index}.question", $translation?->question ?? '') }}"
                                           class="w-full border-gray-300 rounded-lg px-4 py-3"
                                           required>
                                    @error("translations.{$loop->index}.question")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Answer -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Réponse <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="translations[{{ $loop->index }}][answer]" 
                                              rows="4"
                                              class="w-full border-gray-300 rounded-lg px-4 py-3"
                                              required>{{ old("translations.{$loop->index}.answer", $translation?->answer ?? '') }}</textarea>
                                    @error("translations.{$loop->index}.answer")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.faqs.index') }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection


