@extends('layouts.app')

@section('title', $seo['title'] ?? __('Réserver'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        {{-- Breadcrumb --}}
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center text-sm text-sand-500 gap-2">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-primary-500">{{ __('Accueil') }}</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li>
                    <a href="{{ route('tours.index') }}" class="hover:text-primary-500">{{ __('Tours') }}</a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li>
                    <a href="{{ route('tours.show', $tour->slug) }}" class="hover:text-primary-500">
                        {{ Str::limit($tour->translate()?->title ?? $tour->title, 30) }}
                    </a>
                </li>
                <li><span class="mx-1">/</span></li>
                <li class="text-sand-800 font-medium">{{ __('Réservation') }}</li>
            </ol>
        </nav>

        {{-- Tour Summary --}}
        <div class="card p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                @if($tour->getFirstMediaUrl('images', 'thumb'))
                    <img
                        src="{{ $tour->getFirstMediaUrl('images', 'thumb') }}"
                        alt="{{ $tour->translate()?->title ?? $tour->title }}"
                        class="w-24 h-24 object-cover rounded-base"
                    />
                @endif
                <div class="flex-1">
                    <h1 class="text-xl font-display font-bold text-sand-900">
                        {{ $tour->translate()?->title ?? $tour->title }}
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-sand-500">
                        @if($tour->duration_formatted)
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-clock class="w-4 h-4" />
                                {{ $tour->duration_formatted }}
                            </span>
                        @endif
                        @if($tour->avg_rating)
                            <span class="flex items-center gap-1">
                                <x-heroicon-s-star class="w-4 h-4 text-accent-500" />
                                {{ number_format($tour->avg_rating, 1) }} ({{ $tour->reviews_count }} {{ __('avis') }})
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-sand-500">{{ __('À partir de') }}</p>
                    <p class="text-2xl font-bold text-primary-500">
                        {{ number_format($tour->getMinPrice(), 0) }} €
                    </p>
                </div>
            </div>
        </div>

        {{-- Booking Wizard --}}
        @livewire('booking-wizard', ['tour' => $tour])
    </div>
</div>
@endsection
