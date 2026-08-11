@extends('layouts.app')

@section('title', __('Politique de confidentialité'))
@section('meta_description', __('Politique de confidentialité et protection des données personnelles de MarrakechTours conformément au RGPD.'))

@section('content')
<div class="bg-sand-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="card p-8 md:p-12 prose prose-sand max-w-none">
            <h1>{{ __('Politique de confidentialité') }}</h1>
            <p class="lead text-sand-600">
                {{ __('Dernière mise à jour : :date', ['date' => now()->translatedFormat('d F Y')]) }}
            </p>

            <h2>{{ __('1. Responsable du traitement') }}</h2>
            <p>
                <strong>MarrakechTours</strong><br>
                {{ __('Email') }} : contact@marrakechtours.net<br>
                {{ __('DPO') }} : {{ config('gdpr.dpo.email') }}
            </p>

            <h2>{{ __('2. Données collectées') }}</h2>
            <ul>
                <li>{{ __('Données d\'identification : nom, email, téléphone') }}</li>
                <li>{{ __('Données de réservation : dates, participants, préférences') }}</li>
                <li>{{ __('Données de paiement : traitées par Stripe (non stockées sur nos serveurs)') }}</li>
                <li>{{ __('Données de navigation : cookies, adresse IP (anonymisée)') }}</li>
            </ul>

            <h2>{{ __('3. Finalités du traitement') }}</h2>
            @foreach(config('gdpr.purposes') as $purpose)
                <h3>{{ $purpose['name'] }}</h3>
                <p>
                    {{ __('Base légale') }} : {{ $purpose['legal_basis'] }}<br>
                    {{ __('Conservation') }} : {{ $purpose['retention'] }}
                </p>
            @endforeach

            <h2>{{ __('4. Cookies') }}</h2>
            <p>{{ __('Nous utilisons les catégories de cookies suivantes :') }}</p>
            @foreach(config('gdpr.cookies') as $key => $category)
                <h3>{{ $category['name'] }}</h3>
                <p>{{ $category['description'] }}</p>
                @if(!empty($category['items']))
                    <ul>
                        @foreach($category['items'] as $cookie => $description)
                            <li><strong>{{ $cookie }}</strong> — {{ $description }}</li>
                        @endforeach
                    </ul>
                @endif
            @endforeach

            <h2>{{ __('5. Vos droits') }}</h2>
            <p>{{ __('Conformément au RGPD, vous disposez des droits suivants :') }}</p>
            <ul>
                <li>{{ __('Droit d\'accès et de portabilité') }}</li>
                <li>{{ __('Droit de rectification') }}</li>
                <li>{{ __('Droit à l\'effacement') }}</li>
                <li>{{ __('Droit d\'opposition et de limitation') }}</li>
                <li>{{ __('Droit de retirer votre consentement') }}</li>
            </ul>

            @auth('client')
                <div class="not-prose bg-primary-50 border border-primary-200 rounded-lg p-6 my-8">
                    <h3 class="font-semibold text-primary-800 mb-3">{{ __('Exercer vos droits') }}</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('client.gdpr.export.request') }}" class="btn-primary">
                            {{ __('Télécharger mes données') }}
                        </a>
                        <a href="{{ route('client.gdpr.delete.request') }}" class="btn-outline text-red-600 border-red-300">
                            {{ __('Supprimer mon compte') }}
                        </a>
                    </div>
                </div>
            @else
                <p>
                    {{ __('Connectez-vous à votre compte pour exporter ou supprimer vos données, ou contactez-nous à') }}
                    <a href="mailto:{{ config('gdpr.dpo.email') }}">{{ config('gdpr.dpo.email') }}</a>.
                </p>
            @endauth

            <h2>{{ __('6. Réclamation') }}</h2>
            <p>
                {{ __('Vous pouvez introduire une réclamation auprès de l\'autorité de contrôle :') }}
                <a href="{{ config('gdpr.supervisory_authority.url') }}" target="_blank" rel="noopener">
                    {{ config('gdpr.supervisory_authority.name') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
