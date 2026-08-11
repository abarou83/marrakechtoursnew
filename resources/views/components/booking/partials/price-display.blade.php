@props(['tour', 'activePromotion' => null])

@php
    $hasActivePricings = $tour->pricings()
        ->where('is_active', true)
        ->where(function ($query) {
            $query->whereHas('groupPrices')->orWhereHas('privatePrices');
        })
        ->exists();

    $defaultPricing = $tour->defaultPricing();
    $basePrice = null;
    $promoPrice = null;

    if ($defaultPricing && !$defaultPricing->requiresConsultation()) {
        $basePrice = (float) $defaultPricing->price_min;
        if ($activePromotion) {
            $promoPrice = (float) $activePromotion->calculateDiscountedPrice($basePrice);
        }
    }

    $displayPrice = $promoPrice ?? $basePrice;
    $formattedPrice = $displayPrice
        ? \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($displayPrice))
        : __('Consult');
    $primaryColor = primary_color();
@endphp

@if($hasActivePricings)
    <div class="text-center">
        @if($promoPrice && $basePrice)
            <div class="text-sm text-gray-500 mb-2">{{ __('Promo price') }}</div>
            <div class="flex items-baseline justify-center gap-2 flex-wrap">
                <span class="line-through text-gray-400 text-lg">{{ \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($basePrice)) }}</span>
                <span class="text-3xl font-bold" style="color: {{ $primaryColor }};">{{ $formattedPrice }}</span>
                <span class="text-xs text-gray-500">{{ __('per person') }}</span>
            </div>
        @elseif($displayPrice)
            <div class="flex items-baseline justify-center gap-2 flex-wrap">
                <span class="text-sm text-gray-500">{{ __('From') }}</span>
                <span class="text-3xl font-bold text-gray-900">{{ $formattedPrice }}</span>
                <span class="text-xs text-gray-500">{{ __('per person') }}</span>
            </div>
        @else
            <div class="text-3xl font-bold text-gray-900">{{ __('Consult') }}</div>
        @endif
    </div>
@endif
