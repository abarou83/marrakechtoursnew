@props([
    'id' => 'dialog-' . uniqid(),
    'maxWidth' => 'md',
    'title' => null,
    'description' => null,
])

@php
    $maxWidths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'full' => 'max-w-full',
    ];

    $width = $maxWidths[$maxWidth] ?? $maxWidths['md'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-dialog-{{ $id }}.window="open = true"
    x-on:close-dialog-{{ $id }}.window="open = false"
    x-on:keydown.escape.window="open = false"
    {{ $attributes }}
>
    @if(isset($trigger))
        <div @click="open = true">
            {{ $trigger }}
        </div>
    @endif

    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="{{ $id }}-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    x-show="open"
                    @click="open = false"
                    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                ></div>

                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-trap.inert.noscroll="open"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 w-full {{ $width }}"
                    @click.away="open = false"
                >
                    @if($title)
                        <div class="border-b border-sand-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 id="{{ $id }}-title" class="text-lg font-semibold text-gray-900 font-display">
                                        {{ $title }}
                                    </h3>
                                    @if($description)
                                        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
                                    @endif
                                </div>
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-sand-100 hover:text-gray-500 transition-colors"
                                >
                                    <span class="sr-only">{{ __('Fermer') }}</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="px-6 py-4">
                        {{ $slot }}
                    </div>

                    @if(isset($footer))
                        <div class="border-t border-sand-200 bg-sand-50 px-6 py-4">
                            {{ $footer }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
