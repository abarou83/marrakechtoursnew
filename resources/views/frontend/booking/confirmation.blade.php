@extends('layouts.app')

@section('title', __('Confirmation de réservation'))

@section('content')
<div class="bg-sand-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="card p-8 text-center">
            {{-- Success Icon --}}
            <div class="w-20 h-20 mx-auto bg-success-100 rounded-full flex items-center justify-center mb-6">
                <x-heroicon-s-check class="w-10 h-10 text-success-500" />
            </div>

            <h1 class="text-3xl font-display font-bold text-sand-900 mb-2">
                {{ __('Réservation confirmée !') }}
            </h1>
            <p class="text-sand-600 mb-8">
                {{ __('Merci pour votre réservation. Un email de confirmation a été envoyé à :email.', ['email' => $booking->customer_email]) }}
            </p>

            {{-- Booking Reference --}}
            <div class="bg-sand-100 rounded-base p-6 mb-8 inline-block">
                <p class="text-sm text-sand-500 mb-1">{{ __('Votre référence de réservation') }}</p>
                <p class="text-3xl font-bold text-primary-500 font-mono tracking-wider">
                    {{ $booking->reference }}
                </p>
            </div>

            {{-- Booking Details --}}
            <div class="bg-white border border-sand-200 rounded-base p-6 text-left mb-8">
                <h2 class="font-semibold text-sand-900 mb-4 pb-2 border-b border-sand-100">
                    {{ __('Détails de votre réservation') }}
                </h2>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sand-600">{{ __('Tour') }}</span>
                        <span class="font-medium">{{ $booking->tour->translate()?->title ?? $booking->tour->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sand-600">{{ __('Date') }}</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('l d F Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sand-600">{{ __('Participants') }}</span>
                        <span class="font-medium">
                            {{ $booking->adults ?? 1 }} {{ __('adulte(s)') }}
                            @if(($booking->children ?? 0) > 0), {{ $booking->children }} {{ __('enfant(s)') }}@endif
                            @if(($booking->infants ?? 0) > 0), {{ $booking->infants }} {{ __('bébé(s)') }}@endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sand-600">{{ __('Nom') }}</span>
                        <span class="font-medium">{{ $booking->customer_name }}</span>
                    </div>
                    <hr class="border-sand-100 my-2" />
                    <div class="flex justify-between text-lg">
                        <span class="font-semibold">{{ __('Total payé') }}</span>
                        <span class="font-bold text-primary-500">
                            {{ number_format($booking->total_ttc ?? $booking->total_price, 2) }} €
                        </span>
                    </div>
                </div>
            </div>

            {{-- Important Info --}}
            <div class="bg-primary-50 border border-primary-200 rounded-base p-4 text-left mb-8">
                <h3 class="font-semibold text-primary-700 flex items-center gap-2 mb-2">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                    {{ __('Informations importantes') }}
                </h3>
                <ul class="text-sm text-primary-600 space-y-1 list-disc list-inside">
                    <li>{{ __('Présentez le voucher (imprimé ou sur mobile) le jour du tour') }}</li>
                    <li>{{ __('Soyez au point de départ 15 minutes avant l\'heure indiquée') }}</li>
                    <li>{{ __('Annulation gratuite jusqu\'à 24h avant le départ') }}</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="{{ route('booking.voucher', $booking->reference) }}"
                    class="btn-primary"
                >
                    <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                    {{ __('Télécharger le voucher') }}
                </a>
                <a
                    href="{{ route('home') }}"
                    class="btn-outline"
                >
                    {{ __('Retour à l\'accueil') }}
                </a>
            </div>
        </div>

        {{-- Need Help --}}
        <div class="text-center mt-8">
            <p class="text-sand-500">
                {{ __('Besoin d\'aide ?') }}
                <a href="{{ route('contact') }}" class="text-primary-500 hover:underline">
                    {{ __('Contactez-nous') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
