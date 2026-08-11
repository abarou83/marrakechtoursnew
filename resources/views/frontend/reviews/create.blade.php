@extends('layouts.app')

@section('title', __('Donner mon avis'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <a href="{{ route('dashboard.reviews') }}" class="inline-flex items-center text-sand-500 hover:text-primary-600 mb-6">
                <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                {{ __('Retour aux avis') }}
            </a>

            <div class="card p-6">
                {{-- Tour Info --}}
                <div class="flex items-center gap-4 pb-6 border-b border-sand-200 mb-6">
                    @if($booking->tour->getFirstMediaUrl('images', 'thumb'))
                        <img src="{{ $booking->tour->getFirstMediaUrl('images', 'thumb') }}" 
                             alt="{{ $booking->tour->translate()?->title }}"
                             class="w-20 h-20 rounded-lg object-cover">
                    @endif
                    <div>
                        <h1 class="text-xl font-display font-bold text-sand-900">
                            {{ $booking->tour->translate()?->title }}
                        </h1>
                        <p class="text-sm text-sand-500">
                            {{ __('Voyage du') }} {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('dashboard.reviews.store', $booking) }}">
                    @csrf

                    {{-- Main Rating --}}
                    <div class="mb-6">
                        <label class="label">{{ __('Note globale') }} *</label>
                        <div x-data="{ rating: {{ old('rating', 5) }} }" class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                    <x-heroicon-s-star class="w-10 h-10 transition" 
                                        x-bind:class="{{ $i }} <= rating ? 'text-accent-500' : 'text-sand-300 hover:text-accent-300'" />
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating">
                        </div>
                        @error('rating')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="label">{{ __('Titre de votre avis') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               placeholder="{{ __('Résumez votre expérience en quelques mots') }}"
                               class="input @error('title') border-red-500 @enderror">
                        @error('title')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Comment --}}
                    <div class="mb-6">
                        <label class="label">{{ __('Votre avis') }} *</label>
                        <textarea name="comment" rows="5" required minlength="20"
                                  placeholder="{{ __('Décrivez votre expérience (minimum 20 caractères)...') }}"
                                  class="input @error('comment') border-red-500 @enderror">{{ old('comment') }}</textarea>
                        @error('comment')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Additional Ratings --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div x-data="{ rating: {{ old('guide_rating', 0) }} }">
                            <label class="label">{{ __('Note du guide') }}</label>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = rating === {{ $i }} ? 0 : {{ $i }}" class="focus:outline-none">
                                        <x-heroicon-s-star class="w-6 h-6 transition" 
                                            x-bind:class="{{ $i }} <= rating ? 'text-accent-500' : 'text-sand-300 hover:text-accent-300'" />
                                    </button>
                                @endfor
                                <input type="hidden" name="guide_rating" x-model="rating">
                            </div>
                        </div>

                        <div x-data="{ rating: {{ old('value_rating', 0) }} }">
                            <label class="label">{{ __('Rapport qualité/prix') }}</label>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = rating === {{ $i }} ? 0 : {{ $i }}" class="focus:outline-none">
                                        <x-heroicon-s-star class="w-6 h-6 transition" 
                                            x-bind:class="{{ $i }} <= rating ? 'text-accent-500' : 'text-sand-300 hover:text-accent-300'" />
                                    </button>
                                @endfor
                                <input type="hidden" name="value_rating" x-model="rating">
                            </div>
                        </div>
                    </div>

                    {{-- Recommend --}}
                    <div class="mb-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="recommend" value="1" checked 
                                   class="w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500">
                            <span class="text-sand-700">{{ __('Je recommande cette excursion') }}</span>
                        </label>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="btn-primary">
                            {{ __('Publier mon avis') }}
                        </button>
                        <a href="{{ route('dashboard.reviews') }}" class="btn-outline">
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
