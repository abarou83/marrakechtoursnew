@extends('layouts.app')

@section('title', __('Désinscription'))

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Désinscription confirmée') }}</h1>
        <p class="text-gray-600">{{ __('Vous ne recevrez plus nos emails newsletter.') }}</p>
        <a href="{{ route('home') }}" class="inline-block mt-6 text-primary-600 hover:text-primary-700 font-medium">
            {{ __('Retour à l\'accueil') }}
        </a>
    </div>
</div>
@endsection
