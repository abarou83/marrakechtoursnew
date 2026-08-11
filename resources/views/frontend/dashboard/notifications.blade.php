@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.dashboard._sidebar')

            <div class="flex-1">
                <div class="card p-6">
                    <h1 class="text-xl font-display font-bold text-sand-900 mb-2">{{ __('Préférences de notification') }}</h1>
                    <p class="text-sand-500 mb-6">{{ __('Choisissez les emails que vous souhaitez recevoir.') }}</p>

                    <form method="POST" action="{{ route('dashboard.notifications.update') }}">
                        @csrf
                        @method('PUT')

                        @php
                            $prefs = $client->notification_preferences ?? $client->default_notification_preferences;
                        @endphp

                        <div class="space-y-6">
                            {{-- Transactional --}}
                            <div>
                                <h3 class="font-semibold text-sand-900 mb-3">{{ __('Emails transactionnels') }}</h3>
                                <div class="space-y-4 pl-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="email_booking_confirmation" value="1" 
                                               @checked($prefs['email_booking_confirmation'] ?? true)
                                               class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500">
                                        <div>
                                            <span class="font-medium text-sand-800">{{ __('Confirmation de réservation') }}</span>
                                            <p class="text-sm text-sand-500">{{ __('Recevez un email de confirmation après chaque réservation.') }}</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="email_booking_reminder" value="1" 
                                               @checked($prefs['email_booking_reminder'] ?? true)
                                               class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500">
                                        <div>
                                            <span class="font-medium text-sand-800">{{ __('Rappels de voyage') }}</span>
                                            <p class="text-sm text-sand-500">{{ __('Recevez un rappel 24h avant votre excursion.') }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <hr class="border-sand-200" />

                            {{-- Marketing --}}
                            <div>
                                <h3 class="font-semibold text-sand-900 mb-3">{{ __('Marketing & actualités') }}</h3>
                                <div class="space-y-4 pl-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="email_promotions" value="1" 
                                               @checked($prefs['email_promotions'] ?? false)
                                               class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500">
                                        <div>
                                            <span class="font-medium text-sand-800">{{ __('Offres spéciales') }}</span>
                                            <p class="text-sm text-sand-500">{{ __('Recevez nos meilleures offres et promotions exclusives.') }}</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="email_newsletter" value="1" 
                                               @checked($prefs['email_newsletter'] ?? false)
                                               class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500">
                                        <div>
                                            <span class="font-medium text-sand-800">{{ __('Newsletter') }}</span>
                                            <p class="text-sm text-sand-500">{{ __('Recevez nos conseils de voyage et inspirations mensuelles.') }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-sand-200">
                            <button type="submit" class="btn-primary">
                                {{ __('Enregistrer les préférences') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- GDPR Info --}}
                <div class="card p-6 mt-6 bg-sand-100">
                    <h3 class="font-semibold text-sand-900 mb-2 flex items-center gap-2">
                        <x-heroicon-o-shield-check class="w-5 h-5 text-primary-500" />
                        {{ __('Protection de vos données') }}
                    </h3>
                    <p class="text-sm text-sand-600">
                        {{ __('Conformément au RGPD, vous pouvez à tout moment modifier vos préférences ou demander la suppression de vos données.') }}
                        <a href="{{ route('contact') }}" class="text-primary-600 hover:underline">{{ __('Contactez-nous') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
