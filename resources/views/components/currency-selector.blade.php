@php
    $currencies = \App\Models\Currency::where('is_active', true)
        ->orderByDesc('is_default')->orderBy('code')->get();
    $current = \App\Helpers\CurrencyHelper::current();
    $currentCode = $current?->code;
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <div>
        <button type="button"
                class="inline-flex items-center justify-center px-4 py-2.5 text-base font-semibold rounded-full text-gray-700 hover:text-primary hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors duration-200">
            <span class="font-mono text-base">{{ $currentCode }}</span>
        </button>
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-10 w-44 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        <div class="py-1">
            @foreach($currencies as $c)
                <a href="{{ request()->fullUrlWithQuery(['currency' => $c->code]) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $currentCode === $c->code ? 'bg-gray-50 font-semibold' : '' }}">
                    <span>{{ $c->code }}</span>
                    @if($currentCode === $c->code)
                        <svg class="w-4 h-4 ml-auto text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
