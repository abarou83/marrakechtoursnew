@extends('layouts.app')

@section('title', __('Carte cadeau confirmée'))

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-2xl text-green-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Carte cadeau créée !') }}</h1>
        <p class="text-gray-600 mb-6">{{ __('Voici le code à partager avec le destinataire :') }}</p>
        <div class="bg-gray-100 rounded-xl px-6 py-4 mb-4">
            <code class="text-2xl font-bold text-primary-600 tracking-wider">{{ $giftCard->code }}</code>
        </div>
        <p class="text-lg font-semibold">{{ number_format($giftCard->initial_amount, 2) }} {{ $giftCard->currency }}</p>
        @if($giftCard->expires_at)
            <p class="text-sm text-gray-500 mt-2">{{ __('Valable jusqu\'au :date', ['date' => $giftCard->expires_at->format('d/m/Y')]) }}</p>
        @endif
    </div>
</div>
@endsection
