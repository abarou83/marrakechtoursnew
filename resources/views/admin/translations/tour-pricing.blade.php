@extends('admin.layout')

@section('title', 'Traductions du Pricing')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tour-pricings.index', $pricing->tour) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux pricings
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg max-w-4xl" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
        <div class="p-8 pb-0">
            <h2 class="text-2xl font-bold text-gray-900 mb-2"><i class="fas fa-language mr-2"></i>Traductions : {{ $pricing->title ?? 'Pricing #' . $pricing->id }}</h2>
            <p class="text-gray-600 mb-6">Gérez les traductions de ce pricing dans toutes les langues supportées</p>
        </div>

        @php
            use App\Helpers\LanguageHelper;
            $locales = LanguageHelper::getAvailableLocales();
        @endphp

        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 px-8">
            <div class="flex space-x-1">
                @foreach($availableLocales as $locale)
                    @php $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale]; @endphp
                    <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="inline-flex items-center px-4 py-3 border-b-2 font-semibold text-sm transition-colors duration-200 rounded-t-lg">
                        <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-2" style="font-size: 1.25rem;"></span>
                        <span>{{ $localeInfo['native'] }}</span>
                        <span class="ml-2 text-xs opacity-75">({{ strtoupper($locale) }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('admin.tour-pricings.translations.update', $pricing) }}">
            @csrf

            {{-- Tabs Content --}}
            <div class="p-8">
                @foreach($availableLocales as $locale)
                    @php
                        $translation = $pricing->translations->where('locale', $locale)->first();
                        $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                    @endphp

                    <div x-show="activeTab === '{{ $locale }}'" x-transition>
                        <div class="flex items-center mb-6">
                            <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis mr-3" style="font-size: 1.875rem;"></span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $localeInfo['native'] }}</h3>
                                <p class="text-sm text-gray-500">Traduction en {{ strtolower($localeInfo['native']) }}</p>
                            </div>
                        </div>

                        <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Titre du pricing <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="translations[{{ $loop->index }}][title]" 
                                   value="{{ old("translations.{$loop->index}.title", $translation->title ?? $pricing->title) }}"
                                   class="w-full border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                   required
                                   placeholder="Titre en {{ strtolower($localeInfo['native']) }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-8 pb-8 flex space-x-4">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer toutes les traductions
                </button>
                <a href="{{ route('admin.tour-pricings.index', $pricing->tour) }}" 
                   class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
