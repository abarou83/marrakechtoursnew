<div
    class="relative"
    x-data="{
        open: @entangle('showResults'),
        query: @entangle('query'),
    }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
>
    {{-- Search Input --}}
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>

        <input
            type="search"
            wire:model.live.debounce.300ms="query"
            @focus="if (query.length >= 2) open = true"
            placeholder="{{ __('Rechercher un tour, une destination...') }}"
            class="w-full ps-12 pe-4 py-3 bg-white border border-sand-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
            autocomplete="off"
        >

        {{-- Loading indicator --}}
        <div wire:loading wire:target="query" class="absolute inset-y-0 end-0 flex items-center pe-4">
            <svg class="w-5 h-5 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    {{-- Results Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-sand-200 overflow-hidden"
        style="display: none;"
    >
        {{-- Destinations --}}
        @if(count($destinations) > 0)
            <div class="p-3 border-b border-sand-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">
                    {{ __('Destinations') }}
                </h3>
                <div class="space-y-1">
                    @foreach($destinations as $destination)
                        <button
                            type="button"
                            wire:click="selectResult('destination', '{{ $destination['slug'] }}')"
                            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-sand-50 transition-colors text-start"
                        >
                            <div class="w-8 h-8 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-secondary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 truncate">{{ $destination['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $destination['tours_count'] }} {{ __('tours') }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Categories --}}
        @if(count($categories) > 0)
            <div class="p-3 border-b border-sand-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">
                    {{ __('Catégories') }}
                </h3>
                <div class="space-y-1">
                    @foreach($categories as $category)
                        <button
                            type="button"
                            wire:click="selectResult('category', '{{ $category['slug'] }}')"
                            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-sand-50 transition-colors text-start"
                        >
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                            </div>
                            <p class="font-medium text-gray-900 truncate">{{ $category['name'] }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tours --}}
        @if(count($tours) > 0)
            <div class="p-3">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">
                    {{ __('Tours') }}
                </h3>
                <div class="space-y-1">
                    @foreach($tours as $tour)
                        <button
                            type="button"
                            wire:click="selectResult('tour', '{{ $tour['slug'] }}')"
                            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-sand-50 transition-colors text-start"
                        >
                            <div class="w-12 h-12 rounded-lg bg-sand-100 overflow-hidden flex-shrink-0">
                                @if($tour['image'])
                                    <img src="{{ $tour['image'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 truncate">{{ $tour['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $tour['category'] }}</p>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <p class="font-semibold text-gray-900">{{ number_format($tour['price']) }} €</p>
                                <p class="text-xs text-gray-500">/ {{ __('pers.') }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- No results --}}
        @if(count($destinations) === 0 && count($categories) === 0 && count($tours) === 0 && strlen($query) >= 2)
            <div class="p-6 text-center">
                <p class="text-gray-500">{{ __('Aucun résultat pour') }} "{{ $query }}"</p>
            </div>
        @endif

        {{-- View all results --}}
        @if(count($tours) > 0 || count($destinations) > 0)
            <div class="p-3 bg-sand-50 border-t border-sand-100">
                <a
                    href="{{ route('tours.index', ['locale' => app()->getLocale(), 'q' => $query]) }}"
                    class="block w-full text-center py-2 text-primary-600 font-medium hover:text-primary-700 transition-colors"
                >
                    {{ __('Voir tous les résultats') }} →
                </a>
            </div>
        @endif
    </div>
</div>
