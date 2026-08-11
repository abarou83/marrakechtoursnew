@props([
    'name',
    'label' => null,
    'icon' => null,
])

<button
    type="button"
    x-data="{ name: '{{ $name }}' }"
    x-init="$dispatch('tab-registered', { name })"
    @click="activeTab = '{{ $name }}'"
    :class="{
        '-mb-px py-3 text-sm font-medium border-b-2 transition-colors duration-200 border-primary-500 text-primary-600': activeTab === '{{ $name }}',
        '-mb-px py-3 text-sm font-medium border-b-2 transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $name }}'
    }"
    role="tab"
    :aria-selected="activeTab === '{{ $name }}'"
    {{ $attributes }}
>
    @if($icon)
        <span class="me-2">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 inline" />
        </span>
    @endif
    {{ $label ?? $slot }}
</button>
