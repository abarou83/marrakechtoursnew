<x-layouts.app>
    @php
        $locale = app()->getLocale();
    @endphp

    <x-slot:title>{{ $destination->meta_title ?: $destination->name }} | Marrakech Tours</x-slot:title>
    <x-slot:description>{{ $destination->meta_description ?: $destination->description }}</x-slot:description>

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-secondary-900 text-white py-16 lg:py-24">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-white/70">
                    <li><a href="{{ route('home', $locale) }}" class="hover:text-white">{{ __('Accueil') }}</a></li>
                    <li>/</li>
                    <li><a href="{{ route('destinations.index', $locale) }}" class="hover:text-white">{{ __('Destinations') }}</a></li>
                    <li>/</li>
                    <li class="text-white">{{ $destination->name }}</li>
                </ol>
            </nav>

            <h1 class="text-4xl lg:text-5xl font-bold font-display mb-4">
                {{ $destination->name }}
            </h1>

            <p class="text-xl text-white/90 max-w-3xl mb-6">
                {{ $destination->intro_text ?: $destination->description }}
            </p>

            <div class="flex items-center gap-4 text-white/80">
                <span class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    {{ $toursCount }} {{ __('tours disponibles') }}
                </span>
            </div>
        </div>
    </section>

    {{-- Tours Grid --}}
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold font-display text-gray-900">
                    {{ __('Tours et excursions') }}
                </h2>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">
                        {{ $tours->total() }} {{ __('résultats') }}
                    </span>
                </div>
            </div>

            @if($tours->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($tours as $tour)
                        <x-tour-card :tour="$tour" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $tours->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-gray-500">{{ __('Aucun tour disponible pour cette destination.') }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Description --}}
    @if($destination->description)
        <section class="py-12 bg-sand-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="prose prose-gray max-w-none">
                    {!! nl2br(e($destination->description)) !!}
                </div>
            </div>
        </section>
    @endif

    {{-- Why Book Direct --}}
    <x-why-book-direct variant="compact" />

    {{-- Recently Viewed --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-recently-viewed />
    </div>
</x-layouts.app>
