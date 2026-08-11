<x-app-layout>
    @push('head')
        @if(isset($seo))
            {!! app(\App\Services\SeoService::class)->renderMetaTags($seo) !!}
        @endif
        @if(isset($hreflang))
            @foreach($hreflang as $tag)
                <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
            @endforeach
        @endif
    @endpush
    @push('structured_data')
        {!! app(\App\Services\SeoService::class)->generateTourJsonLd($tour) !!}
    @endpush
    @push('styles')
    <style>
        /* CSS personnalisé pour les couleurs et la typographie */
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary-light { background-color: {{ primary_color() }}20; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .text-light-gray { color: #6b7280; }

        /* Formulaire réservation : sticky sous le menu, sans scroll interne */
        @media (min-width: 1024px) {
            .tour-booking-sticky {
                position: sticky;
                top: calc(5rem + 1rem);
                z-index: 30;
            }
        }
        
        /* Hide scrollbar for thumbnails */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Smooth transitions for gallery */
        .gallery-thumb {
            transition: all 0.3s ease;
        }
        
        /* Thumbnails vertical scroll on mobile */
        @media (max-width: 768px) {
            .scrollbar-hide {
                max-height: 100px;
            }
        }
        
        /* Reviews Carousel Container */
        .reviewsCarousel-container {
            position: relative;
            padding: 0;
            overflow: visible !important;
        }
        
        /* Force overflow visible on swiper but hide slides overflow */
        .reviewsCarousel-container .swiper {
            overflow: hidden !important;
        }
        
        .reviewsCarousel-container .swiper-wrapper {
            overflow: visible !important;
        }
        
        /* Ensure swiper-horizontal doesn't overlap arrows */
        .reviewsCarousel-container .swiper-horizontal {
            overflow: hidden !important;
            position: relative;
            z-index: 1;
        }
        
        /* Hide overflow of slides */
        .reviewsCarousel-container .swiper-slide {
            overflow: hidden;
        }
        
        /* Reviews Carousel Navigation Arrows - Style like image */
        .reviewsCarousel-container .swiper-button-next,
        .reviewsCarousel-container .swiper-button-prev {
            color: #4b5563;
            background: white;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-top: 0;
            z-index: 30 !important;
            transition: all 0.3s ease;
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }
        .reviewsCarousel-container .swiper-button-next:after,
        .reviewsCarousel-container .swiper-button-prev:after {
            font-size: 16px;
            font-weight: 600;
        }
        .reviewsCarousel-container .swiper-button-next:hover,
        .reviewsCarousel-container .swiper-button-prev:hover {
            background: #f9fafb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .reviewsCarousel-container .swiper-button-next {
            right: -22px;
        }
        .reviewsCarousel-container .swiper-button-prev {
            left: -22px;
        }
        @media (max-width: 768px) {
            .reviewsCarousel-container .swiper-button-next,
            .reviewsCarousel-container .swiper-button-prev {
                width: 36px;
                height: 36px;
            }
            .reviewsCarousel-container .swiper-button-next:after,
            .reviewsCarousel-container .swiper-button-prev:after {
                font-size: 12px;
            }
            .reviewsCarousel-container .swiper-button-next {
                right: -18px;
            }
            .reviewsCarousel-container .swiper-button-prev {
                left: -18px;
            }
        }

        .google-tour-reviews-wrap .swiper-button-next,
        .google-tour-reviews-wrap .swiper-button-prev {
            color: #4b5563;
            background: white;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .google-tour-reviews-wrap .swiper-button-next:after,
        .google-tour-reviews-wrap .swiper-button-prev:after {
            font-size: 18px;
        }
        .google-tour-reviews-wrap .swiper-button-next {
            right: -22px;
        }
        .google-tour-reviews-wrap .swiper-button-prev {
            left: -22px;
        }
        
        /* Ensure section allows overflow */
        #reviews {
            overflow: visible !important;
        }
        
        /* Review Card Styles - Force border and rounded corners */
        .review-card {
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            position: relative;
            z-index: 1;
        }
        
        /* Force shadow visibility - alternative method */
        .reviewsCarousel-container .swiper-slide .review-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Uniform size for all review cards */
        .reviewsCarousel-container .swiper-slide {
            height: auto;
            display: flex;
        }
        
        .review-card {
            height: 180px;
            display: flex;
            flex-direction: column;
            width: 100%;
            overflow: hidden;
        }
        
        /* Gallery Lightbox Styles */
        [x-cloak] {
            display: none !important;
        }
    </style>
    @endpush

    @php
        // Récupérer uniquement les avis approuvés
        $tourGooglePlaceData = $tourGooglePlaceData ?? null;
        $tourGoogleReviewsRequested = $tourGoogleReviewsRequested ?? false;
        $tourGoogleReviewsList = is_array($tourGooglePlaceData) ? ($tourGooglePlaceData['reviews'] ?? []) : [];
        $tourGoogleSummary = is_array($tourGooglePlaceData) ? ($tourGooglePlaceData['review_summary'] ?? null) : null;

        $reviews = $tour->reviews()->approved()->with('user')->latest()->get();
        $avgRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
        $reviewCount = $reviews->count();
        $fullStars = floor($avgRating);
        $hasHalfStar = ($avgRating - $fullStars) >= 0.5;

        $headerAvgRating = (float) $avgRating;
        $headerReviewCount = (int) $reviewCount;
        $headerFullStars = (int) $fullStars;
        $headerHasHalfStar = $hasHalfStar;
        if (is_array($tourGooglePlaceData) && ($tourGooglePlaceData['rating'] ?? null) !== null) {
            $headerAvgRating = (float) $tourGooglePlaceData['rating'];
            $headerReviewCount = (int) ($tourGooglePlaceData['user_rating_count'] ?? 0);
            $headerFullStars = (int) floor($headerAvgRating);
            $headerHasHalfStar = ($headerAvgRating - $headerFullStars) >= 0.5;
        }
        $showTourHeaderRating = ($tourGoogleReviewsRequested && is_array($tourGooglePlaceData) && ($tourGooglePlaceData['rating'] ?? null) !== null)
            || $reviewCount > 0;
        
        // Obtenir tous les tarifs actifs
        $allPricings = $tour->getActivePricings();
        
        // Obtenir le tarif pour 2 participants par défaut (ou le tarif par défaut)
        $defaultParticipants = 2;
        $pricingForDefault = $tour->getPricingForParticipants($defaultParticipants) ?? $defaultPricing;
        
        // Obtenir le prix minimum (pour l'affichage initial avec 2 participants)
        $originalPrice = null;
        if ($pricingForDefault && !$pricingForDefault->requiresConsultation()) {
            $originalPrice = (float)$pricingForDefault->price_min;
        } elseif ($defaultPricing && !$defaultPricing->requiresConsultation()) {
            $originalPrice = (float)$defaultPricing->price_min;
        } else {
            $originalPrice = $tour->price ? (float)$tour->price : 79.00;
        }
        
        $originalPriceConverted = $originalPrice ? \App\Helpers\CurrencyHelper::convert($originalPrice) : null;
        $formattedOriginalPrice = $originalPriceConverted ? \App\Helpers\CurrencyHelper::format($originalPriceConverted) : __('On request');
        
        // Calculer le prix avec promotion si active
        $finalPrice = $originalPriceConverted;
        $formattedPrice = $formattedOriginalPrice;
        $hasPromo = false;
        $savings = 0;
        $formattedSavings = '';
        
        if($activePromotion && $originalPrice) {
            $discountedPrice = (float)$activePromotion->calculateDiscountedPrice($originalPrice);
            $discountedPriceConverted = \App\Helpers\CurrencyHelper::convert($discountedPrice);
            $finalPrice = $discountedPriceConverted;
            $formattedPrice = \App\Helpers\CurrencyHelper::format($discountedPriceConverted);
            $savings = $originalPriceConverted - $discountedPriceConverted;
            $formattedSavings = \App\Helpers\CurrencyHelper::format($savings);
            $hasPromo = true;
        }
        
        // Préparer les données des tarifs pour JavaScript
        $pricingData = [];
        foreach ($allPricings as $pricing) {
            $basePrice = $pricing->price_min ? (float)$pricing->price_min : null;
            $finalPrice = null;
            $originalPriceForTier = null;
            
            if ($basePrice) {
                $originalPriceForTier = $basePrice;
                // Calculer le prix avec promotion si active
                if ($activePromotion) {
                    $finalPrice = (float)$activePromotion->calculateDiscountedPrice($basePrice);
                } else {
                    $finalPrice = $basePrice;
                }
                // Convertir le prix
                $finalPrice = \App\Helpers\CurrencyHelper::convert($finalPrice);
                $originalPriceForTier = \App\Helpers\CurrencyHelper::convert($originalPriceForTier);
            }
            
            // Calculer les prix enfants et bébés
            $childPrice = null;
            $infantPrice = null;
            if ($basePrice) {
                $childPrice = $pricing->getChildPrice();
                $infantPrice = $pricing->getInfantPrice();
                
                // Appliquer la promotion si active
                if ($activePromotion) {
                    $childPrice = (float)$activePromotion->calculateDiscountedPrice($childPrice);
                    if ($infantPrice > 0) {
                        $infantPrice = (float)$activePromotion->calculateDiscountedPrice($infantPrice);
                    }
                }
                
                // Convertir les prix
                $childPrice = \App\Helpers\CurrencyHelper::convert($childPrice);
                $infantPrice = \App\Helpers\CurrencyHelper::convert($infantPrice);
            }
            
            // Pour le pricing private, inclure les tiers de prix
            $privatePriceTiers = [];
            if ($pricing->pricing_mode === 'private') {
                // Charger les privatePrices si pas déjà  chargé
                if (!$pricing->relationLoaded('privatePrices')) {
                    $pricing->load('privatePrices');
                }
                
                foreach ($pricing->privatePrices as $tier) {
                    $tierPrice = (float)$tier->price;
                    // Appliquer la promotion si active
                    if ($activePromotion) {
                        $tierPrice = (float)$activePromotion->calculateDiscountedPrice($tierPrice);
                    }
                    // Convertir le prix
                    $tierPrice = \App\Helpers\CurrencyHelper::convert($tierPrice);
                    
                    $privatePriceTiers[] = [
                        'min_people' => $tier->min_people,
                        'max_people' => $tier->max_people,
                        'price' => $tierPrice,
                    ];
                }
            }
            
            $pricingTranslation = $pricing->translate();
            $pricingData[] = [
                'id' => $pricing->id,
                'title' => $pricingTranslation ? $pricingTranslation->title : $pricing->title,
                'pricing_mode' => $pricing->pricing_mode,
                'season' => $pricing->season,
                'min_participants' => $pricing->min_participants,
                'max_participants' => $pricing->max_participants ?? 999,
                'price' => $finalPrice, // Prix adulte (ou prix minimum pour private)
                'child_price' => $childPrice,
                'infant_price' => $infantPrice,
                'child_discount_percentage' => $pricing->child_discount_percentage,
                'original_price' => $originalPriceForTier,
                'requires_consultation' => $pricing->requiresConsultation(),
                'label' => $pricing->participants_label,
                'name' => $pricing->name,
                'description' => $pricing->description,
                'private_price_tiers' => $privatePriceTiers, // Tiers de prix pour private pricing
            ];
        }
        
        // Grouper les tarifs par mode pour l'affichage
        $groupPricings = collect($pricingData)->where('pricing_mode', 'group')->values();
        $privatePricings = collect($pricingData)->where('pricing_mode', 'private')->values();
        
        // Parser l'itinéraire avec format titre|texte ou titre - texte
        $itinerary = translate_model($tour, 'itinerary');
        $itineraryItems = collect();
        if (!empty($itinerary)) {
            $lines = preg_split("/\r\n|\r|\n/", $itinerary);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Format: Titre|Description ou Titre - Description
                if (strpos($line, '|') !== false) {
                    [$title, $text] = explode('|', $line, 2);
                    $itineraryItems->push([
                        'title' => trim($title),
                        'text' => trim($text)
                    ]);
                } elseif (strpos($line, ' - ') !== false) {
                    [$title, $text] = explode(' - ', $line, 2);
                    $itineraryItems->push([
                        'title' => trim($title),
                        'text' => trim($text)
                    ]);
                } else {
                    // Si pas de séparateur, utiliser la ligne comme titre uniquement
                    $itineraryItems->push([
                        'title' => $line,
                        'text' => ''
                    ]);
                }
            }
        }
    @endphp

    <div class="min-h-screen pb-20 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <!-- Header Section -->
            <header class="mb-10">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    {{ translate_model($tour, 'title') }}
                </h1>
                
                <!-- Ratings, Badges and Meta Information -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    @if($showTourHeaderRating)
                    <div class="flex items-center gap-2">
                        <span class="text-xl" style="color: #fbbf24;">
                            @for($i = 1; $i <= $headerFullStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            @if($headerHasHalfStar)
                                <i class="fas fa-star-half-alt"></i>
                            @endif
                            @for($i = $headerFullStars + ($headerHasHalfStar ? 1 : 0) + 1; $i <= 5; $i++)
                                <i class="far fa-star text-gray-300"></i>
                            @endfor
                        </span>
                        <span class="text-base font-semibold text-gray-900">
                            {{ number_format($headerAvgRating, 1) }}
                        </span>
                        <span class="text-sm text-gray-600">
                            ({{ $headerReviewCount }} {{ __('reviews') }}{{ $headerReviewCount > 1 ? 's' : '' }})
                        </span>
                    </div>
                    
                    @if($headerAvgRating >= 4.5)
                        <span class="px-3 py-1.5 rounded-full font-semibold text-xs uppercase text-white shadow-sm"
                              style="background-color: #10b981;">
                            {{ __('Recommended') }}
                        </span>
                    @endif
                    @endif
                    
                    @if($activePromotion)
                        <span class="px-3 py-1.5 rounded-full font-semibold text-xs uppercase text-white shadow-sm"
                              style="background-color: #ef4444;">
                            {{ __('Active Promotion') }}
                        </span>
                    @endif
                    
                    <!-- Meta Information aligned with ratings -->
                    <div class="flex flex-wrap items-center gap-2 text-sm sm:text-base text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt" style="color: {{ primary_color() }};"></i>
                            <span>{{ translate_model($tour, 'location') }}</span>
                        </div>
                        <span class="hidden sm:inline">â?¢</span>
                        <div class="flex items-center gap-2">
                            <i class="far fa-clock" style="color: {{ primary_color() }};"></i>
                            <span>{{ translate_model($tour, 'duration') }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-2/3">
                    {{-- Galerie tour --}}
                    <x-tour-gallery :tour="$tour" />
                    <!-- Avis Google (API Places — même fiche que la home) -->
                    @if($tourGoogleReviewsRequested)
                    <section id="reviews"
                             class="p-8 rounded-xl border border-gray-300 mb-8"
                             style="overflow: visible;">
                    <!-- Avis Google -->
                            <i class="fab fa-google mr-2 text-blue-600"></i>
                            {{ __('Google reviews') }}
                        </h2>
                        @if(! is_array($tourGooglePlaceData))
                            <p class="text-base text-gray-600 text-center py-6">{{ __('Google reviews are temporarily unavailable. Check your Place ID and API key (Places API New).') }}</p>
                        @else
                            @if(! empty($tourGoogleSummary['text']))
                            <div class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
                                <p class="text-sm font-semibold text-gray-900 mb-2">{{ __('Summary based on traveler reviews') }}</p>
                                <p class="text-gray-700 text-sm leading-relaxed break-words [overflow-wrap:anywhere]">{{ $tourGoogleSummary['text'] }}</p>
                                @if(! empty($tourGoogleSummary['disclosure_text']))
                                    <p class="text-xs text-gray-500 mt-3">{{ $tourGoogleSummary['disclosure_text'] }}</p>
                                @endif
                            </div>
                            @endif

                            @if(count($tourGoogleReviewsList) > 0)
                            <div class="google-tour-reviews-wrap relative reviewsCarousel-container">
                                <div class="swiper googleReviewsTourCarousel pb-8">
                                    <div class="swiper-wrapper">
                                        @foreach($tourGoogleReviewsList as $gr)
                                            <div class="swiper-slide h-auto">
                                                <article class="p-5 bg-white h-full min-h-[220px] flex flex-col rounded-xl border border-gray-200 shadow-sm">
                                                    <div class="flex items-start gap-3 mb-3 flex-shrink-0">
                                                        @if(! empty($gr['photo_uri']))
                                                            <img src="{{ $gr['photo_uri'] }}" alt="" class="w-10 h-10 rounded-full object-cover flex-shrink-0" width="40" height="40" loading="lazy" referrerpolicy="no-referrer">
                                                        @else
                                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-gray-600 text-sm font-bold" aria-hidden="true">{{ strtoupper(mb_substr($gr['author'] ?: '?', 0, 1)) }}</div>
                                                        @endif
                                                        <div class="min-w-0 flex-1">
                                                            @if(! empty($gr['author_uri']))
                                                                <a href="{{ $gr['author_uri'] }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-900 text-sm hover:underline truncate block">{{ $gr['author'] ?: 'Google' }}</a>
                                                            @else
                                                                <p class="font-semibold text-gray-900 text-sm truncate">{{ $gr['author'] ?: 'Google' }}</p>
                                                            @endif
                                                            @if(! empty($gr['time_label']))
                                                                <p class="text-xs text-gray-500">{{ $gr['time_label'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-0.5 mb-2 flex-shrink-0">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <i class="fas fa-star text-sm {{ $s <= ($gr['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                                        @endfor
                                                    </div>
                                                    @if(! empty($gr['text']))
                                                        <div class="flex-1 min-h-0 min-w-0 mt-1">
                                                            <p class="text-gray-700 text-sm leading-relaxed break-words [overflow-wrap:anywhere] max-h-64 overflow-y-auto pr-1">{{ $gr['text'] }}</p>
                                                        </div>
                                                    @endif
                                                </article>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-pagination googleReviewsTour-pagination !relative !bottom-0 mt-4"></div>
                                </div>
                                <div class="swiper-button-next googleReviewsTour-next"></div>
                                <div class="swiper-button-prev googleReviewsTour-prev"></div>
                            </div>
                            @elseif(empty($tourGoogleSummary['text']))
                                <p class="text-base text-gray-600 text-center py-6">{{ __('No recent Google reviews to display yet.') }}</p>
                            @endif
                        @endif
                    </section>
                    @else
                    {{-- Avis internes si pas de Place ID / clé API configurés --}}
                    @if($reviews->count() > 0)
                    <section id="reviews"
                             x-data="{ showReviews: true }"
                             x-show="showReviews"
                             class="p-8 rounded-xl border border-gray-300 mb-8"
                             style="overflow: visible;">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 pb-4 border-b-2"
                            style="border-color: {{ primary_color() }};">
                            <i class="fas fa-star mr-2" style="color: {{ primary_color() }};"></i>
                            {{ __('Visitor Reviews') }}
                        </h2>
                        @if($reviewCount > 0)
                            <p class="text-base text-gray-600 mb-6">
                                {{ __('Based on') }} <span class="font-semibold">{{ $reviewCount }}</span> {{ __('reviews') }}{{ $reviewCount > 1 ? 's' : '' }} {{ __('total with an average rating of') }} <span class="font-semibold">{{ number_format($avgRating, 1) }}</span> {{ __('star') }}{{ $avgRating > 1 ? 's' : '' }}.
                            </p>
                        @endif

                        <div class="reviewsCarousel-container relative">
                            <div class="reviewsCarousel swiper">
                                <div class="swiper-wrapper">
                                    @foreach($reviews->take(10) as $review)
                                        <div class="swiper-slide">
                                            <div class="review-card p-5 bg-white h-full flex flex-col" style="border: 1px solid #e5e7eb !important; border-radius: 12px !important;">
                                                <div class="flex items-start justify-between gap-3 mb-3">
                                                    <div class="flex-1 min-w-0 flex items-center gap-3">
                                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-semibold text-gray-900 text-sm">{{ $review->user->name ?? 'Visiteur' }}</p>
                                                            <p class="text-xs text-gray-500">{{ $review->created_at->format('M Y') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1 flex-shrink-0">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-sm" style="color: {{ $i <= $review->rating ? '#FFD700' : '#D3D3D3' }};"></i>
                                                        @endfor
                                                    </div>
                                                </div>

                                                @if($review->comment)
                                                    <p class="text-gray-700 text-sm leading-relaxed flex-1">{{ $review->comment }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="swiper-button-next reviewsCarousel-next"></div>
                            <div class="swiper-button-prev reviewsCarousel-prev"></div>
                        </div>
                    </section>
                    @else
                    <section id="reviews" class="p-8 rounded-xl border border-gray-300 mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 pb-4 border-b-2"
                            style="border-color: {{ primary_color() }};">
                            <i class="fas fa-star mr-2" style="color: {{ primary_color() }};"></i>
                            {{ __('Visitor Reviews') }}
                        </h2>
                        <p class="text-base text-gray-600 text-center py-8">{{ __('No reviews yet. Be the first to leave a review!') }}</p>
                    </section>
                    @endif
                    @endif

                    <!-- Overview Section - Design moderne -->
                    <section id="overview" class="p-8 md:p-10 rounded-xl border border-gray-300 mb-10">
                        <div class="mb-8">
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                                <i class="fas fa-info-circle mr-3" style="color: {{ primary_color() }};"></i>
                                {{ __('Tour Overview') }}
                            </h2>
                            <div class="h-1 w-20 rounded-full mt-3"
                                 style="background-color: {{ primary_color() }};"></div>
                        </div>
                        <div class="prose prose-lg max-w-none text-gray-700 mb-8 text-base leading-relaxed">
                            <div class="text-lg leading-8">
                                {!! nl2br(e(translate_model($tour, 'description'))) !!}
                            </div>
                        </div>
                        
                    </section>

                    <!-- Itinerary Section -->
                    @if($itineraryItems->count() > 0)
                    <section id="itinerary" class="p-8 rounded-xl border border-gray-300 mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 pb-4 border-b-2"
                            style="border-color: {{ primary_color() }};">
                            <i class="fas fa-route mr-2" style="color: {{ primary_color() }};"></i>
                            {{ __('Detailed Itinerary') }}
                        </h2>
                        
                        <div class="relative pl-6">
                            {{-- Ligne verticale --}}
                            <div class="absolute left-[19px] top-4 bottom-4 w-[3px] rounded-full" style="background-color: {{ primary_color() }}30;"></div>
                            
                            @foreach($itineraryItems as $index => $item)
                                <div class="relative flex items-start py-4 {{ !$loop->last ? '' : '' }}">
                                    {{-- Point numéroté --}}
                                    <div class="absolute -left-6 w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white z-10 shadow-sm"
                                         style="background-color: {{ primary_color() }};">
                                        {{ $index + 1 }}
                                    </div>
                                    {{-- Texte --}}
                                    <div class="ml-8 pt-1.5">
                                        <p class="text-gray-800 font-medium text-base">
                                            {{ $item['title'] }}@if(!empty($item['text'])) — <span class="text-gray-600 font-normal">{{ $item['text'] }}</span>@endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    <!-- Tags/Keywords Section -->
                    <section class="p-8 rounded-xl border border-gray-300 mb-8">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">{{ __('Tags') }}:</span>
                            @if($tour->location)
                                <a href="{{ route('tours.location', urlencode($tour->location)) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 hover:shadow-md"
                                   style="background-color: {{ primary_color() }}20; color: {{ primary_color() }}; border: 1px solid {{ primary_color() }}40;">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-xs"></i>
                                    {{ $tour->location }}
                                </a>
                            @endif
                            @if($tour->categories && $tour->categories->count() > 0)
                                @foreach($tour->categories as $category)
                                    <a href="{{ route('category.show', $category->url_key) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 hover:shadow-md"
                                       style="background-color: {{ primary_color() }}20; color: {{ primary_color() }}; border: 1px solid {{ primary_color() }}40;">
                                        <i class="fas fa-th-large mr-1.5 text-xs"></i>
                                        {{ translate_model($category, 'name') }}
                                    </a>
                                @endforeach
                            @elseif($tour->category)
                                <a href="{{ route('category.show', $tour->category->url_key) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 hover:shadow-md"
                                   style="background-color: {{ primary_color() }}20; color: {{ primary_color() }}; border: 1px solid {{ primary_color() }}40;">
                                    <i class="fas fa-th-large mr-1.5 text-xs"></i>
                                    {{ translate_model($tour->category, 'name') }}
                                </a>
                            @endif
                            @if($tour->keywords && count($tour->keywords) > 0)
                                @foreach($tour->keywords as $keyword)
                                    <a href="{{ route('tours.location', urlencode($keyword)) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 hover:shadow-md"
                                       style="background-color: {{ primary_color() }}20; color: {{ primary_color() }}; border: 1px solid {{ primary_color() }}40;">
                                        <i class="fas fa-hashtag mr-1.5 text-xs"></i>
                                        {{ $keyword }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </section>

                </div>

                <!-- Sidebar Booking Form -->
                <div class="lg:w-1/3">
                    <div class="tour-booking-sticky">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-4 island-price" style="background: linear-gradient(to bottom, #ffffff 0%, #f9fafb 100%);">
                            <x-booking.partials.price-display :tour="$tour" :activePromotion="$activePromotion" />
                        </div>
                        <div class="island-calendar">
                            <x-booking-form-simple :tour="$tour" :activePromotion="$activePromotion" :showPrice="false" />
                        </div>
                    </div>
                        
                    <!-- Policy Information Section -->
                        <div class="mt-6 p-5 rounded-xl shadow-md"
                             style="background-color: {{ primary_color() }}10; border: 1px solid {{ primary_color() }}30;">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt mr-3 mt-0.5 flex-shrink-0 text-lg"
                                       style="color: {{ primary_color() }};"></i>
                                    <div class="text-sm">
                                        <span class="font-bold text-gray-900">{{ __('Free cancellation') }}</span>
                                        <span class="text-gray-700"> {{ __('up to 24 hours before the experience starts') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mr-3 mt-0.5 flex-shrink-0 text-lg"
                                       style="color: {{ primary_color() }};"></i>
                                    <div class="text-sm">
                                        <span class="font-bold text-gray-900 underline">{{ __('Book now and pay later') }}</span>
                                        <span class="text-gray-700"> — {{ __('Secure your spot while staying flexible') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Book ahead Section -->
                        <div class="mt-4 p-5 bg-white rounded-xl shadow-md border"
                             style="border-color: {{ primary_color() }}30;">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-fire text-2xl"
                                       style="color: {{ primary_color() }};"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 mb-1">{{ __('Book in advance!') }}</p>
                                    <p class="text-xs text-gray-600">{{ __('On average, this experience is booked 33 days in advance.') }}</p>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    
    @php
        $currentCurrency = \App\Helpers\CurrencyHelper::current();
        $tourPageConfig = [
            'pricingData' => $pricingData,
            'hasPromo' => $hasPromo,
            'currencySymbol' => $currentCurrency?->symbol ?? '€',
            'locale' => app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'),
            'labels' => [
                'onRequest' => __('On request'),
                'priceOnRequest' => __('Price on request'),
                'perPerson' => __('per person'),
                'adults' => __('Adult(s)'),
                'children' => __('Child(ren)'),
                'babies' => __('Baby(ies)'),
                'free' => __('FREE'),
                'total' => __('Total'),
                'save' => __('Save'),
            ],
            'recentlyViewed' => [
                'id' => $tour->id,
                'title' => translate_model($tour, 'title'),
                'image' => $tour->primaryImage ? public_storage_url($tour->primaryImage->path) : asset('images/placeholder-tour.svg'),
                'price' => \App\Helpers\CurrencyHelper::format($finalPrice ?? $tour->getMinPrice() ?? $tour->price ?? 0),
                'url' => route('tours.show', $tour->url_key ?? $tour->slug),
            ],
            'bookingForm' => [
                'tourId' => $tour->id,
                'tourSlug' => $tour->url_key ?? $tour->slug,
                'dateLocale' => app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'),
                'months' => [
                    __('January'), __('February'), __('March'), __('April'),
                    __('May'), __('June'), __('July'), __('August'),
                    __('September'), __('October'), __('November'), __('December'),
                ],
                'labels' => [
                    'oneParticipant' => __('1 participant'),
                    'participants' => __('participants'),
                    'adult' => __('adult'),
                    'adults' => __('adults'),
                    'child' => __('child'),
                    'children' => __('children'),
                    'baby' => __('baby'),
                    'babies' => __('babies'),
                    'noParticipant' => __('No participant'),
                    'selectDateParticipants' => __('Please select a date and at least one participant.'),
                    'noPastDate' => __('You cannot select a past date. Please choose a date from today.'),
                ],
            ],
        ];
    @endphp

    @push('scripts')
        <script type="application/json" id="tour-page-config" @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>{!! json_encode($tourPageConfig, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) !!}</script>
        @vite('resources/js/tour-page.js')
    @endpush

    @push('scripts-after-livewire')
        @vite('resources/js/tour-booking-form-simple.js')
    @endpush

    <x-sticky-booking-bar :tour="$tour" />
    <x-recently-viewed />

</x-app-layout>
