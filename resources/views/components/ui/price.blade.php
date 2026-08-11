@props([
    'amount' => 0,
    'currency' => 'EUR',
    'from' => false,
    'crossed' => null,
    'size' => 'md',
])

@php
    $symbols = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'MAD' => 'DH',
    ];
    $symbol = $symbols[$currency] ?? $currency;

    $sizes = [
        'sm' => 'text-lg',
        'md' => 'text-2xl',
        'lg' => 'text-3xl',
        'xl' => 'text-4xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $formattedAmount = number_format($amount, 2, ',', ' ');
    $formattedCrossed = $crossed ? number_format($crossed, 2, ',', ' ') : null;

    $priceDisplay = in_array($currency, ['USD', 'GBP'])
        ? "{$symbol}{$formattedAmount}"
        : "{$formattedAmount} {$symbol}";

    $crossedDisplay = $formattedCrossed
        ? (in_array($currency, ['USD', 'GBP'])
            ? "{$symbol}{$formattedCrossed}"
            : "{$formattedCrossed} {$symbol}")
        : null;
@endphp

<div {{ $attributes->merge(['class' => 'flex items-baseline gap-2 flex-wrap']) }}>
    @if($from)
        <span class="text-sm text-sand-500 font-normal">{{ __('À partir de') }}</span>
    @endif

    @if($crossed)
        <span class="text-sand-400 line-through text-base">{{ $crossedDisplay }}</span>
    @endif

    <span class="font-bold text-primary-500 {{ $sizeClass }}" aria-label="{{ __('Prix') }}: {{ $priceDisplay }}">
        {{ $priceDisplay }}
    </span>

    @if($slot->isNotEmpty())
        <span class="text-sm text-sand-500">{{ $slot }}</span>
    @endif
</div>
