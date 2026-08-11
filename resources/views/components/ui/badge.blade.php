@props([
    'variant' => 'primary',
    'size' => 'md',
    'dot' => false,
])

@php
    $variants = [
        'primary' => 'bg-primary-100 text-primary-700',
        'secondary' => 'bg-secondary-100 text-secondary-700',
        'accent' => 'bg-accent-100 text-accent-700',
        'success' => 'bg-success-50 text-success-600',
        'warning' => 'bg-warning-50 text-warning-600',
        'danger' => 'bg-danger-50 text-danger-600',
        'info' => 'bg-info-50 text-info-600',
        'gray' => 'bg-sand-100 text-sand-600',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm',
    ];

    $classes = 'inline-flex items-center rounded-full font-medium ' .
               ($variants[$variant] ?? $variants['primary']) . ' ' .
               ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current me-1.5"></span>
    @endif
    {{ $slot }}
</span>
