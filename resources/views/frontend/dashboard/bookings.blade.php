@extends('layouts.app')

@section('title', __('Mes réservations'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.dashboard._sidebar')

            <div class="flex-1">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-xl font-display font-bold text-sand-900">{{ __('Mes réservations') }}</h1>
                        
                        <form method="GET" class="flex gap-2">
                            <select name="status" onchange="this.form.submit()" class="input text-sm py-2">
                                <option value="">{{ __('Tous les statuts') }}</option>
                                <option value="pending" @selected(request('status') === 'pending')>{{ __('En attente') }}</option>
                                <option value="confirmed" @selected(request('status') === 'confirmed')>{{ __('Confirmée') }}</option>
                                <option value="completed" @selected(request('status') === 'completed')>{{ __('Terminée') }}</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>{{ __('Annulée') }}</option>
                            </select>
                        </form>
                    </div>

                    @if($bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($bookings as $booking)
                                <a href="{{ route('dashboard.bookings.show', $booking) }}" 
                                   class="block p-4 rounded-lg border border-sand-200 hover:border-primary-300 hover:bg-primary-50 transition">
                                    <div class="flex items-start gap-4">
                                        @if($booking->tour->getFirstMediaUrl('images', 'thumb'))
                                            <img src="{{ $booking->tour->getFirstMediaUrl('images', 'thumb') }}" 
                                                 alt="{{ $booking->tour->translate()?->title }}"
                                                 class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h3 class="font-semibold text-sand-900">
                                                        {{ $booking->tour->translate()?->title }}
                                                    </h3>
                                                    <p class="text-sm text-sand-500 mt-1">
                                                        <x-heroicon-o-calendar class="w-4 h-4 inline" />
                                                        {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('d F Y') }}
                                                    </p>
                                                    <p class="text-sm text-sand-500">
                                                        <x-heroicon-o-users class="w-4 h-4 inline" />
                                                        {{ $booking->adults ?? 1 }} {{ __('adulte(s)') }}
                                                        @if(($booking->children ?? 0) > 0), {{ $booking->children }} {{ __('enfant(s)') }}@endif
                                                    </p>
                                                </div>
                                                <div class="text-right flex-shrink-0">
                                                    @php
                                                        $statusValue = $booking->status->value ?? $booking->status;
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                                            'confirmed' => 'bg-green-100 text-green-700',
                                                            'completed' => 'bg-blue-100 text-blue-700',
                                                            'cancelled' => 'bg-red-100 text-red-700',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-700' }}">
                                                        {{ ucfirst($statusValue) }}
                                                    </span>
                                                    <p class="text-lg font-bold text-primary-600 mt-2">
                                                        {{ number_format($booking->total_ttc ?? $booking->total_price, 2) }}€
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-sand-100">
                                                <span class="text-xs text-sand-400">
                                                    {{ __('Réf.') }} {{ $booking->reference }}
                                                </span>
                                                <span class="text-sm text-primary-600 font-medium">
                                                    {{ __('Voir détails') }} →
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if($bookings->hasPages())
                            <div class="mt-6">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <x-heroicon-o-ticket class="w-16 h-16 mx-auto text-sand-300 mb-4" />
                            <h3 class="text-lg font-semibold text-sand-700 mb-2">{{ __('Aucune réservation') }}</h3>
                            <p class="text-sand-500 mb-4">{{ __('Vous n\'avez pas encore de réservation.') }}</p>
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
