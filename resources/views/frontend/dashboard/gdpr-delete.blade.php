@extends('layouts.app')

@section('title', __('Supprimer mon compte'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        @include('frontend.dashboard._sidebar')

        <main class="flex-1">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-2xl font-bold text-sand-900 mb-2">{{ __('Supprimer mon compte') }}</h1>
                <p class="text-sand-600 mb-8">
                    {{ __('Conformément au RGPD, vous pouvez demander la suppression de votre compte et de vos données personnelles.') }}
                </p>

                {{-- Warning --}}
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-500 flex-shrink-0" />
                        <div>
                            <h3 class="font-semibold text-red-800">{{ __('Attention : action irréversible') }}</h3>
                            <p class="mt-1 text-sm text-red-700">
                                {{ __('La suppression de votre compte est définitive. Vous ne pourrez pas récupérer vos données une fois cette action effectuée.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- What will be deleted --}}
                <div class="card p-6 mb-6">
                    <h2 class="text-lg font-semibold text-sand-900 mb-4">{{ __('Ce qui sera supprimé ou anonymisé') }}</h2>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <x-heroicon-o-trash class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-medium text-sand-800">{{ __('Informations personnelles') }}</span>
                                <p class="text-sm text-sand-600">{{ __('Nom, email, téléphone, adresse seront supprimés') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-heroicon-o-trash class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-medium text-sand-800">{{ __('Liste de favoris') }}</span>
                                <p class="text-sm text-sand-600">{{ __('Votre wishlist sera entièrement supprimée') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-heroicon-o-eye-slash class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-medium text-sand-800">{{ __('Avis') }}</span>
                                <p class="text-sm text-sand-600">{{ __('Vos avis seront anonymisés mais resteront visibles') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-heroicon-o-eye-slash class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-medium text-sand-800">{{ __('Réservations') }}</span>
                                <p class="text-sm text-sand-600">{{ __('Les réservations passées seront anonymisées pour des raisons comptables') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Deletion Form --}}
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-sand-900 mb-4">{{ __('Confirmer la suppression') }}</h2>
                    
                    <form action="{{ route('client.gdpr.delete') }}" method="POST" x-data="{ confirmed: false }">
                        @csrf
                        @method('DELETE')

                        <div class="space-y-4">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-sand-700 mb-1">
                                    {{ __('Mot de passe actuel') }}
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    required
                                    class="w-full rounded-lg border-sand-300 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="{{ __('Entrez votre mot de passe pour confirmer') }}"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Keep bookings option --}}
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    name="keep_bookings" 
                                    id="keep_bookings"
                                    value="1"
                                    checked
                                    class="mt-1 w-4 h-4 rounded border-sand-300 text-primary-500 focus:ring-primary-500"
                                >
                                <label for="keep_bookings" class="text-sm text-sand-700">
                                    {{ __('Conserver mes réservations de manière anonymisée (recommandé pour vos preuves d\'achat)') }}
                                </label>
                            </div>

                            {{-- Confirmation checkbox --}}
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    name="confirm_deletion" 
                                    id="confirm_deletion"
                                    value="1"
                                    required
                                    x-model="confirmed"
                                    class="mt-1 w-4 h-4 rounded border-sand-300 text-red-500 focus:ring-red-500"
                                >
                                <label for="confirm_deletion" class="text-sm text-sand-700">
                                    {{ __('Je comprends que cette action est irréversible et je souhaite supprimer définitivement mon compte.') }}
                                </label>
                            </div>
                            @error('confirm_deletion')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('client.dashboard') }}" class="btn-outline flex-1 text-center">
                                {{ __('Annuler') }}
                            </a>
                            <button 
                                type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!confirmed"
                            >
                                {{ __('Supprimer définitivement mon compte') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Alternative --}}
                <div class="mt-6 text-center text-sm text-sand-600">
                    <p>
                        {{ __('Vous souhaitez simplement ne plus recevoir d\'emails ?') }}
                        <a href="{{ route('client.notifications') }}" class="text-primary-600 hover:underline">
                            {{ __('Gérer mes préférences de notifications') }}
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
