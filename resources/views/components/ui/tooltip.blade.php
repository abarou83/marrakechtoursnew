@props([
    'text' => '',
    'position' => 'top',
])

@php
    $positions = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 me-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ms-2',
    ];
    $positionClass = $positions[$position] ?? $positions['top'];
@endphp

<div
    x-data="{ show: false }"
    @mouseenter="show = true"
    @mouseleave="show = false"
    class="relative inline-block"
>
    {{ $slot }}

    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 {{ $positionClass }} px-2 py-1 text-xs font-medium text-white bg-sand-900 rounded shadow-lg whitespace-nowrap pointer-events-none"
        x-cloak
    >
        {{ $text }}
    </div>
</div>
