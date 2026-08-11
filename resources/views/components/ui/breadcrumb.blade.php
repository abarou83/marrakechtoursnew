@props([
    'items' => [],
])

<nav {{ $attributes->merge(['class' => 'flex', 'aria-label' => __('Fil d\'Ariane')]) }}>
    <ol class="flex items-center gap-2 text-sm flex-wrap">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-2">
                @if($index > 0)
                    <x-heroicon-s-chevron-right class="w-4 h-4 text-sand-400 rtl:rotate-180" />
                @endif

                @if(isset($item['url']) && $index !== count($items) - 1)
                    <a
                        href="{{ $item['url'] }}"
                        class="text-sand-600 hover:text-primary-500 transition-colors"
                    >
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-sand-900 font-medium" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
