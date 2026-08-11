@props([
    'hover' => false,
    'padding' => true,
])

<div {{ $attributes->merge([
    'class' => 'bg-white rounded-base shadow-card overflow-hidden transition-shadow duration-200' .
               ($hover ? ' hover:shadow-card-hover' : '') .
               ($padding ? ' p-6' : '')
]) }}>
    {{ $slot }}
</div>
