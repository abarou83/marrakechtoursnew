@extends('layouts.app')

@section('title', __('Parrainage'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        @include('frontend.dashboard._sidebar')

        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Programme de parrainage') }}</h1>

            <div class="card p-6 mb-6">
                <p class="text-gray-600 mb-4">
                    {{ __('Partagez votre code : vos amis obtiennent :percent% sur leur première réservation, et vous recevez :reward € de réduction.', [
                        'percent' => config('marketing.referral.referred_discount_percent', 10),
                        'reward' => config('marketing.referral.referrer_reward', 10),
                    ]) }}
                </p>
                <div class="bg-primary-50 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">{{ __('Votre code') }}</p>
                        <code class="text-2xl font-bold text-primary-700">{{ $stats['code'] }}</code>
                    </div>
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ $stats['share_url'] }}')"
                            class="btn-primary text-sm">
                        {{ __('Copier le lien') }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card p-4 text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_referrals'] }}</p>
                    <p class="text-sm text-gray-500">{{ __('Parrainages') }}</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending_rewards'], 0) }} €</p>
                    <p class="text-sm text-gray-500">{{ __('Récompenses en attente') }}</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['earned_rewards'], 0) }} €</p>
                    <p class="text-sm text-gray-500">{{ __('Récompenses gagnées') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
