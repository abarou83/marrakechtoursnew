@extends('layouts.app')

@section('title', __('Exporter mes données'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        @include('frontend.dashboard._sidebar')

        <main class="flex-1">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-2xl font-bold text-sand-900 mb-2">{{ __('Exporter mes données') }}</h1>
                <p class="text-sand-600 mb-8">
                    {{ __('Conformément au RGPD, vous pouvez télécharger une copie de toutes vos données personnelles.') }}
                </p>

                <div class="card p-6 mb-6">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-document-arrow-down class="w-6 h-6 text-primary-600" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-sand-900">{{ __('Contenu de l\'export') }}</h2>
                            <p class="text-sm text-sand-600 mt-1">
                                {{ __('Le fichier téléchargé contiendra toutes vos informations :') }}
                            </p>
                        </div>
                    </div>

                    <ul class="space-y-3 mb-6">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                            <span class="text-sand-700">{{ __('Informations personnelles (nom, email, téléphone...)') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                            <span class="text-sand-700">{{ __('Historique des réservations') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                            <span class="text-sand-700">{{ __('Avis laissés') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                            <span class="text-sand-700">{{ __('Liste de favoris') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                            <span class="text-sand-700">{{ __('Historique des consentements') }}</span>
                        </li>
                    </ul>

                    <form action="{{ route('client.gdpr.export') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                            {{ __('Télécharger mes données (JSON)') }}
                        </button>
                    </form>
                </div>

                <div class="bg-sand-50 rounded-lg p-4 text-sm text-sand-600">
                    <p class="flex items-start gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-sand-500 flex-shrink-0 mt-0.5" />
                        <span>
                            {{ __('Cette fonctionnalité est limitée à 3 téléchargements par jour. Le fichier est au format JSON, lisible par tout éditeur de texte.') }}
                        </span>
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
