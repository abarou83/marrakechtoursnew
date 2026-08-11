@extends('layouts.app')

@section('title', $seo['title'] ?? $translation?->title)
@section('meta_description', $seo['description'] ?? '')

@push('head')
    {{-- Hreflang --}}
    @foreach($hreflang as $tag)
        <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
    @endforeach

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- JSON-LD --}}
    @foreach($jsonLd as $schema)
        <script type="application/ld+json">
            {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endforeach
@endpush

@section('content')
{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white py-16 lg:py-24">
    @if($translation?->hero_image)
        <div class="absolute inset-0 opacity-30">
            <img src="{{ $translation->hero_image }}" alt="{{ $translation?->title }}" 
                 class="w-full h-full object-cover">
        </div>
    @endif
    <div class="container mx-auto px-4 relative z-10">
        {{-- Breadcrumbs --}}
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center text-sm text-white/70 gap-2 flex-wrap">
                @foreach($breadcrumbs as $crumb)
                    @if(!$loop->last)
                        <li>
                            <a href="{{ $crumb['url'] }}" class="hover:text-white">{{ $crumb['title'] }}</a>
                        </li>
                        <li><span class="mx-1">/</span></li>
                    @else
                        <li class="text-white font-medium">{{ $crumb['title'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-display font-bold mb-4">
            {{ $translation?->title }}
        </h1>

        @if($translation?->subtitle)
            <p class="text-xl text-white/90 mb-6 max-w-2xl">
                {{ $translation->subtitle }}
            </p>
        @endif

        <div class="flex items-center gap-6 text-sm">
            <span class="flex items-center gap-2">
                <x-heroicon-o-map-pin class="w-5 h-5" />
                {{ $tours->total() }} {{ __('excursions disponibles') }}
            </span>
            @if($landingPage->destination)
                <span class="flex items-center gap-2">
                    <x-heroicon-o-globe-alt class="w-5 h-5" />
                    {{ $landingPage->destination->translate()?->name }}
                </span>
            @endif
        </div>
    </div>
</section>

{{-- Intro Section --}}
@if($translation?->intro)
<section class="py-12 bg-sand-50">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <div class="prose prose-lg prose-sand mx-auto">
                {!! nl2br(e($translation->intro)) !!}
            </div>
        </div>
    </div>
</section>
@endif

{{-- Tours Grid --}}
<section class="py-12 lg:py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filters --}}
            <aside class="lg:w-64 flex-shrink-0">
                <div class="card p-4 sticky top-24">
                    <h3 class="font-semibold text-sand-900 mb-4">{{ __('Filtrer') }}</h3>
                    
                    <form method="GET" class="space-y-4">
                        {{-- Price Range --}}
                        <div>
                            <label class="label">{{ __('Budget max') }}</label>
                            <select name="price_max" class="input text-sm" onchange="this.form.submit()">
                                <option value="">{{ __('Tous les prix') }}</option>
                                <option value="50" @selected(request('price_max') == 50)>≤ 50€</option>
                                <option value="100" @selected(request('price_max') == 100)>≤ 100€</option>
                                <option value="200" @selected(request('price_max') == 200)>≤ 200€</option>
                                <option value="500" @selected(request('price_max') == 500)>≤ 500€</option>
                            </select>
                        </div>

                        {{-- Duration --}}
                        <div>
                            <label class="label">{{ __('Durée') }}</label>
                            <select name="duration" class="input text-sm" onchange="this.form.submit()">
                                <option value="">{{ __('Toutes durées') }}</option>
                                <option value="half" @selected(request('duration') == 'half')>{{ __('Demi-journée') }}</option>
                                <option value="full" @selected(request('duration') == 'full')>{{ __('Journée') }}</option>
                                <option value="multi" @selected(request('duration') == 'multi')>{{ __('Plusieurs jours') }}</option>
                            </select>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <label class="label">{{ __('Trier par') }}</label>
                            <select name="sort" class="input text-sm" onchange="this.form.submit()">
                                <option value="popular" @selected(request('sort', 'popular') == 'popular')>{{ __('Populaires') }}</option>
                                <option value="price_asc" @selected(request('sort') == 'price_asc')>{{ __('Prix croissant') }}</option>
                                <option value="price_desc" @selected(request('sort') == 'price_desc')>{{ __('Prix décroissant') }}</option>
                                <option value="rating" @selected(request('sort') == 'rating')>{{ __('Mieux notés') }}</option>
                            </select>
                        </div>

                        @if(request()->hasAny(['price_max', 'duration', 'sort']))
                            <a href="{{ url()->current() }}" class="text-sm text-primary-600 hover:underline">
                                {{ __('Réinitialiser les filtres') }}
                            </a>
                        @endif
                    </form>
                </div>
            </aside>

            {{-- Tours List --}}
            <div class="flex-1">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sand-600">
                        {{ $tours->total() }} {{ __('résultats') }}
                    </p>
                </div>

                @if($tours->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($tours as $tour)
                            <x-tour-card :tour="$tour" />
                        @endforeach
                    </div>

                    @if($tours->hasPages())
                        <div class="mt-8">
                            {{ $tours->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <x-heroicon-o-map class="w-16 h-16 mx-auto text-sand-300 mb-4" />
                        <h3 class="text-lg font-semibold text-sand-700 mb-2">{{ __('Aucune excursion trouvée') }}</h3>
                        <p class="text-sand-500">{{ __('Essayez de modifier vos filtres') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
@if($translation?->content)
<section class="py-12 bg-white border-t border-sand-100">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto prose prose-sand prose-lg">
            {!! $translation->content !!}
        </div>
    </div>
</section>
@endif

{{-- FAQ Section --}}
@if(!empty($faqs))
<section class="py-12 lg:py-16 bg-sand-50">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl lg:text-3xl font-display font-bold text-sand-900 text-center mb-8">
                {{ __('Questions fréquentes') }}
            </h2>
            
            <div class="space-y-4" x-data="{ openFaq: null }">
                @foreach($faqs as $index => $faq)
                    <div class="card overflow-hidden">
                        <button
                            @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}"
                            class="w-full flex items-center justify-between p-4 text-left font-semibold text-sand-900 hover:bg-sand-50"
                        >
                            <span>{{ $faq['question'] }}</span>
                            <x-heroicon-o-chevron-down 
                                class="w-5 h-5 transition-transform"
                                x-bind:class="openFaq === {{ $index }} ? 'rotate-180' : ''"
                            />
                        </button>
                        <div
                            x-show="openFaq === {{ $index }}"
                            x-collapse
                            class="px-4 pb-4 text-sand-600"
                        >
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Related Pages --}}
@if($relatedPages->count() > 0)
<section class="py-12 bg-white border-t border-sand-100">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-display font-bold text-sand-900 mb-6">
            {{ __('Découvrez aussi') }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($relatedPages as $page)
                @php $pageTranslation = $page->translate(); @endphp
                <a href="{{ route('landing.show', $pageTranslation?->slug ?? $page->id) }}" 
                   class="card p-4 hover:shadow-lg transition-shadow text-center">
                    <h3 class="font-semibold text-sand-900">{{ $pageTranslation?->title }}</h3>
                    <p class="text-sm text-sand-500 mt-1">{{ $page->tours_count }} {{ __('tours') }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-12 lg:py-16 bg-primary-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl lg:text-3xl font-display font-bold mb-4">
            {{ __('Besoin d\'aide pour choisir ?') }}
        </h2>
        <p class="text-white/80 mb-6 max-w-xl mx-auto">
            {{ __('Notre équipe est disponible pour vous conseiller et créer un voyage sur mesure.') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="btn bg-white text-primary-600 hover:bg-sand-100">
                {{ __('Contactez-nous') }}
            </a>
            <a href="https://wa.me/212600000000" target="_blank" class="btn-outline border-white text-white hover:bg-white/10">
                <i class="fab fa-whatsapp mr-2"></i>
                WhatsApp
            </a>
        </div>
    </div>
</section>
@endsection
