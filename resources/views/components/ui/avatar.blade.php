@props([
    'src' => null,
    'alt' => '',
    'size' => 'md',
    'initials' => null,
])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-base',
        'lg' => 'w-12 h-12 text-lg',
        'xl' => 'w-16 h-16 text-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge([
    'class' => 'rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-medium overflow-hidden ' . $sizeClass
]) }}>
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full h-full object-cover"
        />
    @elseif($initials)
        <span>{{ $initials }}</span>
    @else
        <x-heroicon-s-user class="w-1/2 h-1/2" />
    @endif
</div>
