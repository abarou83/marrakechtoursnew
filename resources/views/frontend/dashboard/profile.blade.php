@extends('layouts.app')

@section('title', __('Mon profil'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.dashboard._sidebar')

            <div class="flex-1 space-y-6">
                {{-- Profile Info --}}
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-sand-900 mb-4">{{ __('Informations personnelles') }}</h2>
                    
                    <form method="POST" action="{{ route('dashboard.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="label">{{ __('Nom complet') }} *</label>
                                <input type="text" name="name" value="{{ old('name', $client->name) }}" 
                                       required class="input @error('name') border-red-500 @enderror">
                                @error('name')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="label">{{ __('Email') }}</label>
                                <input type="email" value="{{ $client->email }}" disabled 
                                       class="input bg-sand-100 cursor-not-allowed">
                                <p class="text-xs text-sand-500 mt-1">{{ __('L\'email ne peut pas être modifié') }}</p>
                            </div>

                            <div>
                                <label class="label">{{ __('Téléphone') }}</label>
                                <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}" 
                                       class="input">
                            </div>

                            <div>
                                <label class="label">{{ __('Pays') }}</label>
                                <input type="text" name="country" value="{{ old('country', $client->country) }}" 
                                       class="input">
                            </div>

                            <div>
                                <label class="label">{{ __('Ville') }}</label>
                                <input type="text" name="city" value="{{ old('city', $client->city) }}" 
                                       class="input">
                            </div>

                            <div>
                                <label class="label">{{ __('Langue préférée') }}</label>
                                <select name="preferred_language" class="input">
                                    <option value="fr" @selected(($client->preferred_language ?? 'fr') === 'fr')>Français</option>
                                    <option value="en" @selected($client->preferred_language === 'en')>English</option>
                                    <option value="es" @selected($client->preferred_language === 'es')>Español</option>
                                    <option value="ar" @selected($client->preferred_language === 'ar')>العربية</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">{{ __('Devise préférée') }}</label>
                                <select name="preferred_currency" class="input">
                                    <option value="EUR" @selected(($client->preferred_currency ?? 'EUR') === 'EUR')>EUR (€)</option>
                                    <option value="USD" @selected($client->preferred_currency === 'USD')>USD ($)</option>
                                    <option value="GBP" @selected($client->preferred_currency === 'GBP')>GBP (£)</option>
                                    <option value="MAD" @selected($client->preferred_currency === 'MAD')>MAD (DH)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="btn-primary">
                                {{ __('Enregistrer') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password --}}
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-sand-900 mb-4">{{ __('Changer le mot de passe') }}</h2>
                    
                    <form method="POST" action="{{ route('dashboard.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 max-w-md">
                            <div>
                                <label class="label">{{ __('Mot de passe actuel') }} *</label>
                                <input type="password" name="current_password" required 
                                       class="input @error('current_password') border-red-500 @enderror">
                                @error('current_password')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="label">{{ __('Nouveau mot de passe') }} *</label>
                                <input type="password" name="password" required 
                                       class="input @error('password') border-red-500 @enderror">
                                @error('password')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="label">{{ __('Confirmer le mot de passe') }} *</label>
                                <input type="password" name="password_confirmation" required class="input">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="btn-outline">
                                {{ __('Modifier le mot de passe') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
