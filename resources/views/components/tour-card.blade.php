@props([
    'tour',
    'showWishlist' => true,
    'lazy' => true,
])

@php
    $translation = $tour->translate(app()->getLocale());
    $title = $translation?->title ?? $tour->title;
    $slug = $translation?->slug ?? $tour->slug;
    $description = $translation?->description ?? $tour->description;

    $minPrice = (float) ($tour->price_from ?? $tour->getMinPrice() ?? $tour->price ?? 0);
    $originalPrice = isset($tour->original_price) && is_numeric($tour->original_price)
        ? (float) $tour->original_price
        : null;
    $hasDiscount = $originalPrice !== null && $originalPrice > $minPrice;

    $currency = session('currency', 'EUR');
    $currencySymbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'MAD' => 'DH'];
    $symbol = $currencySymbols[$currency] ?? '€';

    $badges = [];

    if ($tour->is_bestseller) {
        $badges[] = ['type' => 'bestseller', 'label' => __('Best-seller'), 'color' => 'bg-accent-500 text-sand-950'];
    }

    if ($tour->is_featured) {
        $badges[] = ['type' => 'featured', 'label' => __('Certifié'), 'color' => 'bg-secondary-500 text-white'];
    }

    if ($tour->created_at && $tour->created_at->diffInDays(now()) < 60) {
        $badges[] = ['type' => 'new', 'label' => __('Nouveau'), 'color' => 'bg-success-500 text-white'];
    }

    if ($hasDiscount) {
        $discountPercent = round((($originalPrice - $minPrice) / $originalPrice) * 100);
        $badges[] = ['type' => 'promo', 'label' => "-{$discountPercent}%", 'color' => 'bg-danger-500 text-white'];
    }

    $primaryImage = $tour->primaryImage ?? $tour->images->first();
    $imageUrl = $primaryImage?->url ?? asset('images/placeholder-tour.svg');

    $category = $tour->categories->first() ?? $tour->category;
    $categoryName = $category ? ($category->translate()?->name ?? $category->name) : null;

    $reviewsCount = $tour->reviews_count ?? 0;
    $avgRating = $tour->avg_rating ?? 0;

    $duration = translate_model($tour, 'duration') ?: $tour->duration;
    if (is_numeric($duration)) {
        $hours = (float) $duration;
        $durationLabel = $hours > 24
            ? (int) ceil($hours / 24) . ' ' . __('jours')
            : (int) $hours . 'h';
    } else {
        $durationLabel = $duration ?: null;
    }

    $features = [];
    if ($tour->has_transfer ?? false) {
        $features[] = __('Transfert inclus');
    }
    if ($tour->max_group_size && $tour->max_group_size <= 15) {
        $features[] = __('Petit groupe');
    }
    if ($tour->has_guide ?? true) {
        $features[] = __('Guide local');
    }

    $tourUrl = route('tours.show', ['locale' => app()->getLocale(), 'slug' => $slug]);
@endphp

<article {{ $attributes->merge(['class' => 'group card card-hover flex flex-col h-full']) }}>
    {{-- Image container with badges and wishlist --}}
    <div class="relative aspect-[4/3] overflow-hidden">
        <a href="{{ $tourUrl }}" class="block w-full h-full">
            <img
                @if($lazy) loading="lazy" @endif
                decoding="async"
                src="{{ $imageUrl }}"
                alt="{{ $title }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                width="400"
                height="300"
            />
            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </a>

        {{-- Badges (top-left) --}}
        @if(count($badges) > 0)
            <div class="absolute top-3 start-3 flex flex-wrap gap-1.5 max-w-[70%]">
                @foreach($badges as $badge)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $badge['color'] }} shadow-sm">
                        {{ $badge['label'] }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Wishlist button (top-right) --}}
        @if($showWishlist)
            <button
                type="button"
                x-data="wishlistButton({{ $tour->id }})"
                @click="toggle($event)"
                class="absolute top-3 end-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm shadow-md hover:bg-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                :class="{ 'opacity-60': loading }"
                :aria-label="wishlisted ? '{{ __('Retirer des favoris') }}' : '{{ __('Ajouter aux favoris') }}'"
            >
                <svg
                    class="w-5 h-5 transition-colors duration-200"
                    :class="wishlisted ? 'text-danger-500 fill-current' : 'text-sand-600'"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex flex-col flex-1 p-4">
        {{-- Category --}}
        @if($categoryName)
            <span class="text-xs font-medium text-secondary-500 uppercase tracking-wide mb-1">
                {{ $categoryName }}
            </span>
        @endif

        {{-- Title --}}
        <h3 class="font-display text-lg font-semibold text-sand-900 mb-2 line-clamp-2 group-hover:text-primary-500 transition-colors">
            <a href="{{ $tourUrl }}">{{ $title }}</a>
        </h3>

        {{-- Features metadata --}}
        @if($durationLabel || count($features) > 0)
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-sand-500 mb-3">
                @if($durationLabel)
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        {{ $durationLabel }}
                    </span>
                @endif
                @foreach(array_slice($features, 0, 2) as $feature)
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-success-500" />
                        {{ $feature }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Rating --}}
        @if($reviewsCount > 0)
            <div class="flex items-center gap-1.5 mb-3">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-accent-500' : 'text-sand-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm font-medium text-sand-700">{{ number_format($avgRating, 1) }}</span>
                <span class="text-sm text-sand-500">({{ number_format($reviewsCount) }} {{ __('avis') }})</span>
            </div>
        @endif

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mb-2">
            <span class="text-xs text-sand-500">{{ __('À partir de') }}</span>
            @if($hasDiscount)
                <span class="text-sm text-sand-400 line-through">
                    {{ number_format($originalPrice, 0, ',', ' ') }}{{ $symbol }}
                </span>
            @endif
            <span class="text-xl font-bold text-primary-500">
                {{ number_format($minPrice, 0, ',', ' ') }}{{ $symbol }}
            </span>
            <span class="text-sm text-sand-500">/ {{ __('pers.') }}</span>
        </div>

        {{-- Reassurance --}}
        <div class="flex items-center gap-1.5 text-xs text-success-600">
            <x-heroicon-s-check-circle class="w-4 h-4 flex-shrink-0" />
            <span>{{ __('Annulation gratuite jusqu\'à 24h avant') }}</span>
        </div>
    </div>
</article>
