@props(['tour', 'showAll' => false])

@php
    $activePromotion = $tour->activePromotion();
    $pricings = $showAll ? $tour->pricings()->active()->get() : [$tour->defaultPricing()];
    $pricings = collect($pricings)->filter();
@endphp

<div class="space-y-4">
    {{-- Promotion Badge --}}
    @if($activePromotion)
        <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white p-4 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">🎉</span>
                    <div>
                        <div class="font-bold text-lg">{{ $activePromotion->name }}</div>
                        @if($activePromotion->description)
                            <div class="text-sm opacity-90">{{ $activePromotion->description }}</div>
                        @endif
                        <div class="text-xs mt-1 opacity-75">
                            Valable jusqu'au {{ $activePromotion->end_date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white text-red-600 px-4 py-2 rounded-full font-bold text-xl">
                        {{ $activePromotion->discount_text }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Pricing Options --}}
    <div class="space-y-3">
        @forelse($pricings as $pricing)
            @if($pricing)
                <div class="bg-white border-2 {{ $pricing->is_default ? 'border-indigo-500' : 'border-gray-200' }} rounded-xl p-5 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <h4 class="font-bold text-lg text-gray-900">{{ $pricing->name }}</h4>
                                @if($pricing->is_default)
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                        <i class="fas fa-star mr-1"></i> Populaire
                                    </span>
                                @endif
                            </div>
                            
                            @if($pricing->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $pricing->description }}</p>
                            @endif
                            
                            <div class="text-xs text-gray-500 mt-2">
                                👥 {{ $pricing->min_participants }}
                                @if($pricing->max_participants)
                                    - {{ $pricing->max_participants }}
                                @else
                                    +
                                @endif
                                participants
                            </div>
                        </div>
                        
                        <div class="text-right ml-4">
                            @php
                                $convMin = \App\Helpers\CurrencyHelper::convert((float)$pricing->price_min);
                                $fmtMin = \App\Helpers\CurrencyHelper::format($convMin);
                                $hasRange = $pricing->price_max && $pricing->price_max != $pricing->price_min;
                                if ($hasRange) {
                                    $convMax = \App\Helpers\CurrencyHelper::convert((float)$pricing->price_max);
                                    $fmtMax = \App\Helpers\CurrencyHelper::format($convMax);
                                }
                            @endphp
                            @if($activePromotion)
                                {{-- Prix avec promotion --}}
                                @php
                                    $originalPrice = (float)$pricing->price_min;
                                    $discountedPrice = (float)$activePromotion->calculateDiscountedPrice($originalPrice);
                                    $savings = $originalPrice - $discountedPrice;
                                    $fmtOrig = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($originalPrice));
                                    $fmtDisc = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($discountedPrice));
                                    $fmtSave = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($savings));
                                @endphp
                                <div class="text-sm text-gray-500 line-through">{{ $fmtOrig }}</div>
                                <div class="text-2xl font-bold text-green-600">{{ $fmtDisc }}</div>
                                <div class="text-xs text-green-600 font-semibold mt-1">
                                    <i class="fas fa-piggy-bank mr-1"></i> Économisez {{ $fmtSave }}
                                </div>
                            @else
                                {{-- Prix normal --}}
                                <div class="text-2xl font-bold text-gray-900">
                                    @if($hasRange)
                                        {{ $fmtMin }} - {{ $fmtMax }}
                                    @else
                                        {{ $fmtMin }}
                                    @endif
                                </div>
                            @endif
                            
                            {{-- Bouton Choisir --}}
                            <a href="{{ route('tours.booking.wizard', ['tour' => $tour->id, 'pricing' => $pricing->id]) }}" 
                               class="inline-block mt-3 px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-sm rounded-lg hover:from-purple-700 hover:to-pink-600 transition transform hover:scale-105 shadow-md">
                                Choisir
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 text-center text-gray-500">
                <p>Tarifs non disponibles pour le moment</p>
            </div>
        @endforelse
    </div>
</div>

