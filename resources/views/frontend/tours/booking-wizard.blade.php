@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .text-primary { color: {{ primary_color() }}; }
    .bg-primary { background-color: {{ primary_color() }}; }
    .border-primary { border-color: {{ primary_color() }}; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    
    .step-active {
        background: {{ primary_color() }};
        transform: scale(1.1);
    }
    
    .flatpickr-day.selected {
        background: {{ primary_color() }} !important;
        border-color: {{ primary_color() }} !important;
    }
    
    .flatpickr-months {
        background: {{ primary_color() }} !important;
    }
</style>
@endpush

@php
    $currentCurrency = \App\Helpers\CurrencyHelper::current();
    $currencySymbol = $currentCurrency?->symbol ?: '';
@endphp

<x-app-layout>
    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12" x-data="bookingWizard({{ isset($selectedPricingId) ? 2 : 1 }})">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Réservation : {{ translate_model($tour, 'title') }}
                </h1>
                <p class="text-gray-600">Finalisez votre réservation en quelques étapes simples</p>
            </div>

            {{-- Progress Steps --}}
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex items-center justify-between">
                    @foreach([1 => 'Formule', 2 => 'Date & Participants', 3 => 'Informations', 4 => 'Confirmation'] as $stepNum => $stepName)
                        <div class="flex-1 text-center">
                            <div class="relative">
                                <div class="w-10 h-10 md:w-12 md:h-12 mx-auto rounded-full flex items-center justify-center text-white font-bold transition-all duration-300"
                                     :class="currentStep >= {{ $stepNum }} ? 'step-active shadow-lg' : 'bg-gray-300'">
                                    {{ $stepNum }}
                                </div>
                                <div class="mt-2 text-xs md:text-sm font-semibold"
                                     :class="currentStep >= {{ $stepNum }} ? 'text-primary' : 'text-gray-400'">
                                    {{ $stepName }}
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-2 transition-colors duration-300"
                                 :class="currentStep >= {{ $stepNum + 1 }} ? 'bg-primary' : 'bg-gray-300'"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <form action="{{ route('bookings.store', $tour) }}" method="POST" id="bookingWizardForm">
                @csrf
                
                <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                    {{-- STEP 1: Choisir la Formule --}}
                    <div x-show="currentStep === 1" x-transition class="p-6 md:p-8">
                        <h2 class="font-poppins text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-tags text-primary mr-3 text-3xl"></i>
                            Choisissez votre formule
                        </h2>

                        @if($activePromotion)
                            <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white p-4 rounded-lg mb-6 shadow-md">
                                <div class="flex items-center justify-between flex-wrap gap-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-fire text-2xl"></i>
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
                                    <div class="bg-white text-red-600 px-4 py-2 rounded-full font-bold text-xl">
                                        {{ $activePromotion->discount_text }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @forelse($tour->pricings()->active()->get() as $pricing)
                                @php
                                    $originalPrice = (float)$pricing->price_min;
                                    $discountedPrice = $activePromotion ? (float)$activePromotion->calculateDiscountedPrice($originalPrice) : $originalPrice;
                                    $finalPrice = $discountedPrice;
                                    
                                    // Calculer les prix enfants et bébés
                                    $childPrice = $pricing->getChildPrice();
                                    $infantPrice = $pricing->getInfantPrice();
                                    
                                    // Appliquer la promotion si active
                                    if ($activePromotion) {
                                        $childPrice = (float)$activePromotion->calculateDiscountedPrice($childPrice);
                                        if ($infantPrice > 0) {
                                            $infantPrice = (float)$activePromotion->calculateDiscountedPrice($infantPrice);
                                        }
                                    }
                                    
                                    // Conversion devise
                                    $originalPriceConverted = \App\Helpers\CurrencyHelper::convert($originalPrice);
                                    $discountedPriceConverted = \App\Helpers\CurrencyHelper::convert($discountedPrice);
                                    $childPriceConverted = \App\Helpers\CurrencyHelper::convert($childPrice);
                                    $infantPriceConverted = \App\Helpers\CurrencyHelper::convert($infantPrice);
                                    $savingsConverted = $originalPriceConverted - $discountedPriceConverted;
                                    
                                    $fmtOrig = \App\Helpers\CurrencyHelper::format($originalPriceConverted);
                                    $fmtDisc = \App\Helpers\CurrencyHelper::format($discountedPriceConverted);
                                    $fmtSave = \App\Helpers\CurrencyHelper::format($savingsConverted);
                                @endphp
                                
                                <label class="block cursor-pointer">
                                    <input type="radio" name="pricing_id" value="{{ $pricing->id }}" 
                                           data-price="{{ $discountedPriceConverted }}"
                                           data-child-price="{{ $childPriceConverted }}"
                                           data-infant-price="{{ $infantPriceConverted }}"
                                           data-child-discount="{{ $pricing->child_discount_percentage }}"
                                           data-min="{{ $pricing->min_participants }}"
                                           data-max="{{ $pricing->max_participants }}"
                                           data-name="{{ $pricing->name }}"
                                           class="hidden pricing-radio"
                                           @change="selectedPricing = $event.target; updatePrice()"
                                           {{ isset($selectedPricingId) && $selectedPricingId == $pricing->id ? 'checked' : '' }}
                                           required>
                                    <div class="border-2 rounded-xl p-5 md:p-6 transition-all duration-200 hover:shadow-lg pricing-option"
                                         :class="selectedPricing && selectedPricing.value == '{{ $pricing->id }}' ? 'border-primary bg-primary/5 shadow-md' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="flex items-start justify-between flex-wrap gap-4">
                                            <div class="flex-1 min-w-[200px]">
                                                <div class="flex items-center space-x-2 mb-2 flex-wrap">
                                                    <h3 class="text-lg md:text-xl font-bold text-gray-900">{{ $pricing->name }}</h3>
                                                    @if($pricing->is_default)
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                                                            <i class="fas fa-star mr-1"></i> Populaire
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($pricing->description)
                                                    <p class="text-gray-600 text-sm mb-3">{{ $pricing->description }}</p>
                                                @endif
                                                
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <i class="fas fa-users mr-2"></i>
                                                    {{ $pricing->min_participants }}{{ $pricing->max_participants ? '-'.$pricing->max_participants : '+' }} participants
                                                </div>
                                            </div>
                                            
                                            <div class="text-right">
                                                @if($activePromotion && $discountedPrice < $originalPrice)
                                                    <div class="text-sm text-gray-500 line-through mb-1">{{ $fmtOrig }}</div>
                                                    <div class="text-2xl md:text-3xl font-bold text-green-600">{{ $fmtDisc }}</div>
                                                    <div class="text-xs text-green-700 font-semibold mt-1">
                                                        <i class="fas fa-piggy-bank mr-1"></i> Économisez {{ $fmtSave }}
                                                    </div>
                                                @else
                                                    <div class="text-2xl md:text-3xl font-bold text-primary">
                                                        @if($pricing->price_max && $pricing->price_max != $pricing->price_min)
                                                            @php
                                                                $pmin = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert((float)$pricing->price_min));
                                                                $pmax = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert((float)$pricing->price_max));
                                                            @endphp
                                                            {{ $pmin }} - {{ $pmax }}
                                                        @else
                                                            {{ $fmtDisc }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-lg">
                                    <i class="fas fa-info-circle text-4xl mb-3 text-gray-400"></i>
                                    <p>Aucun tarif disponible pour le moment.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- STEP 2: Date & Participants --}}
                    <div x-show="currentStep === 2" x-transition class="p-6 md:p-8">
                        <h2 class="font-poppins text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-calendar-check text-primary mr-3 text-3xl"></i>
                            Date & Participants
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            {{-- Date Selection --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    <span class="text-red-500">*</span> Choisissez votre date
                                </label>
                                
                                <div class="bg-gray-50 rounded-xl p-4 border-2 border-gray-200">
                                    <input type="text" 
                                           id="date-picker-free" 
                                           name="preferred_date"
                                           class="w-full border-0 text-center text-xl font-bold text-primary focus:ring-0 bg-transparent" 
                                           placeholder="📅 Cliquez ici"
                                           readonly
                                           required>
                                </div>
                                
                                <div id="date-selected-info" class="hidden mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center justify-center space-x-2">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                        <p class="text-sm font-semibold text-green-700">
                                            Date sélectionnée : <span id="selected-date-display"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Travelers Selection --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    <span class="text-red-500">*</span> Nombre de voyageurs
                                </label>
                                <p class="text-xs text-gray-500 mb-4">Sélectionnez jusqu'à 15 voyageurs au total</p>
                                
                                {{-- Adult (Age 13-99) --}}
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-semibold text-gray-700">
                                            <i class="fas fa-user mr-1"></i>Adulte (13-99 ans)
                                        </label>
                                        <span class="text-xs text-gray-500">Min: 1, Max: 15</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" @click="decreaseAdults" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            −
                                        </button>
                                        <input type="number" name="adults" x-model="adults" 
                                               min="1" max="15"
                                               class="w-20 text-center text-lg font-semibold border-2 border-gray-300 rounded-lg py-2 focus:border-primary focus:ring-2 focus:ring-primary" 
                                               required>
                                        <button type="button" @click="increaseAdults" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            +
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Child (Age 3-12) --}}
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-semibold text-gray-700">
                                            <i class="fas fa-child mr-1"></i>Enfant (3-12 ans)
                                        </label>
                                        <span class="text-xs text-gray-500">Min: 0, Max: 15</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" @click="decreaseChildren" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            −
                                        </button>
                                        <input type="number" name="children" x-model="children" 
                                               min="0" max="15"
                                               class="w-20 text-center text-lg font-semibold border-2 border-gray-300 rounded-lg py-2 focus:border-primary focus:ring-2 focus:ring-primary">
                                        <button type="button" @click="increaseChildren" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            +
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Infant (Age 0-2) --}}
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-semibold text-gray-700">
                                            <i class="fas fa-baby mr-1"></i>Bébé (0-2 ans)
                                        </label>
                                        <span class="text-xs text-gray-500">Min: 0, Max: 15</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" @click="decreaseInfants" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            −
                                        </button>
                                        <input type="number" name="infants" x-model="infants" 
                                               min="0" max="15"
                                               class="w-20 text-center text-lg font-semibold border-2 border-gray-300 rounded-lg py-2 focus:border-primary focus:ring-2 focus:ring-primary">
                                        <button type="button" @click="increaseInfants" 
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-lg transition-colors flex items-center justify-center">
                                            +
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Total travelers display --}}
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-700">Total voyageurs:</span>
                                        <span class="text-lg font-bold text-primary" x-text="totalTravelers"></span>
                                    </div>
                                    <p class="text-xs text-red-600 mt-1" x-show="totalTravelers > 15">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Maximum 15 voyageurs au total
                                    </p>
                                </div>
                                
                                {{-- Hidden field for backward compatibility --}}
                                <input type="hidden" name="seats" :value="totalTravelers">
                            </div>
                        </div>

                        {{-- Price Breakdown --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                            <h3 class="font-poppins text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calculator text-primary mr-2"></i>
                                Détail du Prix
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-700">
                                    <span>Formule:</span>
                                    <span class="font-semibold" x-text="selectedPricingName || 'Non sélectionnée'"></span>
                                </div>
                                
                                <div class="flex justify-between text-gray-700">
                                    <span>Prix par personne:</span>
                                    <span class="font-semibold" x-text="formatPrice(pricePerPerson)"></span>
                                </div>
                                
                                <div class="flex justify-between text-gray-700">
                                    <span>Participants:</span>
                                    <span class="font-semibold" x-text="participants + ' personne(s)'"></span>
                                </div>

                                @if($activePromotion)
                                    <div class="flex justify-between text-green-600 text-sm font-semibold">
                                        <span><i class="fas fa-tag mr-1"></i> Promotion:</span>
                                        <span>{{ $activePromotion->discount_text }}</span>
                                    </div>
                                @endif
                                
                                <div class="border-t-2 border-gray-300 pt-3 mt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xl font-bold text-gray-900">Total:</span>
                                        <span class="text-3xl font-extrabold text-primary" x-text="formatPrice(totalPrice)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: Vos Informations --}}
                    <div x-show="currentStep === 3" x-transition class="p-6 md:p-8">
                        <h2 class="font-poppins text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user text-primary mr-3 text-3xl"></i>
                            Vos Informations
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Nom complet
                                </label>
                                <input type="text" name="guest_name" value="{{ old('guest_name', auth('client')->user()->name ?? '') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="Jean Dupont" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Email
                                </label>
                                <input type="email" name="guest_email" value="{{ old('guest_email', auth('client')->user()->email ?? '') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="jean@email.com" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Téléphone
                                </label>
                                <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="+33 6 12 34 56 78" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Commentaires ou demandes spéciales (optionnel)
                                </label>
                                <textarea name="comments" rows="4" 
                                          class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                          placeholder="Ex: allergies, besoins spéciaux, questions..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 4: Confirmation --}}
                    <div x-show="currentStep === 4" x-transition class="p-6 md:p-8">
                        <h2 class="font-poppins text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3 text-3xl"></i>
                            Récapitulatif
                        </h2>

                        <div class="space-y-6">
                            {{-- Tour Info --}}
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                                <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-map-marked-alt text-primary mr-2"></i>
                                    Tour sélectionné
                                </h3>
                                <div class="flex items-start space-x-4">
                                    @if($tour->primaryImage)
                                        <img src="{{ Storage::url($tour->primaryImage->path) }}" alt="{{ $tour->title }}" class="w-20 h-20 rounded-lg object-cover">
                                    @endif
                                    <div>
                                        <div class="font-bold text-lg text-gray-900">{{ translate_model($tour, 'title') }}</div>
                                        <div class="text-gray-600 text-sm mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ translate_model($tour, 'location') }}
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            <i class="far fa-clock mr-1"></i>{{ translate_model($tour, 'duration') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Booking Summary --}}
                            <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Détails de la réservation</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">Formule:</span>
                                        <span class="font-semibold" x-text="selectedPricingName || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">Date:</span>
                                        <span class="font-semibold" id="summary-date">-</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">Voyageurs:</span>
                                        <span class="font-semibold" x-text="totalTravelers + ' personne(s)'"></span>
                                    </div>
                                    <div class="text-xs text-gray-500 pl-2 pb-2 border-b border-gray-200" x-show="totalTravelers > 0">
                                        <div x-show="adults > 0" x-text="adults + ' Adulte(s)'"></div>
                                        <div x-show="children > 0" x-text="children + ' Enfant(s)'"></div>
                                        <div x-show="infants > 0" x-text="infants + ' Bébé(s)'"></div>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">Prix unitaire:</span>
                                        <span class="font-semibold" x-text="formatPrice(pricePerPerson)"></span>
                                    </div>
                                    @if($activePromotion)
                                        <div class="flex justify-between py-2 border-b border-gray-200 text-green-600">
                                            <span>Promotion:</span>
                                            <span class="font-bold">{{ $activePromotion->discount_text }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between py-4 bg-green-50 px-4 rounded-lg mt-4">
                                        <span class="text-xl font-bold text-gray-900">Total à payer:</span>
                                        <span class="text-3xl font-extrabold text-green-600" x-text="formatPrice(totalPrice)"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Terms --}}
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" id="terms" name="terms" required class="mt-1 w-5 h-5">
                                    <label for="terms" class="text-sm text-gray-700">
                                        <span class="text-red-500">*</span> J'accepte les <a href="#" class="text-primary underline font-semibold">conditions générales</a> et la <a href="#" class="text-primary underline font-semibold">politique de confidentialité</a>. Je comprends que cette réservation est soumise à confirmation.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="bg-gray-50 px-6 md:px-8 py-6 flex items-center justify-between border-t flex-wrap gap-4">
                        <button type="button" @click="previousStep" x-show="currentStep > 1"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Précédent
                        </button>
                        
                        <div class="text-sm text-gray-600 font-medium" x-show="currentStep < 4">
                            Étape <span x-text="currentStep"></span> sur 4
                        </div>
                        
                        <button type="button" @click="nextStep" x-show="currentStep < 4"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 font-semibold transition-all shadow-md"
                                style="background-color: {{ primary_color() }};">
                            Suivant <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        
                        <button type="submit" x-show="currentStep === 4"
                                class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-lg shadow-lg transition-all">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmer la Réservation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Alpine.js Logic --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingWizard', (initialStep = 1) => ({
                currentStep: initialStep,
                selectedPricing: null,
                selectedPricingName: '',
                pricePerPerson: 0,
                participants: 1,
                adults: 1,
                children: 0,
                infants: 0,
                minParticipants: 1,
                maxParticipants: {{ $tour->capacity }},
                totalPrice: 0,
                currencySymbol: '{{ $currencySymbol }}',
                
                get totalTravelers() {
                    return this.adults + this.children + this.infants;
                },

                init() {
                    @if(isset($selectedPricingId))
                        const preselectedRadio = document.querySelector('input[name="pricing_id"]:checked');
                        if (preselectedRadio) {
                            this.selectedPricing = preselectedRadio;
                            this.updatePrice();
                        }
                    @endif

                    this.$watch('selectedPricing', () => {
                        if (this.selectedPricing) {
                            this.updatePrice();
                        }
                    });

                    this.$watch('participants', () => {
                        this.calculateTotal();
                    });
                    
                    this.$watch('totalTravelers', () => {
                        this.participants = this.totalTravelers;
                        this.calculateTotal();
                    });
                },

                updatePrice() {
                    if (this.selectedPricing) {
                        this.pricePerPerson = parseFloat(this.selectedPricing.dataset.price);
                        this.minParticipants = parseInt(this.selectedPricing.dataset.min);
                        this.maxParticipants = this.selectedPricing.dataset.max ? parseInt(this.selectedPricing.dataset.max) : 999;
                        this.selectedPricingName = this.selectedPricing.dataset.name;
                        this.participants = Math.max(this.minParticipants, this.participants);
                        this.calculateTotal();
                    }
                },

                calculateTotal() {
                    if (!this.selectedPricing) {
                        this.totalPrice = 0;
                        return;
                    }
                    
                    const adultPrice = parseFloat(this.selectedPricing.dataset.price) || 0;
                    const childPrice = parseFloat(this.selectedPricing.dataset.childPrice) || adultPrice;
                    const infantPrice = parseFloat(this.selectedPricing.dataset.infantPrice) || 0;
                    
                    // Calculer le total : (adultes × prix adulte) + (enfants × prix enfant) + (bébés × prix bébé)
                    this.totalPrice = (this.adults * adultPrice) + (this.children * childPrice) + (this.infants * infantPrice);
                    
                    // Mettre à jour le prix par personne pour l'affichage
                    if (this.totalTravelers > 0) {
                        this.pricePerPerson = this.totalPrice / this.totalTravelers;
                    } else {
                        this.pricePerPerson = adultPrice;
                    }
                },

                formatPrice(price) {
                    if (!price || price === 0) return '-';
                    return price.toFixed(2).replace('.', ',') + ' ' + this.currencySymbol;
                },

                increaseParticipants() {
                    if (this.participants < this.maxParticipants) {
                        this.participants++;
                    }
                },

                decreaseParticipants() {
                    if (this.participants > this.minParticipants) {
                        this.participants--;
                    }
                },
                
                increaseAdults() {
                    if (this.adults < 15 && this.totalTravelers < 15) {
                        this.adults++;
                    }
                },
                
                decreaseAdults() {
                    if (this.adults > 1) {
                        this.adults--;
                    }
                },
                
                increaseChildren() {
                    if (this.children < 15 && this.totalTravelers < 15) {
                        this.children++;
                    }
                },
                
                decreaseChildren() {
                    if (this.children > 0) {
                        this.children--;
                    }
                },
                
                increaseInfants() {
                    if (this.infants < 15 && this.totalTravelers < 15) {
                        this.infants++;
                    }
                },
                
                decreaseInfants() {
                    if (this.infants > 0) {
                        this.infants--;
                    }
                },

                nextStep() {
                    if (this.currentStep === 1 && !this.selectedPricing) {
                        alert('Veuillez sélectionner une formule');
                        return;
                    }
                    
                    if (this.currentStep === 2) {
                        const dateInput = document.getElementById('date-picker-free');
                        if (dateInput && !dateInput.value) {
                            alert('Veuillez sélectionner une date');
                            return;
                        }
                        
                        // Validation des voyageurs
                        if (this.totalTravelers > 15) {
                            alert('Le total des voyageurs ne peut pas dépasser 15.');
                            return;
                        }
                        
                        if (this.adults < 1) {
                            alert('Au moins 1 adulte est requis.');
                            return;
                        }
                        
                        const summaryDate = document.getElementById('summary-date');
                        if (summaryDate && dateInput && dateInput.value) {
                            summaryDate.textContent = dateInput.value;
                        }
                    }
                    
                    if (this.currentStep === 3) {
                        const name = document.querySelector('input[name="guest_name"]').value;
                        const email = document.querySelector('input[name="guest_email"]').value;
                        const phone = document.querySelector('input[name="guest_phone"]').value;
                        
                        if (!name || !email || !phone) {
                            alert('Veuillez remplir tous les champs obligatoires');
                            return;
                        }
                    }
                    
                    if (this.currentStep < 4) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                previousStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            }))
        });
    </script>

    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date-picker-free", {
                locale: "fr",
                minDate: "today",
                dateFormat: "d/m/Y",
                inline: false,
                disableMobile: true,
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        document.getElementById('selected-date-display').textContent = dateStr;
                        document.getElementById('date-selected-info').classList.remove('hidden');
                    }
                }
            });
        });
    </script>
</x-app-layout>
