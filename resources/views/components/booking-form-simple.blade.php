@props(['tour', 'activePromotion' => null, 'showPrice' => true])

@php
    $currentCurrency = \App\Helpers\CurrencyHelper::current();
    $currencySymbol = $currentCurrency?->symbol ?: '€';
    $primaryColor = primary_color();
    
    // Check if tour has any active pricings
    $hasActivePricings = $tour->pricings()
        ->where('is_active', true)
        ->where(function($query) {
            $query->whereHas('groupPrices')
                  ->orWhereHas('privatePrices');
        })
        ->exists();
    
    // Get default pricing for display
    $defaultPricing = $tour->defaultPricing();
    $basePrice = null;
    $promoPrice = null;
    
    if ($defaultPricing && !$defaultPricing->requiresConsultation()) {
        $basePrice = (float)$defaultPricing->price_min;
        if ($activePromotion) {
            $promoPrice = (float)$activePromotion->calculateDiscountedPrice($basePrice);
        }
    }
    
    $displayPrice = $promoPrice ?? $basePrice;
    $formattedPrice = $displayPrice ? \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($displayPrice)) : __('Consult');
@endphp

@if(!$hasActivePricings)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Pricing not configured') }}</h3>
        </div>
    </div>
@else
<div id="booking-form-container" class="bg-white rounded-xl shadow-lg border border-gray-200 p-6" 
     x-data="simpleBookingForm({{ $tour->id }})"
     style="background: linear-gradient(to bottom, #ffffff 0%, #f9fafb 100%);">
    
    @if($showPrice)
        <div class="mb-6 pb-6 border-b border-gray-200">
            <x-booking.partials.price-display :tour="$tour" :activePromotion="$activePromotion" />
        </div>
    @endif

    <!-- Date Field -->
    <div id="booking-date-field" class="mb-6 relative" @click.away="showDateDropdown = false">
        <button type="button" 
                @click="showDateDropdown = !showDateDropdown"
                class="w-full flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition-all bg-white text-left">
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-900" x-text="selectedDate ? formatDate(selectedDate) : @js(__('Select a date'))"></div>
                <div class="text-xs text-gray-500 mt-1" x-show="selectedDate" x-text="getDateDayName(selectedDate)"></div>
            </div>
            <i class="fas fa-calendar-alt text-gray-400"></i>
        </button>

        <!-- Date Picker Dropdown -->
        <div x-show="showDateDropdown" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 p-2"
             style="transform-origin: top; z-index: 50;">
            
            <!-- Month Navigation -->
            <div class="flex items-center justify-between mb-2">
                <button type="button" 
                        @click="previousMonth()"
                        :disabled="isPastMonth()"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                        :style="!isPastMonth() ? 'color: {{ $primaryColor }};' : ''">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <div class="text-sm font-bold text-gray-900" x-text="getCurrentMonthYear()"></div>
                <button type="button" 
                        @click="nextMonth()"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                        style="color: {{ $primaryColor }};">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>

            <!-- Day Names -->
            <div class="grid grid-cols-7 gap-0.5 mb-1">
                <template x-for="day in [@js(__('Mon')), @js(__('Tue')), @js(__('Wed')), @js(__('Thu')), @js(__('Fri')), @js(__('Sat')), @js(__('Sun'))]" :key="day">
                    <div class="text-center text-xs font-semibold text-gray-600 py-0.5" x-text="day"></div>
                </template>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-0.5">
                <template x-for="(day, index) in calendarDays" :key="day.key || `${day.date}-${index}`">
                    <button type="button"
                            @click="handleDateClick(day)"
                            :disabled="day.disabled"
                            class="aspect-square p-0.5 rounded text-xs font-medium transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                            :class="{
                                'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 active:bg-gray-300': day.isOtherMonth && !day.disabled && !day.isSelected,
                                'bg-gray-200 text-gray-500 cursor-not-allowed': day.disabled,
                                'hover:bg-gray-100 text-gray-600 cursor-pointer': !day.isSelected && !day.disabled && !day.isOtherMonth,
                                'bg-white text-gray-900 border-2': day.isToday && !day.isSelected && !day.disabled && !day.isOtherMonth
                            }"
                            :style="day.isSelected ? 'background-color: {{ $primaryColor }}; color: white;' : (day.isOtherMonth && !day.disabled ? 'cursor: pointer !important;' : '')">
                        <span x-text="day.day"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Participants Field (Opens Dropdown) -->
    <div class="mb-6 relative" @click.away="showParticipantsDropdown = false">
        <button type="button" 
                @click="showParticipantsDropdown = !showParticipantsDropdown"
                class="w-full flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition-all bg-white text-left">
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-900" x-text="getParticipantsSummary()"></div>
                <div class="text-xs text-gray-500 mt-1" x-text="getParticipantsDetail()"></div>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="showParticipantsDropdown ? 'rotate-180' : ''"></i>
        </button>

        <!-- Participants Dropdown Content -->
        <div x-show="showParticipantsDropdown" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute w-full bottom-full mb-2 bg-white rounded-lg shadow-xl border border-gray-200 p-4"
             style="transform-origin: bottom; z-index: 40;">
            
            <!-- Adults -->
            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 text-sm">{{ __('Adults') }}</div>
                    <div class="text-xs text-gray-500">{{ __('13 years and older') }}</div>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            @click="if(adults > 1) adults--;"
                            :disabled="adults <= 1"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="adults > 1 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-10 text-center font-bold text-base text-gray-900" x-text="adults"></span>
                    <button type="button" 
                            @click="adults++;"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                            style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Children -->
            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 text-sm">{{ __('Children') }}</div>
                    <div class="text-xs text-gray-500">{{ __('2 to 12 years') }}</div>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            @click="if(children > 0) children--;"
                            :disabled="children <= 0"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="children > 0 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-10 text-center font-bold text-base text-gray-900" x-text="children"></span>
                    <button type="button" 
                            @click="children++;"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                            style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Infants -->
            <div class="flex items-center justify-between py-3">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 text-sm">{{ __('Babies') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Under 2 years') }}</div>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            @click="if(infants > 0) infants--;"
                            :disabled="infants <= 0"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="infants > 0 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-10 text-center font-bold text-base text-gray-900" x-text="infants"></span>
                    <button type="button" 
                            @click="infants++;"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                            style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Check Availability Button -->
    <button type="button" 
            @click="validateStep1()"
            :disabled="!selectedDate || totalParticipants < 1"
            class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:opacity-90 font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50"
            style="background-color: {{ $primaryColor }};">
        <i class="fas fa-search mr-2"></i>
        {{ __('Check Availability') }}
    </button>
</div>
@endif

