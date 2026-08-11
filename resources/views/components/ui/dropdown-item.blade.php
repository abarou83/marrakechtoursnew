@props([
    'href' => null,
    'active' => false,
    'icon' => null,
    'danger' => false,
])

@php
    $baseClasses = 'flex items-center gap-3 w-full px-4 py-2.5 text-sm text-start transition-colors duration-150';

    if ($danger) {
        $classes = $baseClasses . ' text-danger-600 hover:bg-danger-50';
    } elseif ($active) {
        $classes = $baseClasses . ' bg-primary-50 text-primary-700';
    } else {
        $classes = $baseClasses . ' text-gray-700 hover:bg-sand-50';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                {{ $icon }}
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                {{ $icon }}
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
