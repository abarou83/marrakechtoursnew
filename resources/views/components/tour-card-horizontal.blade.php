@props([
    'tour',
    'showWishlist' => true,
])

@php
    $locale = app()->getLocale();
    $currency = session('geo.currency', 'EUR');
    $currencySymbol = match($currency) {
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'MAD' => 'DH',
        default => '€',
    };

    $featuredImage = $tour->getFirstMediaUrl('featured', 'thumb')
        ?: $tour->getFirstMediaUrl('gallery', 'thumb')
        ?: 'https://images.unsplash.com/photo-1489749798305-4fea3ae63d43?w=300&h=225&fit=crop';
@endphp

<article class="group flex bg-white rounded-xl border border-sand-200 overflow-hidden transition-all duration-200 hover:shadow-md hover:border-sand-300">
    {{-- Image --}}
    <div class="relative w-32 sm:w-40 flex-shrink-0">
        <a href="{{ route('tours.show', ['locale' => $locale, 'slug' => $tour->getTranslation('slug', $locale)]) }}" class="block h-full">
            <img
                src="{{ $featuredImage }}"
                alt="{{ $tour->name }}"
                class="w-full h-full object-cover"
                loading="lazy"
            >
        </a>

        @if($tour->is_bestseller)
            <span class="absolute top-2 start-2 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-accent-500 text-white">
                {{ __('Best-seller') }}
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1 p-3 sm:p-4 flex flex-col min-w-0">
        <p class="text-[10px] font-medium text-secondary-600 uppercase tracking-wide mb-0.5">
            {{ $tour->category?->name }}
        </p>

        <h3 class="font-semibold text-gray-900 text-sm sm:text-base line-clamp-2 mb-1 group-hover:text-primary-600 transition-colors">
            <a href="{{ route('tours.show', ['locale' => $locale, 'slug' => $tour->getTranslation('slug', $locale)]) }}">
                {{ $tour->name }}
            </a>
        </h3>

        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $tour->formatted_duration }}
            </span>

            @if($tour->reviews_count > 0)
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-accent-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                    </svg>
                    {{ number_format($tour->avg_rating, 1) }} ({{ $tour->reviews_count }})
                </span>
            @endif
        </div>

        <div class="mt-auto flex items-center justify-between">
            <div>
                <span class="text-lg font-bold text-gray-900">{{ number_format($tour->price_adult, 0) }} {{ $currencySymbol }}</span>
                <span class="text-xs text-gray-500">/ {{ __('pers.') }}</span>
            </div>

            @if($showWishlist)
                <button
                    type="button"
                    x-data="{ wishlisted: false }"
                    @click.prevent="wishlisted = !wishlisted"
                    class="p-1.5 rounded-full transition-colors"
                    :class="wishlisted ? 'text-danger-500' : 'text-gray-300 hover:text-gray-400'"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" :fill="wishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</article>
