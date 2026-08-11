@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'start-0',
        'right' => 'end-0',
        'center' => 'start-1/2 -translate-x-1/2',
        default => 'end-0',
    };

    $widthClasses = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        'full' => 'w-full',
        default => 'w-48',
    };
@endphp

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @close.stop="open = false"
    class="relative"
>
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClasses }} {{ $alignmentClasses }} rounded-base shadow-lg ring-1 ring-black ring-opacity-5 {{ $contentClasses }}"
        @click="open = false"
        x-cloak
    >
        {{ $content }}
    </div>
</div>
