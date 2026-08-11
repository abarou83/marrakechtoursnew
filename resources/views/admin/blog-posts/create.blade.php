@extends('admin.layout')

@section('title', 'Nouvel article')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.blog-posts.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">← Retour au blog</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-plus-circle mr-2"></i>Nouvel article</h2>

        <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Date de publication</label>
                            <input type="datetime-local" name="published_at"
                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Auteur</label>
                            <input type="text" name="author" value="{{ old('author') }}"
                                   placeholder="Nom de l'auteur"
                                   class="w-full border-gray-300 rounded-lg px-4 py-3">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Image à la une</label>
                        <input type="file" name="featured_image" accept="image/*"
                               class="w-full border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">Publier immédiatement</label>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-language mr-2"></i>Contenu multilingue</h3>
                        <div class="flex flex-wrap gap-1 border-b border-gray-200 -mx-6 px-6 pb-0">
                            @foreach($availableLocales as $locale)
                                @php $localeInfo = $locales[$locale] ?? ['native' => $locale]; @endphp
                                <button type="button" @click="activeTab = '{{ $locale }}'"
                                        :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'"
                                        class="inline-flex items-center px-4 py-2 border-b-2 font-semibold text-sm">
                                    <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-2"></span>
                                    {{ $localeInfo['native'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-6">
                        @foreach($availableLocales as $locale)
                            @php
                                $translationIndex = $loop->index;
                                $translation = null;
                            @endphp
                            <div x-show="activeTab === '{{ $locale }}'" x-transition class="space-y-6">
                                @include('admin.blog-posts.partials.translation-fields', compact('locale', 'translationIndex', 'translation'))
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
                <a href="{{ route('admin.blog-posts.index') }}" class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">Annuler</a>
            </div>
        </form>
    </div>
@endsection

@include('admin.blog-posts.partials.ckeditor-scripts')
