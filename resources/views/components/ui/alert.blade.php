@props([
    'variant' => 'info',
    'dismissible' => false,
    'icon' => true,
])

@php
    $variants = [
        'success' => [
            'bg' => 'bg-success-50',
            'border' => 'border-success-200',
            'text' => 'text-success-600',
            'icon' => 'check-circle',
        ],
        'warning' => [
            'bg' => 'bg-warning-50',
            'border' => 'border-warning-200',
            'text' => 'text-warning-600',
            'icon' => 'exclamation-triangle',
        ],
        'danger' => [
            'bg' => 'bg-danger-50',
            'border' => 'border-danger-200',
            'text' => 'text-danger-600',
            'icon' => 'x-circle',
        ],
        'info' => [
            'bg' => 'bg-info-50',
            'border' => 'border-info-200',
            'text' => 'text-info-600',
            'icon' => 'information-circle',
        ],
    ];

    $config = $variants[$variant] ?? $variants['info'];
@endphp

<div
    {{ $attributes->merge([
        'class' => 'rounded-base border p-4 ' . $config['bg'] . ' ' . $config['border'],
        'role' => 'alert',
    ]) }}
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="flex items-start gap-3">
        @if($icon)
            <div class="{{ $config['text'] }} flex-shrink-0">
                <x-dynamic-component :component="'heroicon-o-' . $config['icon']" class="w-5 h-5" />
            </div>
        @endif

        <div class="flex-1 {{ $config['text'] }}">
            {{ $slot }}
        </div>

        @if($dismissible)
            <button
                type="button"
                class="{{ $config['text'] }} hover:opacity-75 focus:outline-none"
                @click="show = false"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        @endif
    </div>
</div>
