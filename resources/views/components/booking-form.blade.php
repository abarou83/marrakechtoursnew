@props(['tour'])

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
@endphp

@if(!$hasActivePricings)
    <!-- No Pricing Configured Message -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Pricing not configured') }}</h3>
        </div>
    </div>
@else
<div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6" 
     x-data="bookingForm({{ $tour->id }})"
     style="background: linear-gradient(to bottom, #ffffff 0%, #f9fafb 100%);">
    
    <!-- Price Display Header -->
    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="flex items-baseline justify-between mb-2">
            <div>
                <div class="text-sm text-gray-500 mb-1">{{ __('From') }}</div>
                <div class="text-3xl font-bold text-gray-900" x-show="!loading && priceData">
                    <span x-text="priceData ? formatPrice(priceData.base_price) : '...'"></span>
                </div>
                <div class="text-3xl font-bold text-gray-900" x-show="loading">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </div>
                <div class="text-sm text-gray-500 mt-1" x-show="priceData && pricingMode === 'group'">
                    {{ __('per person') }}
                </div>
                <div class="text-sm text-gray-500 mt-1" x-show="priceData && pricingMode === 'private'">
                    {{ __('per group') }}
                </div>
            </div>
        </div>
        
        <!-- Season Badge -->
        <div x-show="priceData && priceData.season" class="mt-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary">
                <i class="fas fa-calendar-alt mr-1"></i>
                <span x-text="priceData && priceData.season ? priceData.season.charAt(0).toUpperCase() + priceData.season.slice(1) + ' Season' : ''"></span>
            </span>
        </div>
    </div>

    <!-- Pricing Mode Selector -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('Booking Type') }}</label>
        <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                   :style="pricingMode === 'group' ? 'border-color: {{ $primaryColor }}; background-color: {{ $primaryColor }}20;' : 'border-color: #e5e7eb;'"
                   :class="pricingMode === 'group' ? '' : 'hover:border-gray-300'">
                <input type="radio" 
                       name="pricing_mode" 
                       value="group" 
                       x-model="pricingMode" 
                       @change="onPricingModeChange()" 
                       class="sr-only">
                <div class="text-center">
                    <i class="fas fa-users text-lg mb-1 block" 
                       :style="pricingMode === 'group' ? 'color: {{ $primaryColor }};' : 'color: #9ca3af;'"></i>
                    <span class="text-sm font-medium" 
                          :style="pricingMode === 'group' ? 'color: {{ $primaryColor }};' : 'color: #4b5563;'">{{ __('Group') }}</span>
                    <div class="text-xs text-gray-500 mt-0.5">{{ __('Per person') }}</div>
                </div>
            </label>
            <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                   :style="pricingMode === 'private' ? 'border-color: {{ $primaryColor }}; background-color: {{ $primaryColor }}20;' : 'border-color: #e5e7eb;'"
                   :class="pricingMode === 'private' ? '' : 'hover:border-gray-300'">
                <input type="radio" 
                       name="pricing_mode" 
                       value="private" 
                       x-model="pricingMode" 
                       @change="onPricingModeChange()" 
                       class="sr-only">
                <div class="text-center">
                    <i class="fas fa-user-lock text-lg mb-1 block" 
                       :style="pricingMode === 'private' ? 'color: {{ $primaryColor }};' : 'color: #9ca3af;'"></i>
                    <span class="text-sm font-medium" 
                          :style="pricingMode === 'private' ? 'color: {{ $primaryColor }};' : 'color: #4b5563;'">{{ __('Private') }}</span>
                    <div class="text-xs text-gray-500 mt-0.5">{{ __('Per group') }}</div>
                </div>
            </label>
        </div>
    </div>

    <!-- Date Picker Button (Opens Modal) -->
    <div class="mb-6 relative">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-calendar-alt mr-2" style="color: {{ $primaryColor }};"></i>{{ __('Departure date') }} *
        </label>
        <button type="button" 
                @click="showDateModal = !showDateModal"
                class="w-full flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition-all bg-white text-left">
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-900" x-text="selectedDate ? formatDate(selectedDate) : @js(__('Select a date'))"></div>
                <div class="text-xs text-gray-500 mt-1" x-show="selectedDate" x-text="getDateDayName(selectedDate)"></div>
            </div>
            <i class="fas fa-calendar-alt text-gray-400"></i>
        </button>
        <input type="hidden" 
               id="booking_date" 
               x-model="selectedDate" 
               required>

        <!-- Date Picker Dropdown (Above the field) -->
        <div x-show="showDateModal" 
             x-cloak
             @click.away="showDateModal = false"
             @keydown.escape.window="showDateModal = false"
             @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
             style="display: none;">
            
            <!-- Calendar Content -->
            <div class="p-4" @click.stop>
                <!-- Month Navigation -->
                <div class="flex items-center justify-between mb-4">
                    <button type="button" 
                            @click="previousMonth()"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chevron-left" style="color: {{ $primaryColor }};"></i>
                    </button>
                    <div class="text-base font-bold text-gray-900" x-text="getCurrentMonthYear()"></div>
                    <button type="button" 
                            @click="nextMonth()"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chevron-right" style="color: {{ $primaryColor }};"></i>
                    </button>
                </div>

                <!-- Day Names -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="day in [@js(__('Mon')), @js(__('Tue')), @js(__('Wed')), @js(__('Thu')), @js(__('Fri')), @js(__('Sat')), @js(__('Sun'))]" :key="day">
                        <div class="text-center text-xs font-semibold text-gray-600 py-1" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="day in calendarDays" :key="day.date">
                        <div 
                                :data-date="day.date"
                                :data-disabled="day.disabled"
                                :data-other-month="day.isOtherMonth"
                                @click.stop="handleDateClick(day)"
                                @mousedown.stop
                                class="aspect-square p-1.5 rounded-lg text-xs font-medium transition-all select-none calendar-day"
                                :class="{
                                    'opacity-30 cursor-not-allowed': day.disabled,
                                    'pointer-events-none': day.disabled,
                                    'cursor-pointer': !day.disabled,
                                    'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 active:bg-gray-300': day.isOtherMonth && !day.disabled && !day.isSelected,
                                    'hover:bg-gray-100 text-gray-600': !day.isSelected && !day.disabled && !day.isOtherMonth,
                                    'bg-gray-200 text-gray-500': day.disabled,
                                    'bg-white text-gray-900 border-2': day.isToday && !day.isSelected && !day.disabled && !day.isOtherMonth
                                }"
                                :style="day.isSelected ? 'background-color: {{ $primaryColor }}; color: white;' : (day.isOtherMonth && !day.disabled ? 'color: #4b5563; cursor: pointer !important; pointer-events: auto !important;' : '')">
                            <span x-text="day.day"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Departure Time Selector -->
    <div class="mb-6" x-show="selectedDate && departureTimes.length > 0">
        <label for="departure_time" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-clock mr-2" style="color: {{ $primaryColor }};"></i>{{ __('Departure time') }} *
        </label>
        <select id="departure_time" 
                x-model="selectedTime"
                @change="calculatePrice()"
                class="w-full border-gray-300 rounded-lg shadow-sm transition-all py-2.5 px-4"
                style="focus:border-color: {{ $primaryColor }};"
                required>
            <option value="">@js(__('Select a time'))</option>
            <template x-for="time in departureTimes" :key="time.id">
                <option :value="time.id" 
                        :disabled="!time.is_available"
                        x-text="time.time + (time.is_available ? ' (' + time.available + ' ' + @js(__('spots')) + ')' : ' - ' + @js(__('Full')))">
                </option>
            </template>
        </select>
        <p class="text-xs text-gray-500 mt-1" x-show="departureTimes.length === 0 && selectedDate">
            {{ __('No time available for this date') }}
        </p>
    </div>

    <!-- Participants Selector Button (Opens Modal) -->
    <div x-show="pricingMode === 'group'" class="mb-6 relative">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-users mr-2" style="color: {{ $primaryColor }};"></i>{{ __('Participants') }} *
        </label>
        <button type="button" 
                @click="showParticipantsModal = !showParticipantsModal"
                class="w-full flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg hover:border-gray-400 transition-all bg-white text-left">
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-900" x-text="getParticipantsSummary()"></div>
                <div class="text-xs text-gray-500 mt-1" x-text="getParticipantsDetail()"></div>
            </div>
            <i class="fas fa-chevron-down text-gray-400" :class="showParticipantsModal ? 'transform rotate-180' : ''"></i>
        </button>

        <!-- Participants Dropdown (Above the field) -->
        <div x-show="showParticipantsModal" 
             x-cloak
             @click.away="showParticipantsModal = false"
             @keydown.escape.window="showParticipantsModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
             style="display: none;">
            
            <!-- Content -->
            <div class="p-4">
                <!-- Adults -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 text-sm">{{ __('Adults') }}</div>
                        <div class="text-xs text-gray-500">{{ __('13 years and older') }}</div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="if(adults > 1) adults--; calculatePrice();"
                                :disabled="adults <= 1"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="adults > 1 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-10 text-center font-bold text-base text-gray-900" x-text="adults"></span>
                        <button type="button" 
                                @click="adults++; calculatePrice();"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Children -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 text-sm">{{ __('Children') }}</div>
                        <div class="text-xs text-gray-500">{{ __('2 to 12 years') }}</div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="if(children > 0) children--; calculatePrice();"
                                :disabled="children <= 0"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="children > 0 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-10 text-center font-bold text-base text-gray-900" x-text="children"></span>
                        <button type="button" 
                                @click="children++; calculatePrice();"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Infants -->
                <div class="flex items-center justify-between py-4">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 text-sm">{{ __('Babies') }}</div>
                        <div class="text-xs text-gray-500">{{ __('Under 2 years') }}</div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="if(infants > 0) infants--; calculatePrice();"
                                :disabled="infants <= 0"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="infants > 0 ? 'border-color: {{ $primaryColor }}; color: {{ $primaryColor }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-10 text-center font-bold text-base text-gray-900" x-text="infants"></span>
                        <button type="button" 
                                @click="infants++; calculatePrice();"
                                class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Price Breakdown (if children or infants) -->
                <div x-show="(children > 0 || infants > 0) && priceData && priceData.base_breakdown" 
                     class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-xs font-semibold text-gray-700 mb-2">{{ __('Price detail:') }}</div>
                    <div class="space-y-1 text-xs">
                        <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.adults && priceData.base_breakdown.adults.quantity > 0">
                            <div class="flex justify-between text-gray-700">
                                <span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.adults ? priceData.base_breakdown.adults.quantity + ' ' + @js(__('Adult(s)')) : ''"></span>
                                <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.adults ? formatPrice(priceData.base_breakdown.adults.total) : ''"></span>
                            </div>
                        </template>
                        <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.children && priceData.base_breakdown.children.quantity > 0">
                            <div class="flex justify-between text-green-700">
                                <span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.children ? priceData.base_breakdown.children.quantity + ' ' + @js(__('Child(ren)')) : ''"></span>
                                <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.children ? formatPrice(priceData.base_breakdown.children.total) : ''"></span>
                            </div>
                        </template>
                        <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.infants && priceData.base_breakdown.infants.quantity > 0">
                            <div class="flex justify-between text-blue-700">
                                <span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.infants ? priceData.base_breakdown.infants.quantity + ' ' + @js(__('Baby(ies)')) : ''"></span>
                                <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.infants ? formatPrice(priceData.base_breakdown.infants.total) : ''"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Private Pricing Fields -->
    <div x-show="pricingMode === 'private'" class="mb-6">
        <label for="total_people" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-users mr-2" style="color: {{ $primaryColor }};"></i>{{ __('Number of people') }} *
        </label>
        <input type="number" 
               id="total_people" 
               x-model.number="totalPeople" 
               @change="calculatePrice()"
               min="1" 
               class="w-full border-gray-300 rounded-lg shadow-sm transition-all py-2.5 px-4" 
               style="focus:border-color: {{ $primaryColor }};"
               required>
    </div>


    <!-- Addons (Dynamic based on pricing mode) -->
    <div x-show="selectedDate && addons.length > 0" class="mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center mb-4">
                <div class="bg-gray-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-plus-circle text-lg" style="color: {{ $primaryColor }};"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Additional options') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Enhance your experience') }}</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <template x-for="addon in addons" :key="addon.id">
                    <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer transition-colors"
                           :class="addon.is_included ? 'border-green-200 bg-green-50' : 'border-gray-200 hover:bg-gray-50'">
                        <div class="flex items-center flex-1">
                            <input type="checkbox" 
                                   :value="addon.id"
                                   x-model="selectedAddons[addon.id]"
                                   :disabled="addon.is_required || addon.is_included"
                                   :checked="addon.is_required || addon.is_included"
                                   @change="calculatePrice()"
                                   class="rounded border-gray-300 focus:ring-primary mr-3"
                                   :style="addon.is_included ? '' : 'color: {{ $primaryColor }};'"
                                   :class="addon.is_included ? 'text-green-600' : ''">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 text-sm" x-text="addon.name"></div>
                                <div class="text-xs text-gray-500">
                                    <span x-show="addon.is_included" class="text-green-700 font-semibold">
                                        <i class="fas fa-gift mr-1"></i>{{ __('Included') }}
                                    </span>
                                    <span x-show="addon.is_required && !addon.is_included" class="text-red-600">
                                        {{ __('Required') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-sm font-semibold ml-3"
                             :class="addon.is_included ? 'text-green-600' : 'text-gray-700'"
                             x-text="addon.is_included ? @js(__('Included')) : getAddonPriceLabel(addon)">
                        </div>
                    </label>
                </template>
            </div>
        </div>
    </div>

    <!-- Price Breakdown (Collapsible) -->
    <div x-show="!loading && priceData" class="mb-6">
        <button type="button" 
                @click="showBreakdown = !showBreakdown"
                class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-sm font-semibold text-gray-700">
            <span>{{ __('Price detail') }}</span>
            <i class="fas" :class="showBreakdown ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </button>
        
        <div x-show="showBreakdown" 
             x-transition
             class="mt-3 p-4 bg-gray-50 rounded-lg space-y-2 text-sm">
            <!-- Base Price Breakdown -->
            <template x-if="priceData && priceData.base_breakdown">
                <div class="space-y-1">
                    <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.adults && priceData.base_breakdown.adults.quantity > 0">
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('Adults') }} (x<span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.adults ? priceData.base_breakdown.adults.quantity : ''"></span>)</span>
                            <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.adults ? formatPrice(priceData.base_breakdown.adults.total) : ''"></span>
                        </div>
                    </template>
                    <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.children && priceData.base_breakdown.children.quantity > 0">
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('Children') }} (x<span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.children ? priceData.base_breakdown.children.quantity : ''"></span>)</span>
                            <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.children ? formatPrice(priceData.base_breakdown.children.total) : ''"></span>
                        </div>
                    </template>
                    <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.infants && priceData.base_breakdown.infants.quantity > 0">
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('Babies') }} (x<span x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.infants ? priceData.base_breakdown.infants.quantity : ''"></span>)</span>
                            <span class="font-semibold" x-text="priceData && priceData.base_breakdown && priceData.base_breakdown.infants ? formatPrice(priceData.base_breakdown.infants.total) : ''"></span>
                        </div>
                    </template>
                    <template x-if="priceData && priceData.base_breakdown && priceData.base_breakdown.people">
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('Group') }} (<span x-text="priceData && priceData.base_breakdown ? priceData.base_breakdown.people : ''"></span> {{ __('people') }})</span>
                            <span class="font-semibold" x-text="priceData && priceData.base_breakdown ? formatPrice(priceData.base_breakdown.total) : ''"></span>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Add-ons -->
            <template x-if="priceData && priceData.addons && priceData.addons.length > 0">
                <div class="border-t pt-2 mt-2 space-y-1">
                    <div class="text-xs font-semibold text-gray-600 mb-1">{{ __('Additional options:') }}</div>
                    <template x-for="addon in priceData.addons" :key="addon.addon_id">
                        <div class="flex justify-between text-gray-700">
                            <span class="text-xs">
                                <span x-text="addon.addon_name"></span>
                                <span x-show="addon.pricing_type === 'per_person'" class="text-gray-500">
                                    (<span x-text="addon.quantity"></span> @js(__('pers.')))
                                </span>
                            </span>
                            <span class="font-semibold text-xs" x-text="formatPrice(addon.total_price)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-sm font-semibold border-t pt-1 mt-1" x-show="priceData && priceData.addons_total > 0">
                        <span>{{ __('Total options:') }}</span>
                        <span x-text="priceData ? formatPrice(priceData.addons_total) : ''"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Total Price Display -->
    <div x-show="!loading && priceData" 
         class="mb-6 p-4 rounded-lg border-2"
         style="background-color: {{ $primaryColor }}20; border-color: {{ $primaryColor }};">
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-gray-900">{{ __('Total:') }}</span>
            <span class="text-2xl font-bold" 
                  style="color: {{ $primaryColor }};"
                  x-text="priceData ? formatPrice(priceData.total_price) : ''"></span>
        </div>
    </div>

    <div x-show="loading" class="text-center py-4 mb-6">
        <i class="fas fa-spinner fa-spin text-xl" style="color: {{ $primaryColor }};"></i>
        <p class="text-sm text-gray-600 mt-2">{{ __('Calculating...') }}</p>
    </div>

    <div x-show="!loading && !priceData && hasCalculated" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-700 text-sm text-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ __('Unable to calculate price. Check your selection.') }}
        </p>
    </div>

    <!-- Book Button -->
    <button type="button" 
            @click="proceedToBooking()"
            :disabled="!priceData || loading || !selectedTime"
            class="w-full mt-6 px-6 py-4 bg-primary text-white rounded-lg hover:opacity-90 font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50"
            style="background-color: {{ $primaryColor }};">
        <i class="fas fa-calendar-check mr-2"></i>
        {{ __('Book now') }}
    </button>
    
    <!-- Security Badge -->
    <div class="mt-4 text-center">
        <p class="text-xs text-gray-500">
            <i class="fas fa-lock mr-1"></i>
            {{ __('Secure booking • Free cancellation') }}
        </p>
    </div>

    <!-- Hidden fields for form submission -->
    <input type="hidden" id="adults" :value="adults">
    <input type="hidden" id="children" :value="children">
    <input type="hidden" id="infants" :value="infants">
    <input type="hidden" id="participants" :value="adults + children + infants">
</div>
@endif

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingForm', (tourId) => ({
        tourId: tourId,
        pricingMode: 'group',
        selectedDate: '',
        selectedTime: '',
        departureTimes: [],
        adults: 1,
        children: 0,
        infants: 0,
        totalPeople: 1,
        selectedAddons: {},
        priceData: null,
        loading: false,
        hasCalculated: false,
        isInitializing: true,
        addons: [],
        showBreakdown: false,
        showParticipantsModal: false,
        showDateModal: false,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),

        init() {
            // Set minimum date to today
            const today = new Date();
            this.minDate = today.toISOString().split('T')[0];
            this.selectedDate = this.minDate;
            
            // Initialize calendar
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
            
            // Ensure minimum values
            if (this.adults < 1) {
                this.adults = 1;
            }
            if (this.totalPeople < 1) {
                this.totalPeople = 1;
            }
            
            // Load departure times and addons for initial date
            this.loadDepartureTimes();
            this.loadAddons();
            
            // Mark initialization as complete after a short delay
            setTimeout(() => {
                this.isInitializing = false;
            }, 500);
            
            // Don't calculate price automatically on init - wait for user interaction
            // Price will be calculated when user selects date, changes participants, or mode
        },

        get calendarDays() {
    const days = [];

    // Aujourd’hui (local time, minuit)
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Premier jour du mois courant
    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
    firstDayOfMonth.setHours(0, 0, 0, 0);

    // Calcul du lundi de la première semaine affichée
    const dayOfWeek = firstDayOfMonth.getDay(); // 0 = dimanche
    const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;

    const startDate = new Date(
        firstDayOfMonth.getFullYear(),
        firstDayOfMonth.getMonth(),
        firstDayOfMonth.getDate() + diffToMonday
    );
    startDate.setHours(0, 0, 0, 0);

    // Génération des 42 jours (6 semaines)
    for (let i = 0; i < 42; i++) {
        const date = new Date(
            startDate.getFullYear(),
            startDate.getMonth(),
            startDate.getDate() + i
        );
        date.setHours(0, 0, 0, 0);

        const dateStr = date.toISOString().split('T')[0];
        const isOtherMonth = date.getMonth() !== this.currentMonth;
        const isToday = date.getTime() === today.getTime();
        const isSelected = dateStr === this.selectedDate;
        
        // Comparaison de dates : un jour est désactivé seulement s'il est AVANT aujourd'hui
        const dateTimestamp = date.getTime();
        const todayTimestamp = today.getTime();
        const isDisabled = dateTimestamp < todayTimestamp;

        // Debug pour TOUS les jours de février (mois suivant)
        if (isOtherMonth && date.getMonth() === 1) {
            console.log('📅 Février jour:', dateStr, 'day:', date.getDate(), 'disabled:', isDisabled, 'dateTime:', dateTimestamp, 'todayTime:', todayTimestamp, 'comparison:', dateTimestamp < todayTimestamp);
        }
        
        // Debug général pour voir tous les jours générés
        if (i === 0 || i === 30 || i === 35) {
            console.log('Day', i, ':', dateStr, 'month:', date.getMonth(), 'isOtherMonth:', isOtherMonth, 'disabled:', isDisabled);
        }

        days.push({
            date: dateStr,
            day: date.getDate(),
            isOtherMonth: isOtherMonth,
            isToday: isToday,
            isSelected: isSelected,
            disabled: isDisabled
        });
    }

    return days;
},


        getCurrentMonthYear() {
            const months = [@js(__('January')), @js(__('February')), @js(__('March')), @js(__('April')), @js(__('May')), @js(__('June')), 
                          @js(__('July')), @js(__('August')), @js(__('September')), @js(__('October')), @js(__('November')), @js(__('December'))];
            return `${months[this.currentMonth]} ${this.currentYear}`;
        },

        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },

        handleDateClick(day) {
            console.log('=== handleDateClick CALLED ===');
            console.log('Day object:', day);
            console.log('Day date:', day.date);
            console.log('Day disabled:', day.disabled);
            console.log('Day isOtherMonth:', day.isOtherMonth);
            
            // Empêcher le clic si le jour est désactivé
            if (day.disabled) {
                console.log('❌ Day is disabled, cannot click');
                alert(@js(__('This day is disabled')) + ': ' + day.date);
                return false;
            }
            
            console.log('✅ Day is NOT disabled, proceeding with click');
            
            // Si c'est un jour du mois suivant/précédent, changer le mois affiché d'abord
            if (day.isOtherMonth) {
                console.log('🔄 Changing month for other month day');
                const selectedDateObj = new Date(day.date + 'T00:00:00');
                this.currentMonth = selectedDateObj.getMonth();
                this.currentYear = selectedDateObj.getFullYear();
                console.log('Changed month to:', this.currentMonth, 'year:', this.currentYear);
            }
            
            // Sélectionner la date
            this.selectedDate = day.date;
            console.log('✅ Selected date:', this.selectedDate);
            
            // Fermer le modal et déclencher le changement de date
            this.showDateModal = false;
            if (this.onDateChange) {
                this.onDateChange();
            }
            
            return true;
        },

        selectDate(date, isOtherMonth = false) {
            // Si c'est un jour du mois suivant/précédent, changer le mois affiché
            if (isOtherMonth) {
                const selectedDateObj = new Date(date + 'T00:00:00');
                this.currentMonth = selectedDateObj.getMonth();
                this.currentYear = selectedDateObj.getFullYear();
            }
            this.selectedDate = date;
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr + 'T00:00:00');
            const locale = @js(app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'));
            return date.toLocaleDateString(locale, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        getDateDayName(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr + 'T00:00:00');
            const locale = @js(app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'));
            return date.toLocaleDateString(locale, { weekday: 'long' });
        },

        async loadDepartureTimes() {
            if (!this.selectedDate) {
                this.departureTimes = [];
                this.selectedTime = '';
                return;
            }

            try {
                const response = await fetch(`/api/v1/tours/${this.tourId}/dates/${this.selectedDate}/times`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    this.departureTimes = result.times || [];
                    // Auto-select first available time
                    const firstAvailable = this.departureTimes.find(t => t.is_available);
                    if (firstAvailable) {
                        this.selectedTime = firstAvailable.id;
                        this.calculatePrice();
                    } else {
                        this.selectedTime = '';
                    }
                } else {
                    this.departureTimes = [];
                    this.selectedTime = '';
                }
            } catch (error) {
                console.error('Error loading departure times:', error);
                this.departureTimes = [];
                this.selectedTime = '';
            }
        },

        onDateChange() {
            this.selectedTime = '';
            this.loadDepartureTimes();
            // Load addons for the new date (season may change)
            this.loadAddons();
            // Calculate price when date changes (user interaction)
            if (this.selectedDate && !this.isInitializing) {
                setTimeout(() => {
                    this.calculatePrice();
                }, 200);
            }
        },

        async calculatePrice() {
            if (!this.selectedDate) {
                this.priceData = null;
                return;
            }

            // Validate minimum requirements
            if (this.pricingMode === 'group' && this.adults < 1) {
                this.priceData = null;
                return;
            }
            
            if (this.pricingMode === 'private' && this.totalPeople < 1) {
                this.priceData = null;
                return;
            }

            this.loading = true;
            this.hasCalculated = true;

            const data = {
                tour_id: this.tourId,
                date: this.selectedDate,
                pricing_mode: this.pricingMode,
                adults: this.pricingMode === 'group' ? this.adults : 0,
                children: this.pricingMode === 'group' ? this.children : 0,
                infants: this.pricingMode === 'group' ? this.infants : 0,
                selected_addons: this.selectedAddons,
            };

            if (this.pricingMode === 'private') {
                const total = this.totalPeople;
                data.adults = total;
                data.children = 0;
                data.infants = 0;
            }
            
            // Add tour_date_id if time is selected
            if (this.selectedTime) {
                data.tour_date_id = this.selectedTime;
            }

            try {
                const response = await fetch('/api/v1/calculate-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    this.priceData = result.data;
                } else {
                    this.priceData = null;
                    console.error('Price calculation error:', result.message);
                    
                    // Only show alert if user has interacted (not on silent init)
                    if (this.hasCalculated && !this.isInitializing) {
                        // Show user-friendly error message with link to admin if needed
                        let errorMsg = result.message || @js(__('Please check your selection'));
                        
                        // If error mentions admin, make it more user-friendly
                        if (errorMsg.includes('/admin/')) {
                            errorMsg = errorMsg.replace(/\/admin\/tours\/(\d+)\/pricings/g, '');
                            errorMsg += '\n\n' + @js(__('Please contact the administrator to configure pricing.'));
                        }
                        
                        alert(@js(__('Calculation error')) + ':\n' + errorMsg);
                    }
                }
            } catch (error) {
                console.error('Error calculating price:', error);
                this.priceData = null;
                // Only show alert if user has interacted
                if (this.hasCalculated && !this.isInitializing) {
                    alert(@js(__('Connection error. Please try again.')));
                }
            } finally {
                this.loading = false;
            }
        },

        async loadAddons() {
            if (!this.selectedDate || !this.pricingMode || !this.selectedPricingId) {
                this.addons = [];
                return;
            }

            try {
                const response = await fetch(`/api/v1/tours/${this.tourId}/pricing/${this.pricingMode}/addons?date=${this.selectedDate}&pricing_id=${this.selectedPricingId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                
                if (result.success && result.addons) {
                    this.addons = result.addons;
                    // Reset and pre-select required/included addons
                    this.selectedAddons = {};
                    this.addons.forEach(addon => {
                        if (addon.is_required || addon.is_included) {
                            this.selectedAddons[addon.id] = 1;
                        }
                    });
                    // Only calculate price if not initializing (user interaction)
                    if (!this.isInitializing) {
                        this.calculatePrice();
                    }
                } else {
                    this.addons = [];
                    this.selectedAddons = {};
                }
            } catch (error) {
                console.error('Error loading addons:', error);
                this.addons = [];
                this.selectedAddons = {};
            }
        },

        getAddonPriceLabel(addon) {
            if (addon.price === 0 || addon.is_included) return @js(__('FREE'));
            const price = this.formatPrice(addon.price);
            const type = addon.pricing_type === 'per_person' ? @js(__('pers.')) : @js(__('group'));
            return `+${price} / ${type}`;
        },

        onPricingModeChange() {
            // Reset selected addons when switching modes
            this.selectedAddons = {};
            // Load addons for the new pricing mode
            this.loadAddons();
            // Calculate price when mode changes (user interaction)
            if (this.selectedDate && !this.isInitializing) {
                setTimeout(() => {
                    this.calculatePrice();
                }, 200);
            }
        },

        formatPrice(price) {
            return `{{ $currencySymbol }}${parseFloat(price).toFixed(2)}`;
        },

        getParticipantsSummary() {
            const total = this.adults + this.children + this.infants;
            if (total === 1) {
                return '1 ' + @js(__('participant'));
            }
            return `${total} ` + @js(__('participants'));
        },

        getParticipantsDetail() {
            const parts = [];
            if (this.adults > 0) {
                parts.push(`${this.adults} ${this.adults === 1 ? @js(__('adult')) : @js(__('adults'))}`);
            }
            if (this.children > 0) {
                parts.push(`${this.children} ${this.children === 1 ? @js(__('child')) : @js(__('children'))}`);
            }
            if (this.infants > 0) {
                parts.push(`${this.infants} ${this.infants === 1 ? @js(__('baby')) : @js(__('babies'))}`);
            }
            return parts.length > 0 ? parts.join(', ') : @js(__('No participant'));
        },

        async proceedToBooking() {
            if (!this.priceData || !this.selectedTime) {
                alert(@js(__('Please first select a date and departure time.')));
                return;
            }

            if (!this.priceData.pricing_id) {
                alert(@js(__('Error: missing pricing_id. Please try again.')));
                return;
            }

            // Récupérer le token CSRF dynamiquement
            const csrfToken = window.getCsrfToken ? window.getCsrfToken() : (document.querySelector('meta[name="csrf-token"]')?.content || '');

            // Préparer les données pour l'ajout au panier
            const cartData = {
                date: this.selectedDate,
                pricing_mode: this.pricingMode,
                pricing_id: this.priceData.pricing_id,
                tour_date_id: this.selectedTime,
                adults: this.pricingMode === 'group' ? this.adults : this.totalPeople,
                children: this.pricingMode === 'group' ? this.children : 0,
                infants: this.pricingMode === 'group' ? this.infants : 0,
                selected_addons: this.selectedAddons || {},
                total_price: this.priceData.total_price || 0,
                _token: csrfToken
            };

            try {
                // Ajouter au panier via AJAX
                const response = await fetch(`/cart/add/${this.tourId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(cartData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Rediriger vers le panier
                    window.location.href = '/cart?added=1';
                } else {
                    throw new Error(result.message || @js(__('Error adding to cart')));
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                alert(@js(__('Error adding to cart. Please try again.')));
            }
        }
    }));
});

// Gestionnaire d'événement natif pour déboguer les clics sur les jours du calendrier
document.addEventListener('DOMContentLoaded', function() {
    // Utiliser la délégation d'événements pour capturer les clics même après le rendu dynamique
    document.addEventListener('click', function(e) {
        const calendarDay = e.target.closest('.calendar-day');
        if (calendarDay) {
            const date = calendarDay.getAttribute('data-date');
            const disabled = calendarDay.getAttribute('data-disabled') === 'true';
            const isOtherMonth = calendarDay.getAttribute('data-other-month') === 'true';
            
            console.log('🔍 NATIVE CLICK DETECTED on calendar day');
            console.log('Date:', date);
            console.log('Disabled:', disabled);
            console.log('Is Other Month:', isOtherMonth);
            console.log('Element:', calendarDay);
            
            if (disabled) {
                console.log('❌ Day is disabled, click blocked');
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            if (isOtherMonth) {
                console.log('✅ Clicking on other month day (should work)');
            }
        }
    }, true); // Utiliser capture phase pour intercepter avant Alpine
});
</script>



