@extends('layouts.app')

@section('title', __('Cartes cadeaux'))

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Offrez une expérience inoubliable') }}</h1>
    <p class="text-gray-600 mb-8">{{ __('Cartes cadeaux valables :months mois sur tous nos tours.', ['months' => config('marketing.gift_card.validity_months', 12)]) }}</p>
    @livewire('gift-card-checkout')
</div>
@endsection
