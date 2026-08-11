@props([
    'tabs' => [],
    'active' => null,
])

@php
    $activeTab = $active ?? (count($tabs) > 0 ? array_key_first($tabs) : null);
@endphp

<div
    x-data="{ activeTab: '{{ $activeTab }}' }"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    {{-- Tab navigation --}}
    <div class="border-b border-sand-200">
        <nav class="flex gap-4 -mb-px" aria-label="Tabs">
            @foreach($tabs as $key => $label)
                <button
                    type="button"
                    @click="activeTab = '{{ $key }}'"
                    :class="activeTab === '{{ $key }}'
                        ? 'border-primary-500 text-primary-600'
                        : 'border-transparent text-sand-500 hover:text-sand-700 hover:border-sand-300'"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors"
                    :aria-selected="activeTab === '{{ $key }}'"
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab content --}}
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
