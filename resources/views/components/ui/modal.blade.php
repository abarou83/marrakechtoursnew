@props([
    'name' => '',
    'maxWidth' => 'lg',
    'closeable' => true,
])

@php
    $maxWidths = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        'full' => 'sm:max-w-full sm:mx-4',
    ];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    class="relative z-50"
    x-cloak
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-sand-900/50 backdrop-blur-sm"
        @if($closeable) @click="open = false" @endif
    ></div>

    <!-- Modal -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 overflow-y-auto p-4 sm:p-6"
    >
        <div class="flex min-h-full items-center justify-center">
            <div
                {{ $attributes->merge([
                    'class' => 'relative w-full bg-white rounded-base shadow-xl ' . ($maxWidths[$maxWidth] ?? $maxWidths['lg'])
                ]) }}
                @click.stop
            >
                @if($closeable)
                    <button
                        type="button"
                        class="absolute top-4 end-4 text-sand-400 hover:text-sand-600 focus:outline-none z-10"
                        @click="open = false"
                    >
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>
</div>
