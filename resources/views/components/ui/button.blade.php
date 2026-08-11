@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-base transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 focus-visible:ring-primary-500 shadow-button',
        'secondary' => 'bg-secondary-500 text-white hover:bg-secondary-600 active:bg-secondary-700 focus-visible:ring-secondary-500 shadow-button',
        'accent' => 'bg-accent-500 text-sand-950 hover:bg-accent-600 active:bg-accent-700 focus-visible:ring-accent-500 shadow-button',
        'outline' => 'border-2 border-primary-500 text-primary-500 bg-transparent hover:bg-primary-50 active:bg-primary-100 focus-visible:ring-primary-500',
        'ghost' => 'text-sand-700 hover:bg-sand-100 active:bg-sand-200 focus-visible:ring-sand-500',
        'danger' => 'bg-danger-500 text-white hover:bg-danger-600 active:bg-danger-700 focus-visible:ring-danger-500 shadow-button',
        'success' => 'bg-success-500 text-white hover:bg-success-600 active:bg-success-700 focus-visible:ring-success-500 shadow-button',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            <x-heroicon-o-{{ $icon }} class="w-5 h-5" />
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right' && !$loading)
            <x-heroicon-o-{{ $icon }} class="w-5 h-5" />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled || $loading) disabled @endif
    >
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            <x-heroicon-o-{{ $icon }} class="w-5 h-5" />
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right' && !$loading)
            <x-heroicon-o-{{ $icon }} class="w-5 h-5" />
        @endif
    </button>
@endif
