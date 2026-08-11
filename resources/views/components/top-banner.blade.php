@props([
    'showHero' => false,
    'title' => __('Explore the world'),
    'subtitle' => __("Discover extraordinary adventures and create unforgettable memories"),
])

@php
    // Prefer admin-managed banners; fallback to categories if none
    $banners = \App\Models\Banner::active()->with(['translations', 'images'])->get();
@endphp

@if($banners->count())
<section class="relative">
    <div class="swiper" id="globalTopBanner">
        <div class="swiper-wrapper">
            @foreach($banners as $bn)
                @php
                    $translation = $bn->translate();
                    $bannerImages = $bn->images->count() > 0 ? $bn->images : collect([(object)['path' => $bn->image_path]]);
                @endphp
                @foreach($bannerImages as $bannerImage)
                    <div class="swiper-slide">
                        <div class="relative {{ $showHero
                            ? 'h-[50vh] sm:h-[55vh] md:h-[60vh] lg:h-[65vh] min-h-[400px] sm:min-h-[450px] md:min-h-[500px] lg:min-h-[550px] max-h-[700px]'
                            : 'h-[30vh] sm:h-[35vh] md:h-[40vh] min-h-[200px] sm:min-h-[250px] md:min-h-[300px] max-h-[400px]'
                        }} sm:mx-auto sm:max-w-7xl sm:px-4 sm:px-6 lg:px-8 sm:rounded-2xl overflow-hidden">
                            <img src="{{ public_storage_url($bannerImage->path) }}" alt="{{ $translation ? $translation->title : 'Banner' }}"
                                 class="w-full h-full object-cover sm:rounded-b-2xl" />
                            @if($showHero && $loop->first)
                            <div class="absolute inset-0 flex items-end justify-center pb-[15%] sm:pb-[13%]">
                                <div class="w-full px-4 sm:px-6 lg:px-8">
                                    <div class="max-w-7xl mx-auto text-center">
                                            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white mb-4" style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 0, 0, 0.3);">
                                                {{ $translation ? $translation->title : __('Explore the world') }}
                                            </h1>
                                        @if($translation && $translation->slug)
                                        <p class="text-lg sm:text-xl text-white max-w-3xl mx-auto mb-8">
                                            {{ $translation->slug }}
                                        </p>
                                        @endif
                                        @if($bn->link_url)
                                        <div class="flex items-center justify-center">
                                            <a href="{{ $bn->link_url }}" class="button-personalize">
                                                <div class="bubble-layer bubble-1"></div>
                                                <div class="bubble-layer bubble-2"></div>
                                                <div class="bubble-layer bubble-3"></div>
                                                <div class="bubble-layer bubble-4"></div>
                                                <div class="bubble-layer bubble-5"></div>
                                                <div class="bubble-layer bubble-6"></div>
                                                <div class="bubble-layer bubble-7"></div>
                                                <span>{{ __('Discover') }}</span>
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-8">
                                <div class="w-full sm:max-w-7xl sm:mx-auto px-4 sm:px-6 lg:px-8">
                                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-gray-900 font-semibold">
                                        {{ $translation ? $translation->title : 'Banner' }}
                                    </div>
                                    @if($bn->link_url)
                                        <div class="mt-3">
                                            <a href="{{ $bn->link_url }}" class="inline-flex items-center px-5 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                                                <i class="fas fa-link mr-2"></i> {{ __('Discover') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <style>
        /* Simple fade effect for banner */
        #globalTopBanner .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
        }

        #globalTopBanner .swiper-pagination-bullet-active {
            background: rgba(255, 255, 255, 1);
        }

        /* From Uiverse.io by mdanarul_9390 */
        .button-personalize {
            position: relative;
            padding: 14px 20px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            border: 2px solid var(--color-secondary);
            border-radius: 50px;
            cursor: pointer;
            overflow: hidden;
            background: var(--color-primary);
            display: inline-block;
            z-index: 1;
            transition: transform 0.2s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 0, 0, 0.2);
        }
        
        .button-personalize:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4), 0 0 30px rgba(0, 0, 0, 0.3);
        }

        .button-personalize span {
            position: relative;
            z-index: 15;
        }

        .button-personalize:active {
            transform: scale(0.96);
        }

        .button-personalize::before {
            content: "";
            background: var(--color-secondary);
            border-radius: inherit;
            height: calc(100% - 4px);
            width: calc(100% - 4px);
            position: absolute;
            top: 2px;
            left: 2px;
            z-index: 12;
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.5s ease;
        }

        .button-personalize:hover::before {
            opacity: 1;
            transform: scale(1);
        }

        .bubble-layer {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 70%;
            filter: blur(15px);
            z-index: 0;
        }

        .bubble-1 {
            background: var(--color-secondary);
            top: -20%;
            left: -10%;
            animation: moveUpRight 6s ease-in-out infinite;
        }

        .bubble-2 {
            background: var(--color-primary);
            top: 0%;
            left: 10%;
            animation: moveDownLeft 5s ease-in-out infinite;
            animation-delay: 1s;
        }

        .bubble-3 {
            background: var(--color-accent);
            top: 20%;
            left: 50%;
            animation: moveRight 4s ease-in-out infinite;
            animation-delay: 2s;
        }

        .bubble-4 {
            background: var(--color-accent);
            top: -20%;
            left: 70%;
            animation: moveUpLeft 7s ease-in-out infinite;
            animation-delay: 3s;
        }

        .bubble-5 {
            background: var(--color-secondary);
            top: -20%;
            left: -10%;
            animation: moveUpRight 6s ease-in-out infinite;
        }

        .bubble-7 {
            background: var(--color-primary);
            top: 0%;
            left: 10%;
            animation: moveDownLeft 5s ease-in-out infinite;
            animation-delay: 1s;
        }

        .bubble-6 {
            background: var(--color-accent);
            top: 20%;
            left: 50%;
            animation: moveRight 4s ease-in-out infinite;
            animation-delay: 2s;
        }

        @keyframes moveUpRight {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(100%, -100%);
            }
            50% {
                transform: translate(-50%, 50%);
            }
            75% {
                transform: translate(50%, -50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveDownLeft {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(-100%, 100%);
            }
            50% {
                transform: translate(50%, -50%);
            }
            75% {
                transform: translate(-50%, 50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveRight {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(100%, 0);
            }
            50% {
                transform: translate(-100%, 50%);
            }
            75% {
                transform: translate(50%, -50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveUpLeft {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(-100%, -100%);
            }
            50% {
                transform: translate(50%, 50%);
            }
            75% {
                transform: translate(-50%, -50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveDownRight {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(100%, 100%);
            }
            50% {
                transform: translate(-50%, -50%);
            }
            75% {
                transform: translate(50%, 50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveLeft {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(-100%, 0);
            }
            50% {
                transform: translate(100%, -50%);
            }
            75% {
                transform: translate(-50%, 50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }

        @keyframes moveUp {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(0, -100%);
            }
            50% {
                transform: translate(50%, 50%);
            }
            75% {
                transform: translate(-50%, -50%);
            }
            100% {
                transform: translate(0, 0);
            }
        }
    </style>
    
    <script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
        document.addEventListener('DOMContentLoaded', function () {
            const bannerEl = document.getElementById('globalTopBanner');
            if (bannerEl) {
                const slidesCount = bannerEl.querySelectorAll('.swiper-slide').length;
                // Swiper needs at least 2 slides for loop to work with slidesPerView: 1
                const canLoop = slidesCount >= 2;
                
                new Swiper('#globalTopBanner', {
                    loop: canLoop,
                    speed: 800,
                    spaceBetween: 0,
                    autoplay: { 
                        delay: 5000, 
                        disableOnInteraction: false
                    },
                    pagination: { 
                        el: '#globalTopBanner .swiper-pagination', 
                        clickable: true
                    },
                    slidesPerView: 1,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
            }
        });
    </script>
</section>
@endif
