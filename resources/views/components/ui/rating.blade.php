@props([
    'value' => 0,
    'max' => 5,
    'size' => 'md',
    'showValue' => false,
    'reviewsCount' => null,
])

@php
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $fullStars = floor($value);
    $hasHalfStar = ($value - $fullStars) >= 0.5;
    $emptyStars = $max - $fullStars - ($hasHalfStar ? 1 : 0);
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
    {{-- Full stars --}}
    @for($i = 0; $i < $fullStars; $i++)
        <svg class="{{ $sizeClass }} text-accent-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
    @endfor

    {{-- Half star --}}
    @if($hasHalfStar)
        <svg class="{{ $sizeClass }} text-accent-500" viewBox="0 0 20 20">
            <defs>
                <linearGradient id="half-{{ $value }}">
                    <stop offset="50%" stop-color="currentColor"/>
                    <stop offset="50%" stop-color="#D1C1A7"/>
                </linearGradient>
            </defs>
            <path fill="url(#half-{{ $value }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
    @endif

    {{-- Empty stars --}}
    @for($i = 0; $i < $emptyStars; $i++)
        <svg class="{{ $sizeClass }} text-sand-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
    @endfor

    @if($showValue || $reviewsCount !== null)
        <span class="text-sm text-sand-600 ms-1">
            @if($showValue)
                {{ number_format($value, 1) }}
            @endif
            @if($reviewsCount !== null)
                ({{ $reviewsCount }} {{ __('avis') }})
            @endif
        </span>
    @endif
</div>
