@props([
    'tour',
    'pricing' => null,
    'currency' => 'EUR',
])

@php
    $minPrice = $tour->price_from ?? $tour->getMinPrice() ?? $tour->price ?? 0;
    $originalPrice = $tour->original_price ?? null;
    $hasDiscount = $originalPrice && $originalPrice > $minPrice;

    $currencySymbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'MAD' => 'DH'];
    $symbol = $currencySymbols[$currency] ?? '€';

    $reviewsCount = $tour->reviews_count ?? 0;
    $avgRating = $tour->avg_rating ?? 0;
@endphp

<div {{ $attributes->merge(['class' => 'card p-6 sticky top-24']) }}>
    {{-- Price --}}
    <div class="mb-4">
        <div class="flex items-baseline gap-2 flex-wrap">
            <span class="text-sm text-sand-500">{{ __('À partir de') }}</span>
            @if($hasDiscount)
                <span class="text-lg text-sand-400 line-through">
                    {{ number_format($originalPrice, 0, ',', ' ') }}{{ $symbol }}
                </span>
            @endif
        </div>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-3xl font-bold text-primary-500">
                {{ number_format($minPrice, 0, ',', ' ') }}{{ $symbol }}
            </span>
            <span class="text-sand-500">/ {{ __('personne') }}</span>
        </div>
    </div>

    {{-- Rating summary --}}
    @if($reviewsCount > 0)
        <div class="flex items-center gap-2 mb-6 pb-6 border-b border-sand-200">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-accent-500' : 'text-sand-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            <span class="font-semibold text-sand-900">{{ number_format($avgRating, 1) }}</span>
            <a href="#reviews" class="text-sm text-secondary-500 hover:underline">
                {{ number_format($reviewsCount) }} {{ __('avis') }}
            </a>
        </div>
    @endif

    {{-- Quick booking form --}}
    <form
        x-data="{
            date: '',
            adults: 2,
            children: 0,
            loading: false
        }"
        @submit.prevent="loading = true; $dispatch('check-availability', { date, adults, children })"
        class="space-y-4"
    >
        {{-- Date picker --}}
        <div>
            <label class="label">{{ __('Date') }}</label>
            <div class="relative">
                <input
                    type="date"
                    x-model="date"
                    :min="new Date().toISOString().split('T')[0]"
                    class="input ps-10"
                    required
                />
                <x-heroicon-o-calendar class="absolute start-3 top-1/2 -translate-y-1/2 w-5 h-5 text-sand-400 pointer-events-none" />
            </div>
        </div>

        {{-- Travelers --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">{{ __('Adultes') }}</label>
                <div class="flex items-center border border-sand-300 rounded-base">
                    <button
                        type="button"
                        @click="adults = Math.max(1, adults - 1)"
                        class="px-3 py-2.5 text-sand-500 hover:text-sand-700 disabled:opacity-50"
                        :disabled="adults <= 1"
                    >
                        <x-heroicon-s-minus class="w-4 h-4" />
                    </button>
                    <span class="flex-1 text-center font-medium" x-text="adults"></span>
                    <button
                        type="button"
                        @click="adults++"
                        class="px-3 py-2.5 text-sand-500 hover:text-sand-700"
                    >
                        <x-heroicon-s-plus class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div>
                <label class="label">{{ __('Enfants') }}</label>
                <div class="flex items-center border border-sand-300 rounded-base">
                    <button
                        type="button"
                        @click="children = Math.max(0, children - 1)"
                        class="px-3 py-2.5 text-sand-500 hover:text-sand-700 disabled:opacity-50"
                        :disabled="children <= 0"
                    >
                        <x-heroicon-s-minus class="w-4 h-4" />
                    </button>
                    <span class="flex-1 text-center font-medium" x-text="children"></span>
                    <button
                        type="button"
                        @click="children++"
                        class="px-3 py-2.5 text-sand-500 hover:text-sand-700"
                    >
                        <x-heroicon-s-plus class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        {{-- Submit button --}}
        <button
            type="submit"
            class="w-full btn-primary py-3 text-base font-semibold"
            :disabled="loading || !date"
        >
            <span x-show="!loading">{{ __('Vérifier la disponibilité') }}</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <x-ui.spinner size="sm" />
                {{ __('Chargement...') }}
            </span>
        </button>
    </form>

    {{-- Reassurance --}}
    <div class="mt-6 pt-6 border-t border-sand-200 space-y-3">
        <div class="flex items-start gap-3 text-sm">
            <x-heroicon-o-shield-check class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" />
            <div>
                <span class="font-medium text-sand-900">{{ __('Annulation gratuite') }}</span>
                <p class="text-sand-500">{{ __('Jusqu\'à 24h avant le départ') }}</p>
            </div>
        </div>
        <div class="flex items-start gap-3 text-sm">
            <x-heroicon-o-credit-card class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" />
            <div>
                <span class="font-medium text-sand-900">{{ __('Paiement sécurisé') }}</span>
                <p class="text-sand-500">{{ __('Visa, Mastercard, PayPal') }}</p>
            </div>
        </div>
        <div class="flex items-start gap-3 text-sm">
            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" />
            <div>
                <span class="font-medium text-sand-900">{{ __('Support direct') }}</span>
                <p class="text-sand-500">{{ __('WhatsApp avec notre équipe') }}</p>
            </div>
        </div>
    </div>
</div>
