@props(['count' => 0])

@if($count > 0)
    <a href="{{ route('cart.index') }}"
       title="{{ __('Cart') }}"
       class="relative inline-flex items-center justify-center p-2 rounded-full text-gray-700 hover:text-primary hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-bold text-white bg-primary rounded-full">
            {{ $count > 9 ? '9+' : $count }}
        </span>
        <span class="sr-only">{{ __('Cart') }} ({{ $count }})</span>
    </a>
@endif
