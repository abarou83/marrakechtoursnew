<x-app-layout>
    @push('structured_data')
        {!! app(\App\Services\SeoService::class)->generateOrganizationJsonLd() !!}
    @endpush
    <x-top-banner :showHero="true" title="{{ __('Explore the world') }}" subtitle="{{ __('Discover extraordinary adventures and create unforgettable memories') }}" />

    {{-- Why book with us - 4 feature blocks (Carousel) --}}
    @if($featureBlocks->count() > 0)
    <section class="section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate-fade-in-up">
                @if($sectionTitle)
                    <h2 class="section-title text-secondary">{{ $sectionTitle }}</h2>
                @endif
                @if($sectionDescription)
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed mt-3">{{ $sectionDescription }}</p>
                @endif
            </div>
            
            {{-- Swiper Carousel for Feature Blocks --}}
            @php
                // Utiliser la couleur depuis les paramètres de section
                $containerBgColor = $sectionSettings->container_background_color ?? '#F9FAFB';
            @endphp
            <div class="swiper featureBlocksCarousel relative rounded-2xl p-2" style="background-color: {{ $containerBgColor }};">
                <div class="swiper-wrapper">
                    @foreach($featureBlocks as $block)
                        @php
                            $iconBgColor = 'transparent';
                            if ($block->icon_background_color_enabled && !empty($block->icon_background_color)) {
                                $iconBgColor = $block->icon_background_color;
                            }
                        @endphp
                        <div class="swiper-slide">
                            <div class="text-center p-8 animate-fade-in-up">
                                <div class="icon-container mx-auto mb-6 flex items-center justify-center w-16 h-16 rounded-lg" style="background-color: {{ $iconBgColor }};">
                                    @if($block->image_path)
                                        <img src="{{ Storage::url($block->image_path) }}" 
                                             alt="{{ translate_model($block, 'title') }}" 
                                             class="h-12 w-12 object-contain">
                                    @else
                                        <i class="{{ $block->icon }} text-xl" style="color: {{ primary_color() }};"></i>
                                    @endif
                                </div>
                                <h3 class="text-2xl sm:text-2xl md:text-xl font-bold text-gray-900 mb-3">{{ translate_model($block, 'title') }}</h3>
                                <p class="text-base sm:text-base md:text-sm text-gray-600 max-w-xs mx-auto leading-relaxed">{{ translate_model($block, 'description') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                <div class="swiper-pagination featureBlocksCarousel-pagination"></div>
            </div>
        </div>
    </section>
    @endif
    
    {{-- Reviews Section (Google Maps Places API) --}}
    @if(site_setting('reviews_home_active', true))
    @php
        $currentLocale = app()->getLocale();
        $reviewsTitle = site_setting('reviews_home_title_' . $currentLocale, site_setting('reviews_home_title', __('What our travelers say')));
        $googleReviews = is_array($googlePlaceData ?? null) ? ($googlePlaceData['reviews'] ?? []) : [];
        $apiRating = is_array($googlePlaceData ?? null) ? ($googlePlaceData['rating'] ?? null) : null;
        $apiCount = is_array($googlePlaceData ?? null) ? (int) ($googlePlaceData['user_rating_count'] ?? 0) : 0;
        $showApiBadge = $apiRating !== null;
    @endphp
    <section class="section-padding bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate-fade-in-up">
                <h2 class="section-title text-secondary mb-3">
                    {{ $reviewsTitle }}
                </h2>
            </div>

            {{-- Badge note Google (API ou saisie manuelle) --}}
            @if($showApiBadge)
            <div class="flex flex-wrap items-center justify-center mb-6 gap-3">
                <img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" alt="Google" class="h-6">
                @php
                    $rating = (float) $apiRating;
                    $fullStars = (int) floor($rating);
                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                @endphp
                <div class="flex items-center gap-2">
                    @for($i = 0; $i < $fullStars; $i++)
                        <i class="fas fa-star text-yellow-400"></i>
                    @endfor
                    @if($hasHalfStar)
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                    @endif
                    @for($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                        <i class="far fa-star text-gray-300"></i>
                    @endfor
                </div>
                <span class="font-bold text-gray-900">{{ number_format($rating, 1) }}</span>
                @if($apiCount > 0)
                    <span class="text-gray-400">|</span>
                    <span class="font-semibold text-gray-900">{{ __(':count Google reviews', ['count' => $apiCount]) }}</span>
                @endif
            </div>
            @elseif(site_setting('reviews_home_google_rating') || site_setting('reviews_home_google_text'))
            <div class="flex flex-wrap items-center justify-center mb-6 gap-3">
                <img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" alt="Google" class="h-6">
                @php
                    $rating = (float) site_setting('reviews_home_google_rating', '4.9');
                    $fullStars = (int) floor($rating);
                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                @endphp
                <div class="flex items-center gap-2">
                    @for($i = 0; $i < $fullStars; $i++)
                        <i class="fas fa-star text-yellow-400"></i>
                    @endfor
                    @if($hasHalfStar)
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                    @endif
                    @for($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                        <i class="far fa-star text-gray-300"></i>
                    @endfor
                </div>
                <span class="font-bold text-gray-900">{{ number_format($rating, 1) }}</span>
                @if(site_setting('reviews_home_google_text'))
                    <span class="text-gray-400">|</span>
                    <span class="font-semibold text-gray-900">{{ site_setting('reviews_home_google_text') }}</span>
                @endif
            </div>
            @endif

            <div class="min-h-[200px] flex items-center justify-center">
                @if(is_array($googlePlaceData))
                    @php
                        $gReviewSummary = $googlePlaceData['review_summary'] ?? null;
                    @endphp
                    <div class="w-full max-w-6xl mx-auto">
                        @if(!empty($gReviewSummary['text']))
                        <div class="max-w-3xl mx-auto mb-8 p-5 bg-white border border-gray-200 rounded-xl shadow-sm">
                            <p class="text-sm font-semibold text-gray-900 mb-2">{{ __('Summary based on traveler reviews') }}</p>
                            <p class="text-gray-700 text-sm leading-relaxed break-words [overflow-wrap:anywhere]">{{ $gReviewSummary['text'] }}</p>
                            @if(!empty($gReviewSummary['disclosure_text']))
                                <p class="text-xs text-gray-500 mt-3">{{ $gReviewSummary['disclosure_text'] }}</p>
                            @endif
                        </div>
                        @endif

                        @if(count($googleReviews) > 0)
                        <div class="swiper googleReviewsHomeCarousel pb-12">
                            <div class="swiper-wrapper">
                                @foreach($googleReviews as $gr)
                                    <div class="swiper-slide h-auto">
                                        <article class="p-5 bg-white h-full min-h-[260px] flex flex-col rounded-xl border border-gray-200 shadow-sm">
                                            <div class="flex items-start gap-3 mb-3 flex-shrink-0">
                                                @if(!empty($gr['photo_uri']))
                                                    <img src="{{ $gr['photo_uri'] }}" alt="" class="w-10 h-10 rounded-full object-cover flex-shrink-0" width="40" height="40" loading="lazy" referrerpolicy="no-referrer">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-gray-600 text-sm font-bold" aria-hidden="true">{{ strtoupper(mb_substr($gr['author'] ?: '?', 0, 1)) }}</div>
                                                @endif
                                                <div class="min-w-0 flex-1">
                                                    @if(!empty($gr['author_uri']))
                                                        <a href="{{ $gr['author_uri'] }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-900 text-sm hover:underline truncate block">{{ $gr['author'] ?: 'Google' }}</a>
                                                    @else
                                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $gr['author'] ?: 'Google' }}</p>
                                                    @endif
                                                    @if(!empty($gr['time_label']))
                                                        <p class="text-xs text-gray-500">{{ $gr['time_label'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-0.5 mb-2 flex-shrink-0">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i class="fas fa-star text-sm {{ $s <= ($gr['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                                @endfor
                                            </div>
                                            @if(!empty($gr['text']))
                                                <div class="flex-1 min-h-0 min-w-0 mt-1">
                                                    <p class="text-gray-700 text-sm leading-relaxed break-words [overflow-wrap:anywhere] max-h-72 sm:max-h-80 overflow-y-auto overscroll-y-contain pr-1">{{ $gr['text'] }}</p>
                                                </div>
                                            @endif
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination googleReviewsHome-pagination !relative !bottom-0 mt-4"></div>
                        </div>
                        @endif

                        @if(count($googleReviews) === 0 && empty($gReviewSummary['text']))
                            <p class="text-gray-600 mb-4 text-center px-4">{{ __('No recent Google reviews to display yet.') }}</p>
                        @endif
                    </div>
                @else
                    <div class="w-full max-w-2xl mx-auto text-center px-4">
                        <p class="text-gray-600 mb-4">{{ __('Google reviews are temporarily unavailable. Check your Place ID and API key (Places API New).') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Free cancellation section (enhanced) --}}
    <section class="section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-green-50 rounded-xl p-4 sm:p-5 md:p-6 flex flex-col sm:flex-row items-start gap-3 sm:gap-4 md:gap-5 animate-fade-in-up border border-green-100">
                {{-- Shield Icon --}}
                <div class="flex-shrink-0 w-full sm:w-auto flex justify-center sm:justify-start">
                    <div class="w-14 h-14 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-6 sm:h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- Text Content --}}
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-xl sm:text-lg md:text-xl font-bold text-green-800 mb-2 sm:mb-2">
                        {{ __('FREE Cancellation 24H') }}
                    </h3>
                    <p class="text-base sm:text-sm md:text-base text-green-700 leading-relaxed">
                        {{ __('Book today, lock the price. You can cancel for free within the next 24 hours if your plans change.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- TOURS POPULAIRES --}}
    @if($tours->count() > 0)
    <section id="tours" class="section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate-fade-in-up">
                <h2 class="section-title text-secondary mb-3">
                    {{ __('Our best experiences') }}
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    {{ __('Discover the most appreciated tours by our travelers') }}
                </p>
            </div>

            {{-- Swiper Carousel --}}
            <div class="swiper toursCarousel relative" style="padding-bottom: 0;">
                <div class="swiper-wrapper">
                @foreach($tours->take(6) as $tour)
                <div class="swiper-slide">
                    <x-tour-card :tour="$tour" />
                </div>
                @endforeach
                </div>
                
                {{-- Navigation --}}
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                
            </div>
            
            {{-- Pagination --}}
            <div class="swiper-pagination toursCarousel-pagination" style="position: relative; z-index: 10; margin-top: 2rem;"></div>

            {{-- Voir tous --}}
            <div class="text-center mt-8">
                <a href="{{ route('tours.index') }}" 
                   class="btn-modern inline-flex items-center">
                    {{ __('View all tours') }}
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- FAQ SECTION --}}
    @php
        $faqs = \App\Models\FAQ::active()->ordered()->with('translations')->take(6)->get();
    @endphp
    @if($faqs->count() > 0)
    <section class="section-padding">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate-fade-in-up">
                <h2 class="section-title text-secondary mb-3">
                    {{ __('Have questions?') }}
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    {{ __('Find answers to the most frequently asked questions') }}
                </p>
            </div>

            <div class="space-y-3">
                @foreach($faqs as $faq)
                    @php
                        $translation = $faq->translate();
                    @endphp
                    @if($translation)
                    <details class="faq-details group border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <summary class="faq-summary flex items-center justify-between gap-4 p-5 sm:p-6 cursor-pointer list-none select-none">
                            <span class="text-base sm:text-lg font-semibold text-gray-900 pe-2">
                                {{ $translation->question }}
                            </span>
                            <span class="faq-chevron shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-primary" aria-hidden="true">
                                <i class="fas fa-chevron-down text-sm transition-transform duration-200"></i>
                            </span>
                        </summary>
                        <div class="faq-answer px-5 sm:px-6 pb-5 sm:pb-6 pt-0 text-gray-600 text-sm sm:text-base leading-relaxed border-t border-gray-100">
                            {!! nl2br(e($translation->answer)) !!}
                        </div>
                    </details>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Keywords/Tags Section --}}
    @if(isset($popularKeywords) && $popularKeywords->count() > 0)
    <section class="section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate-fade-in-up">
                <h2 class="text-2xl md:text-3xl font-bold text-secondary mb-3">
                    {{ __('Popular Keywords & Tags') }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ __('Click on any keyword to discover related tours and activities') }}
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                @foreach($popularKeywords as $keyword)
                    @php
                        $badgeClass = 'bg-white border-gray-200 text-gray-700 hover:bg-primary hover:text-white hover:border-primary';
                        $icon = 'fas fa-tag';
                        
                        if ($keyword['type'] === 'location') {
                            $badgeClass = 'bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600';
                            $icon = 'fas fa-map-marker-alt';
                        } elseif ($keyword['type'] === 'category') {
                            $badgeClass = 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-600 hover:text-white hover:border-purple-600';
                            $icon = 'fas fa-th-large';
                        } elseif ($keyword['type'] === 'keyword') {
                            $badgeClass = 'bg-green-50 border-green-200 text-green-700 hover:bg-green-600 hover:text-white hover:border-green-600';
                            $icon = 'fas fa-hashtag';
                        }
                    @endphp
                    <a href="{{ $keyword['url'] }}" 
                       class="inline-flex items-center px-4 py-2 border rounded-full font-medium transition-all duration-200 shadow-sm hover:shadow-md group {{ $badgeClass }}">
                        <i class="{{ $icon }} mr-2 text-sm"></i>
                        <span>{{ $keyword['name'] }}</span>
                        @if(isset($keyword['count']) && $keyword['count'] > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-white/50 group-hover:bg-white/80">
                                {{ $keyword['count'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- View All Keywords Link --}}
            <div class="text-center mt-6">
                <a href="{{ route('tours.index') }}" 
                   class="inline-flex items-center text-primary hover:text-primary/80 font-semibold transition">
                    <span>{{ __('View All Tours') }}</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Swiper Tours Carousel JS --}}
    <style>
        .swiper-button-next, .swiper-button-prev {
            color: var(--color-primary);
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px;
        }
        .swiper-pagination-bullet-active {
            background: var(--color-primary);
        }
        .toursCarousel-pagination {
            position: relative !important;
            bottom: auto !important;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
            z-index: 10 !important;
            transform: none !important;
        }
        .toursCarousel-pagination .swiper-pagination-bullet {
            margin: 0 5px;
        }
        .swiper-slide {
            height: auto;
        }
    </style>
    <script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                // Détecter si on est sur mobile
                const isMobile = window.innerWidth < 640;
                
                // Helper function to check if loop can be enabled
                function canEnableLoop(slidesCount, slidesPerView) {
                    return slidesCount >= slidesPerView * 2;
                }
                
                // Tours Carousel
                const toursCarouselEl = document.querySelector('.toursCarousel');
                const toursSlidesCount = toursCarouselEl ? toursCarouselEl.querySelectorAll('.swiper-slide').length : 0;
                const toursSwiper = new Swiper('.toursCarousel', {
                    slidesPerView: isMobile ? 1.15 : 3,
                    spaceBetween: 20,
                    loop: false,
                    autoplay: { delay: 3000, disableOnInteraction: false },
                    pagination: { el: '.toursCarousel-pagination', clickable: true },
                    navigation: { nextEl: '.toursCarousel .swiper-button-next', prevEl: '.toursCarousel .swiper-button-prev' },
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 20, loop: canEnableLoop(toursSlidesCount, 2) },
                        1024: { slidesPerView: 3, spaceBetween: 30, loop: canEnableLoop(toursSlidesCount, 3) },
                    },
                });
                
                
                // Feature Blocks Carousel
                const featureBlocksCarouselEl = document.querySelector('.featureBlocksCarousel');
                const featureBlocksSlidesCount = featureBlocksCarouselEl ? featureBlocksCarouselEl.querySelectorAll('.swiper-slide').length : 0;
                const featureBlocksSwiper = new Swiper('.featureBlocksCarousel', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: canEnableLoop(featureBlocksSlidesCount, 1),
                    autoplay: { delay: 4000, disableOnInteraction: false },
                    pagination: { el: '.featureBlocksCarousel-pagination', clickable: true },
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 20, loop: canEnableLoop(featureBlocksSlidesCount, 2) },
                        1024: { slidesPerView: 4, spaceBetween: 20, loop: canEnableLoop(featureBlocksSlidesCount, 4) },
                    },
                });

                const googleReviewsEl = document.querySelector('.googleReviewsHomeCarousel');
                if (googleReviewsEl) {
                    const googleReviewsCount = googleReviewsEl.querySelectorAll('.swiper-slide').length;
                    new Swiper('.googleReviewsHomeCarousel', {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: canEnableLoop(googleReviewsCount, 1),
                        autoplay: { delay: 5000, disableOnInteraction: false },
                        pagination: { el: '.googleReviewsHome-pagination', clickable: true },
                        breakpoints: {
                            640: { slidesPerView: Math.min(2, googleReviewsCount) || 1, spaceBetween: 20, loop: canEnableLoop(googleReviewsCount, 2) },
                            1024: { slidesPerView: Math.min(3, googleReviewsCount) || 1, spaceBetween: 24, loop: canEnableLoop(googleReviewsCount, 3) },
                        },
                    });
                }
            }
        });
    </script>
    
    {{-- Style pour afficher un aperçu du slide suivant sur mobile --}}
    <style>
        @media (max-width: 639px) {
            .toursCarousel .swiper-wrapper {
                padding-bottom: 20px;
            }
            .toursCarousel .swiper-slide:not(.swiper-slide-active) {
                opacity: 0.7;
            }
            .toursCarousel .swiper-slide-active {
                opacity: 1;
            }
        }
    </style>
</x-app-layout>
