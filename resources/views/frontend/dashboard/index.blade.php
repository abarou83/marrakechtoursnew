@extends('layouts.app')

@section('title', __('Mon espace'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar --}}
            @include('frontend.dashboard._sidebar')

            {{-- Main Content --}}
            <div class="flex-1">
                {{-- Welcome --}}
                <div class="card p-6 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-2xl font-bold text-primary-600">{{ $client->initials }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-display font-bold text-sand-900">
                                {{ __('Bonjour, :name !', ['name' => explode(' ', $client->name)[0]]) }}
                            </h1>
                            <p class="text-sand-500">{{ __('Bienvenue dans votre espace personnel') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="card p-4 text-center">
                        <p class="text-3xl font-bold text-primary-600">{{ $stats['total_bookings'] }}</p>
                        <p class="text-sm text-sand-500">{{ __('Réservations') }}</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                        <p class="text-sm text-sand-500">{{ __('Voyages') }}</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-3xl font-bold text-accent-500">{{ $stats['reviews'] }}</p>
                        <p class="text-sm text-sand-500">{{ __('Avis') }}</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-3xl font-bold text-red-500">{{ $stats['wishlist'] }}</p>
                        <p class="text-sm text-sand-500">{{ __('Favoris') }}</p>
                    </div>
                </div>

                {{-- Upcoming Bookings --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-sand-900">{{ __('Prochaines excursions') }}</h2>
                        <a href="{{ route('dashboard.bookings') }}" class="text-sm text-primary-600 hover:underline">
                            {{ __('Voir tout') }}
                        </a>
                    </div>

                    @if($upcomingBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBookings as $booking)
                                <a href="{{ route('dashboard.bookings.show', $booking) }}" 
                                   class="flex items-center gap-4 p-4 rounded-lg border border-sand-200 hover:border-primary-300 hover:bg-primary-50 transition">
                                    @if($booking->tour->getFirstMediaUrl('images', 'thumb'))
                                        <img src="{{ $booking->tour->getFirstMediaUrl('images', 'thumb') }}" 
                                             alt="{{ $booking->tour->translate()?->title }}"
                                             class="w-20 h-20 rounded-lg object-cover">
                                    @else
                                        <div class="w-20 h-20 rounded-lg bg-sand-200 flex items-center justify-center">
                                            <x-heroicon-o-map class="w-8 h-8 text-sand-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-sand-900 truncate">
                                            {{ $booking->tour->translate()?->title }}
                                        </h3>
                                        <p class="text-sm text-sand-500">
                                            {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('l d F Y') }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1
                                            {{ $booking->status->value === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ ucfirst($booking->status->value ?? $booking->status) }}
                                        </span>
                                    </div>
                                    <x-heroicon-o-chevron-right class="w-5 h-5 text-sand-400" />
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-calendar class="w-12 h-12 mx-auto text-sand-300 mb-3" />
                            <p class="text-sand-500 mb-4">{{ __('Aucune excursion à venir') }}</p>
                            <a href="{{ route('tours.index') }}" class="btn-primary">
                                {{ __('Découvrir nos tours') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
