@props(['tour', 'groupPricings', 'privatePricings', 'visible' => false])

@php
    $currencySymbol = \App\Helpers\CurrencyHelper::current()?->symbol ?? '€';
@endphp

{{-- Booking Steps Component --}}
<div @unless($visible) x-show="showStep2" x-cloak @endunless
     id="booking-step-2-block"
     class="p-6 md:p-8">
    
    <!-- Step 1: Sélection du tarif et de l'heure -->
    <div x-show="currentStep === 1" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="space-y-6">
        
        <!-- Header -->
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-6 sm:mb-8 flex items-center">
            <i class="fas fa-tags mr-3 text-xl sm:text-2xl" style="color: {{ primary_color() }};"></i>
            <span>{{ __('Choose your formula') }}</span>
        </h2>
        
        <!-- Liste de toutes les options de tarifs -->
        <div class="space-y-3 sm:space-y-4">
            <template x-for="pricing in [...groupPricings, ...privatePricings]" :key="pricing.id">
                <div class="bg-white rounded-xl shadow-md border-2 transition-all duration-300 relative cursor-pointer"
                     :class="selectedPricingId == pricing.id ? 'border-primary shadow-lg' : 'border-gray-200 hover:border-primary/50'"
                     @click="selectPricing(pricing)">
                    
                    <!-- Header avec radio et prix -->
                    <div class="p-3 sm:p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">
                            <div class="flex items-start flex-1 min-w-0">
                                <input type="radio" 
                                       name="step1_pricing_selection"
                                       :value="pricing.id"
                                       :checked="selectedPricingId == pricing.id"
                                       @change="async () => { await onPricingChange(pricing); if(selectedTime) calculateTotal(); }"
                                       @click.stop
                                       class="mt-1 w-5 h-5 mr-3 sm:mr-4 flex-shrink-0 cursor-pointer"
                                       style="accent-color: {{ primary_color() }};">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 truncate" x-text="pricing.title || (pricing.pricing_mode === 'group' ? @js(__('Group Rate')) : @js(__('Private Rate')))"></h4>
                                    <p class="text-sm text-gray-600" x-text="formatDate(selectedDate)"></p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right ml-8 sm:ml-4 flex-shrink-0">
                                <div class="text-2xl sm:text-3xl font-bold" 
                                     style="color: {{ primary_color() }};"
                                     x-text="getPricingTotalForDisplay(pricing)">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détails uniquement pour l'option sélectionnée -->
                    <div x-show="selectedPricingId == pricing.id" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="px-5 pb-5 space-y-4 border-t border-gray-200">
                        
                        <!-- Description si disponible -->
                        <div x-show="pricing.description" class="pt-4 text-sm text-gray-600 italic" x-text="pricing.description"></div>
                        
                        <!-- Sélection de l'heure de départ -->
                        <div class="pt-4">
                            <label class="block text-base font-semibold text-gray-900 mb-3">
                                <i class="fas fa-clock mr-2" style="color: {{ primary_color() }};"></i>
                                {{ __('Departure time') }}
                            </label>
                            <div x-show="loadingTimes" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                            <div x-show="!loadingTimes && departureTimes.length > 0" class="flex flex-wrap gap-2">
                                <template x-for="time in departureTimes" :key="time.id">
                            <button type="button"
                                    @click.stop="selectedTime = time.id; calculateTotal()"
                                            :class="selectedTime == time.id ? 'text-white' : 'bg-white text-gray-700 border-gray-300 hover:border-primary'"
                                            class="px-5 py-2.5 rounded-lg font-semibold text-sm border-2 transition-all duration-200"
                                            :style="selectedTime == time.id ? 'background-color: {{ primary_color() }}; border-color: {{ primary_color() }};' : ''"
                                            :disabled="!time.is_available">
                                        <span x-text="time.time"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="!loadingTimes && departureTimes.length === 0" class="text-sm text-gray-500 italic">
                                {{ __('No time configured') }}
                            </div>
                        </div>
                        
                        <!-- Politique d'annulation -->
                        <div class="flex items-start">
                            <i class="fas fa-check-circle mr-2 text-green-500 mt-0.5 flex-shrink-0 text-sm"></i>
                            <div class="text-xs sm:text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ __('Free cancellation') }}</span>
                                <span class="hidden sm:inline"> {{ __('before') }} <span x-text="getCancellationDate()"></span> {{ __('(local time)') }}</span>
                                <span class="sm:hidden"> {{ __('before') }} <span x-text="getCancellationDate()"></span></span>
                            </div>
                        </div>
                        
                        <!-- Breakdown du prix -->
                        <div class="space-y-2 pt-2">
                            <!-- Pour mode groupe -->
                            <template x-if="pricing.pricing_mode === 'group'">
                                <div class="space-y-1.5">
                                    <div x-show="adults > 0" class="flex justify-between items-center text-xs sm:text-sm gap-2">
                                        <span class="text-gray-700 truncate">
                                            <span x-text="adults"></span> {{ __('Adult(s)') }} × 
                                            <span x-text="formatPrice(pricing.price)"></span>
                                        </span>
                                        <span class="font-semibold text-gray-900 flex-shrink-0" x-text="formatPrice(adults * pricing.price)"></span>
                                    </div>
                                    <div x-show="children > 0" class="flex justify-between items-center text-xs sm:text-sm gap-2">
                                        <span class="text-gray-700 truncate">
                                            <span x-text="children"></span> {{ __('Child(ren)') }} × 
                                            <span x-text="formatPrice(pricing.child_price || pricing.price)"></span>
                                            <span x-show="pricing.child_price && pricing.child_price < pricing.price" 
                                                  class="ml-1 sm:ml-2 px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700"
                                                  x-text="'-' + Math.round(((pricing.price - pricing.child_price) / pricing.price) * 100) + '%'">
                                            </span>
                                        </span>
                                        <span class="font-semibold text-green-700 flex-shrink-0" x-text="formatPrice(children * (pricing.child_price || pricing.price))"></span>
                                    </div>
                                    <div x-show="infants > 0" class="flex justify-between items-center text-xs sm:text-sm gap-2">
                                        <span class="text-gray-700 truncate">
                                            <span x-text="infants"></span> {{ __('Baby(ies)') }} × 
                                            <span x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(pricing.infant_price) : @js(__('FREE'))"></span>
                                        </span>
                                        <span class="font-semibold text-blue-700 flex-shrink-0" x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(infants * pricing.infant_price) : @js(__('FREE'))"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Pour mode privé -->
                            <template x-if="pricing.pricing_mode === 'private'">
                                <div class="flex justify-between items-center text-xs sm:text-sm gap-2">
                                    <span class="text-gray-700 truncate">
                                        <span x-text="participants"></span> {{ __('person(s)') }} × 
                                        <span x-text="formatPrice(participants > 0 ? getPrivatePriceForParticipants(pricing, participants) / participants : getPrivatePriceForParticipants(pricing, 1))"></span>
                                    </span>
                                    <span class="font-semibold text-gray-900 flex-shrink-0" x-text="formatPrice(getPrivatePriceForParticipants(pricing, participants))"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Hébergements (dans Step 1) -->
                        <div x-show="selectedPricingId == pricing.id && !loadingAccommodations && accommodations.length > 0" 
                             class="pt-5 border-t space-y-4"
                             style="border-color: {{ primary_color() }};">
                            <div>
                                <h4 class="text-base font-bold text-gray-900 mb-4">
                                    <i class="fas fa-hotel mr-2" style="color: {{ primary_color() }};"></i>
                                    {{ __('Accommodation') }}
                                </h4>

                                <div class="space-y-3">
                                    <template x-for="accommodation in accommodations" :key="accommodation.id">
                                        <div class="border rounded-lg p-4 transition-all border-gray-200 bg-white">
                                            <div class="flex items-center gap-2 mb-3">
                                                <h5 class="font-semibold text-gray-900 text-sm" x-text="accommodation.name"></h5>
                                                <template x-if="accommodation.stars && accommodation.stars > 0">
                                                    <div class="flex items-center">
                                                        <template x-for="(star, index) in Array(Math.max(0, Math.min(accommodation.stars || 0, 5))).fill(0)" :key="'star-' + accommodation.id + '-' + index">
                                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                        </template>
                                                    </div>
                                                </template>
                                                <span x-show="!accommodation.is_optional" 
                                                      class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                                    {{ __('Included') }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800"
                                                      x-text="(accommodation.nights || 1) + ' ' + ((accommodation.nights || 1) > 1 ? @js(__('nights')) : @js(__('night')))">
                                                </span>
                                            </div>
                                            <p x-show="accommodation.location" class="text-xs text-gray-600 mb-3" x-text="accommodation.location"></p>
                                            
                                            <!-- Sélection des types de chambres avec quantités -->
                                            <div x-show="accommodation.rooms && accommodation.rooms.length > 0" 
                                                 class="mt-3 pt-3 border-t border-gray-200 space-y-3">
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('Available room types') }}</label>
                                                <template x-for="room in accommodation.rooms" :key="room.id">
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-sm font-medium text-gray-900" x-text="room.type_name"></span>
                                                                    <span class="text-xs text-gray-500">(max: <span x-text="room.max_occupancy"></span> {{ __('pers.') }})</span>
                                                                </div>
                                                                <div class="text-xs font-semibold text-blue-600" x-text="formatPrice(room.price_per_night) + ' ' + @js(__('per night'))"></div>
                                                            </div>
                                                            <div class="flex items-center gap-2 ml-4">
                                                                <label class="flex items-center gap-2">
                                                                    <span class="text-xs text-gray-600 whitespace-nowrap">{{ __('Quantity:') }}</span>
                                                                    <button type="button" 
                                                                            @click="decrementRoom(accommodation.id, room.id)"
                                                                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all text-xs"
                                                                            :class="selectedRooms[accommodation.id + '-' + room.id] > 0 ? 'border-primary text-primary' : 'border-gray-300 text-gray-400'"
                                                                            style="border-color: {{ primary_color() }};"
                                                                            :disabled="!selectedRooms[accommodation.id + '-' + room.id] || selectedRooms[accommodation.id + '-' + room.id] <= 0">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                    <input type="number" 
                                                                           x-model.number="selectedRooms[accommodation.id + '-' + room.id]"
                                                                           @change="if(selectedRooms[accommodation.id + '-' + room.id] <= 0) delete selectedRooms[accommodation.id + '-' + room.id]; calculateTotal();"
                                                                           min="0" 
                                                                           class="w-16 text-center font-bold text-sm text-gray-900 border-2 rounded-lg px-2 py-1"
                                                                           style="border-color: {{ primary_color() }};"
                                                                           placeholder="0">
                                                                    <button type="button" 
                                                                            @click="incrementRoom(accommodation.id, room.id)"
                                                                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all text-xs"
                                                                            style="border-color: {{ primary_color() }}; color: {{ primary_color() }};">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </label>
                                                                <div class="text-right ml-3">
                                                                    <div class="text-sm font-bold text-gray-900" 
                                                                         x-show="selectedRooms[accommodation.id + '-' + room.id] > 0"
                                                                         x-text="formatPrice((selectedRooms[accommodation.id + '-' + room.id] || 0) * room.price_per_night * (accommodation.nights || 1))">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Options supplémentaires (dans Step 1) -->
                        <div x-show="selectedPricingId == pricing.id && !loadingAddons && addons.length > 0" 
                             class="pt-5 border-t space-y-4"
                             style="border-color: {{ primary_color() }};">
                            <div>
                                <h4 class="text-base font-bold text-gray-900 mb-4">
                                    <i class="fas fa-plus-circle mr-2" style="color: {{ primary_color() }};"></i>
                                    {{ __('Additional options') }}
                                </h4>
                                <div class="space-y-2">
                                    <template x-for="addon in addons" :key="addon.id">
                                        <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border rounded-lg transition-all gap-2 sm:gap-3"
                                               :class="addon.is_included ? 'border-gray-200 cursor-default' : (addon.is_required ? 'border-red-300 bg-red-50 cursor-pointer' : (selectedAddons[addon.id] ? 'border-gray-400 bg-gray-50 hover:bg-gray-100 cursor-pointer' : 'border-gray-200 bg-white hover:bg-gray-50 cursor-pointer'))">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <input type="checkbox" 
                                                       :value="addon.id"
                                                       :checked="addon.is_required || addon.is_included || selectedAddons[addon.id]"
                                                       :disabled="addon.is_required || addon.is_included"
                                                       @change="if (!addon.is_required && !addon.is_included) { selectedAddons[addon.id] = selectedAddons[addon.id] ? undefined : 1; calculateTotal(); }"
                                                       class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500 mr-3 flex-shrink-0"
                                                       :class="(addon.is_required || addon.is_included) ? 'cursor-not-allowed opacity-60' : ''"
                                                       :style="addon.is_included ? 'accent-color: #10b981;' : 'accent-color: {{ primary_color() }};'">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate" x-text="addon.name"></div>
                                                        <span x-show="addon.is_required && !addon.is_included" 
                                                              class="px-2 py-0.5 rounded text-xs font-semibold bg-red-600 text-white flex-shrink-0">
                                                            {{ __('Required') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-left sm:text-right ml-7 sm:ml-3 flex-shrink-0" x-show="!addon.is_included">
                                                <div class="text-xs sm:text-sm font-semibold text-gray-900"
                                                     x-text="addon.pricing_type === 'free' ? @js(__('FREE')) : getAddonPriceLabel(addon)">
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total et bouton Réserver maintenant -->
                        <div class="pt-4 border-t-2 border-gray-300 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                            <div>
                                <div class="text-xs sm:text-sm font-semibold text-gray-600">{{ __('TOTAL') }}</div>
                            </div>
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="text-right flex-1 sm:flex-none">
                                    <div class="text-xl sm:text-2xl font-bold"
                                         style="color: {{ primary_color() }};"
                                         x-text="selectedPricingId == pricing.id && totalPrice ? formatPrice(totalPrice) : getPricingTotalForDisplay(pricing)">
                                    </div>
                                </div>
                                <button type="button"
                                        @click.stop="addToCart()"
                                        :disabled="!selectedTime || selectedPricingId != pricing.id || !totalPrice || !areRequiredAddonsSelected()"
                                        class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-base shadow-md hover:shadow-lg transition-all duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap"
                                        style="background-color: {{ primary_color() }}; color: white;">
                                    <span class="hidden sm:inline">{{ __('Add to cart') }}</span>
                                    <span class="sm:hidden">{{ __('Add to cart') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Breakdown simple pour les options non sélectionnées -->
                    <div x-show="selectedPricingId != pricing.id" 
                         class="px-3 sm:px-5 pb-3 sm:pb-5 border-t border-gray-200 pt-3 sm:pt-4">
                        <div class="space-y-1.5">
                            <!-- Pour mode groupe -->
                            <template x-if="pricing.pricing_mode === 'group'">
                                <div class="space-y-1.5">
                                    <div x-show="adults > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="adults"></span> {{ __('Adult(s)') }} × 
                                            <span x-text="formatPrice(pricing.price)"></span>
                                        </span>
                                        <span class="font-semibold flex-shrink-0" x-text="formatPrice(adults * pricing.price)"></span>
                                    </div>
                                    <div x-show="children > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="children"></span> {{ __('Child(ren)') }} × 
                                            <span x-text="formatPrice(pricing.child_price || pricing.price)"></span>
                                            <span x-show="pricing.child_price && pricing.child_price < pricing.price" 
                                                  class="ml-1 sm:ml-2 px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700"
                                                  x-text="'-' + Math.round(((pricing.price - pricing.child_price) / pricing.price) * 100) + '%'">
                                            </span>
                                        </span>
                                        <span class="font-semibold text-green-700 flex-shrink-0" x-text="formatPrice(children * (pricing.child_price || pricing.price))"></span>
                                    </div>
                                    <div x-show="infants > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="infants"></span> {{ __('Baby(ies)') }} × 
                                            <span x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(pricing.infant_price) : @js(__('FREE'))"></span>
                                        </span>
                                        <span class="font-semibold text-blue-700 flex-shrink-0" x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(infants * pricing.infant_price) : @js(__('FREE'))"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Pour mode privé -->
                            <template x-if="pricing.pricing_mode === 'private'">
                                <div class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                    <span class="truncate">
                                        <span x-text="participants"></span> {{ __('person(s)') }} × 
                                        <span x-text="formatPrice(participants > 0 ? getPrivatePriceForParticipants(pricing, participants) / participants : getPrivatePriceForParticipants(pricing, 1))"></span>
                                    </span>
                                    <span class="font-semibold flex-shrink-0" x-text="formatPrice(getPrivatePriceForParticipants(pricing, participants))"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Bouton Réserver maintenant pour les options non sélectionnées -->
                        <div class="pt-3 sm:pt-4 border-t-2 border-gray-300 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-4">
                            <div>
                                <div class="text-xs sm:text-sm font-semibold text-gray-600">{{ __('TOTAL') }}</div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 sm:gap-4 w-full sm:w-auto">
                                <div class="text-left sm:text-right flex-1 sm:flex-none">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold"
                                         style="color: {{ primary_color() }};"
                                         x-text="getPricingTotalForDisplay(pricing)">
                                    </div>
                                </div>
                                <button type="button"
                                        @click.stop="proceedToBooking()"
                                        :disabled="!selectedTime || selectedPricingId != pricing.id || !totalPrice"
                                        class="flex-1 sm:flex-none w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-base shadow-md hover:shadow-lg transition-all duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap"
                                        style="background-color: {{ primary_color() }}; color: white;">
                                    <span class="hidden sm:inline">{{ __('Book now') }}</span>
                                    <span class="sm:hidden">{{ __('Book') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Message si aucun tarif -->
        <div x-show="groupPricings.length === 0 && privatePricings.length === 0" 
             class="bg-white rounded-lg p-8 text-center border border-gray-200">
            <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-3"></i>
            <p class="text-sm text-gray-500">{{ __('No pricing configured for this tour') }}</p>
        </div>
    </div>

    <!-- Step 2: Récapitulatif final style image (conservé pour compatibilité mais non utilisé) -->
    <div x-show="currentStep === 3" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="space-y-4">
        
        <!-- Liste de toutes les options de tarifs -->
        <div class="space-y-4">
            <!-- Option sélectionnée avec tous les détails -->
            <template x-for="pricing in [...groupPricings, ...privatePricings]" :key="pricing.id">
                <div class="bg-white rounded-lg border-2 transition-all duration-300 relative cursor-pointer"
                     :class="selectedPricingId == pricing.id ? 'border-green-500' : 'border-gray-200 hover:border-green-300'"
                     @click="selectPricing(pricing)">
                    
                    <!-- Header avec radio et prix -->
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start flex-1">
                                <input type="radio" 
                                       name="final_pricing_selection"
                                       :value="pricing.id"
                                       :checked="selectedPricingId == pricing.id"
                                       @change="async () => { await onPricingChange(pricing); if(selectedTime) calculateTotal(); }"
                                       class="mt-1 w-5 h-5 mr-4 flex-shrink-0" 
                                       style="accent-color: #10b981;">
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 mb-1" x-text="pricing.title || (pricing.pricing_mode === 'group' ? @js(__('Group Rate')) : @js(__('Private Rate')))"></h4>
                                    <p class="text-sm text-gray-600" x-text="formatDate(selectedDate)"></p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-2xl font-bold text-gray-900" 
                                     x-text="getPricingTotalForDisplay(pricing)">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détails uniquement pour l'option sélectionnée -->
                    <div x-show="selectedPricingId == pricing.id" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="px-5 pb-5 space-y-4 border-t border-gray-200">
                        
                        <!-- Description si disponible -->
                        <div x-show="pricing.description" class="pt-4 text-sm text-gray-600 italic" x-text="pricing.description"></div>
                        
                        <!-- Heure de départ -->
                        <div class="pt-4">
                            <button type="button"
                                    class="px-4 py-2 rounded-lg font-semibold text-sm border-2 transition-all"
                                    style="border-color: #10b981; background-color: #10b981; color: white;"
                                    x-text="getSelectedTimeLabel()">
                            </button>
                        </div>
                        
                        <!-- Politique d'annulation -->
                        <div class="flex items-start">
                            <i class="fas fa-check-circle mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ __('Free cancellation') }}</span>
                                <span> {{ __('before') }} <span x-text="getCancellationDate()"></span> {{ __('(local time)') }}</span>
                            </div>
                        </div>
                        
                        <!-- Breakdown du prix -->
                        <div class="space-y-2 pt-2">
                            <!-- Pour mode groupe -->
                            <template x-if="pricing.pricing_mode === 'group'">
                                <div class="space-y-1.5">
                                    <div x-show="adults > 0" class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700">
                                            <span x-text="adults"></span> {{ __('Adult(s)') }} × 
                                            <span x-text="formatPrice(pricing.price)"></span>
                                        </span>
                                        <span class="font-semibold text-gray-900" x-text="formatPrice(adults * pricing.price)"></span>
                                    </div>
                                    <div x-show="children > 0" class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700">
                                            <span x-text="children"></span> {{ __('Child(ren)') }} × 
                                            <span x-text="formatPrice(pricing.child_price || pricing.price)"></span>
                                            <span x-show="pricing.child_price && pricing.child_price < pricing.price" 
                                                  class="ml-2 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700"
                                                  x-text="'-' + Math.round(((pricing.price - pricing.child_price) / pricing.price) * 100) + '%'">
                                            </span>
                                        </span>
                                        <span class="font-semibold text-green-700" x-text="formatPrice(children * (pricing.child_price || pricing.price))"></span>
                                    </div>
                                    <div x-show="infants > 0" class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700">
                                            <span x-text="infants"></span> {{ __('Baby(ies)') }} × 
                                            <span x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(pricing.infant_price) : @js(__('FREE'))"></span>
                                        </span>
                                        <span class="font-semibold text-blue-700" x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(infants * pricing.infant_price) : @js(__('FREE'))"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Pour mode privé -->
                            <template x-if="pricing.pricing_mode === 'private'">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">
                                        <span x-text="participants"></span> {{ __('person(s)') }} × 
                                        <span x-text="formatPrice(participants > 0 ? getPrivatePriceForParticipants(pricing, participants) / participants : getPrivatePriceForParticipants(pricing, 1))"></span>
                                    </span>
                                    <span class="font-semibold text-gray-900" x-text="formatPrice(getPrivatePriceForParticipants(pricing, participants))"></span>
                                </div>
                            </template>
                            
                            <!-- Options supplémentaires (si calculées) - Afficher tous les addons, y compris ceux inclus pour group -->
                            <template x-if="priceData && priceData.addons && priceData.addons.length > 0 && selectedPricingId == pricing.id">
                                <div class="pt-2 mt-2 border-t border-gray-200 space-y-1.5">
                                    <template x-for="addon in (priceData && priceData.addons ? priceData.addons : [])" :key="addon.addon_id">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700" x-text="addon.addon_name"></span>
                                            <span class="font-semibold text-gray-900" 
                                                  x-show="!addon.is_included || pricing.pricing_mode !== 'group'"
                                                  x-text="addon.total_price ? formatPrice(addon.total_price) : ''">
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Hébergement (si sélectionné) -->
                            <template x-if="priceData && priceData.accommodation_rooms && priceData.accommodation_rooms.length > 0 && selectedPricingId == pricing.id">
                                <div class="pt-2 mt-2 border-t border-gray-200 space-y-2">
                                    <template x-for="room in priceData.accommodation_rooms" :key="room.room_id + '-' + room.quantity">
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-center text-sm">
                                                <div>
                                                    <span class="text-gray-700 font-medium" x-text="room.accommodation_name"></span>
                                                    <span class="text-gray-500 text-xs ml-1" x-text="'(' + room.room_type_name + ')'"></span>
                                                    <span x-show="room.quantity > 1" class="text-gray-500 text-xs ml-1" x-text="'× ' + room.quantity"></span>
                                                </div>
                                                <span class="font-semibold text-gray-900" 
                                                      x-text="formatPrice(room.subtotal)">
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 ml-1" x-text="room.nights + ' ' + (room.nights > 1 ? @js(__('nights')) : @js(__('night'))) + ' × ' + formatPrice(room.price_per_night) + (room.quantity > 1 ? ' × ' + room.quantity : '')"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Total et bouton Book Now -->
                        <div class="pt-6 border-t-2 flex items-center justify-between"
                             style="border-color: {{ primary_color() }};">
                            <div>
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('TOTAL') }}</div>
                                <div class="text-xs text-gray-600">{{ __('All fees included') }}</div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <div class="text-2xl sm:text-3xl font-bold" 
                                         style="color: {{ primary_color() }};"
                                         x-text="selectedPricingId == pricing.id && totalPrice ? formatPrice(totalPrice) : getPricingTotalForDisplay(pricing)">
                                    </div>
                                </div>
                                <button type="button" 
                                        @click.stop="addToCart()"
                                        :disabled="!totalPrice || !selectedTime || selectedPricingId != pricing.id"
                                        class="px-6 py-3 rounded-lg font-bold text-sm sm:text-base transition-all duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50 shadow-lg hover:shadow-xl"
                                        style="background-color: {{ primary_color() }}; color: white;">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    {{ __('Add to cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Breakdown simple pour les options non sélectionnées -->
                    <div x-show="selectedPricingId != pricing.id" 
                         class="px-3 sm:px-5 pb-3 sm:pb-5 border-t border-gray-200 pt-3 sm:pt-4">
                        <div class="space-y-1.5">
                            <!-- Pour mode groupe -->
                            <template x-if="pricing.pricing_mode === 'group'">
                                <div class="space-y-1.5">
                                    <div x-show="adults > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="adults"></span> {{ __('Adult(s)') }} × 
                                            <span x-text="formatPrice(pricing.price)"></span>
                                        </span>
                                        <span class="font-semibold flex-shrink-0" x-text="formatPrice(adults * pricing.price)"></span>
                                    </div>
                                    <div x-show="children > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="children"></span> {{ __('Child(ren)') }} × 
                                            <span x-text="formatPrice(pricing.child_price || pricing.price)"></span>
                                            <span x-show="pricing.child_price && pricing.child_price < pricing.price" 
                                                  class="ml-1 sm:ml-2 px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700"
                                                  x-text="'-' + Math.round(((pricing.price - pricing.child_price) / pricing.price) * 100) + '%'">
                                            </span>
                                        </span>
                                        <span class="font-semibold text-green-700 flex-shrink-0" x-text="formatPrice(children * (pricing.child_price || pricing.price))"></span>
                                    </div>
                                    <div x-show="infants > 0" class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                        <span class="truncate">
                                            <span x-text="infants"></span> {{ __('Baby(ies)') }} × 
                                            <span x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(pricing.infant_price) : @js(__('FREE'))"></span>
                                        </span>
                                        <span class="font-semibold text-blue-700 flex-shrink-0" x-text="(pricing.infant_price && pricing.infant_price > 0) ? formatPrice(infants * pricing.infant_price) : @js(__('FREE'))"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Pour mode privé -->
                            <template x-if="pricing.pricing_mode === 'private'">
                                <div class="flex justify-between items-center text-xs sm:text-sm text-gray-600 gap-2">
                                    <span class="truncate">
                                        <span x-text="participants"></span> {{ __('person(s)') }} × 
                                        <span x-text="formatPrice(participants > 0 ? getPrivatePriceForParticipants(pricing, participants) / participants : getPrivatePriceForParticipants(pricing, 1))"></span>
                                    </span>
                                    <span class="font-semibold flex-shrink-0" x-text="formatPrice(getPrivatePriceForParticipants(pricing, participants))"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Bouton Réserver maintenant pour les options non sélectionnées -->
                        <div class="pt-3 sm:pt-4 border-t-2 border-gray-300 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-4">
                            <div>
                                <div class="text-xs sm:text-sm font-semibold text-gray-600">{{ __('TOTAL') }}</div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 sm:gap-4 w-full sm:w-auto">
                                <div class="text-left sm:text-right flex-1 sm:flex-none">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold"
                                         style="color: {{ primary_color() }};"
                                         x-text="getPricingTotalForDisplay(pricing)">
                                    </div>
                                </div>
                                <button type="button"
                                        @click.stop="proceedToBooking()"
                                        :disabled="!selectedTime || selectedPricingId != pricing.id || !totalPrice"
                                        class="flex-1 sm:flex-none w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-base shadow-md hover:shadow-lg transition-all duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap"
                                        style="background-color: {{ primary_color() }}; color: white;">
                                    <span class="hidden sm:inline">{{ __('Book now') }}</span>
                                    <span class="sm:hidden">{{ __('Book') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Message si aucun tarif -->
        <div x-show="groupPricings.length === 0 && privatePricings.length === 0" 
             class="bg-white rounded-lg p-8 text-center border border-gray-200">
            <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-3"></i>
            <p class="text-sm text-gray-500">{{ __('No pricing configured for this tour') }}</p>
        </div>
        
        <!-- Bouton retour -->
        <div class="flex justify-center pt-4">
            <button type="button" 
                    @click="currentStep = 2"
                    class="px-6 py-3 rounded-lg font-semibold text-base border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>{{ __('Back') }}</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
    // Booking Step 2 Component
    function registerBookingStep2() {
        if (typeof window.Alpine === 'undefined') {
            return;
        }
        window.Alpine.data('bookingStep2', (tourId, groupPricings = [], privatePricings = []) => ({
            tourId: tourId,
            showStep2: true,
            currentStep: 1,
            pricingMode: '',
            selectedPricingId: null,
            groupPricings: groupPricings,
            privatePricings: privatePricings,
            selectedDate: '',
            selectedTime: '',
            departureTimes: [],
            loadingTimes: false,
            participants: 1,
            adults: 1,
            children: 0,
            infants: 0,
            addons: [],
            selectedAddons: {},
            loadingAddons: false,
            accommodations: [],
            loadingAccommodations: false,
            selectedRooms: {}, // Format: { 'accommodation_id-room_id': quantity }
            totalPrice: null,
            priceData: null,
            
            formatPrice(price) {
                if (!price || price === null) return @js(__('On request'));
                @php
                    $currentCurrency = \App\Helpers\CurrencyHelper::current();
                    $currencySymbol = $currentCurrency?->symbol ?? '€';
                @endphp
                return '{{ $currencySymbol }}' + parseFloat(price).toFixed(2);
            },

            init() {
                // Load departure times immediately
                this.loadDepartureTimes();
                
                // Écouter les demandes de données depuis le bloc fixe
                window.addEventListener('request-booking-data', () => {
                    window.bookingDataCache = {
                        selectedDate: this.selectedDate,
                        selectedTime: this.selectedTime,
                        pricingMode: this.pricingMode,
                        selectedPricingId: this.selectedPricingId,
                        participants: this.participants,
                        adults: this.adults,
                        children: this.children,
                        infants: this.infants,
                        selectedAddons: this.selectedAddons
                    };
                });
                
                // Restaurer la sélection si présente dans l'URL (après le chargement initial)
                this.$nextTick(async () => {
                    if (this.selectedPricingId) {
                        // Sauvegarder l'heure sélectionnée depuis l'URL
                        const savedSelectedTime = this.selectedTime;
                        
                        // Trouver le pricing correspondant
                        const allPricings = [...this.groupPricings, ...this.privatePricings];
                        const pricing = allPricings.find(p => p.id == this.selectedPricingId);
                        if (pricing) {
                            this.onPricingChange(pricing);
                            // Recharger les heures de départ et restaurer la sélection après
                            await this.loadDepartureTimes();
                            // Restaurer l'heure sélectionnée si elle existe
                            if (savedSelectedTime) {
                                this.selectedTime = savedSelectedTime;
                                this.calculateTotal();
                            }
                        }
                    }
                });
            },

            async selectPricing(pricing) {
                if (this.selectedPricingId != pricing.id) {
                    await this.onPricingChange(pricing);
                    if(this.selectedTime) this.calculateTotal();
                }
            },

            async onPricingChange(pricing) {
                this.pricingMode = pricing.pricing_mode;
                this.selectedPricingId = pricing.id;
                this.selectedAddons = {};
                // Charger les addons et hébergements quand une formule est sélectionnée
                await Promise.all([
                    this.loadAddons(),
                    this.loadAccommodations()
                ]);
            },
            
            onPricingModeChange() {
                this.selectedPricingId = null;
                this.selectedAddons = {};
            },

            async loadDepartureTimes() {
                // Load all departure times for the tour (independent of date)
                this.loadingTimes = true;
                try {
                    const response = await fetch(`/api/v1/tours/${this.tourId}/times`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success && result.times) {
                        this.departureTimes = result.times;
                        // Auto-select first available time if none selected (mais ne pas écraser une sélection existante)
                        const currentSelectedTime = this.selectedTime;
                        if (!currentSelectedTime && this.departureTimes.length > 0) {
                            const firstAvailable = this.departureTimes.find(t => t.is_available);
                            if (firstAvailable) {
                                this.selectedTime = firstAvailable.id;
                            }
                        }
                    } else {
                        this.departureTimes = [];
                    }
                } catch (error) {
                    console.error('Error loading departure times:', error);
                    this.departureTimes = [];
                } finally {
                    this.loadingTimes = false;
                }
            },

            async loadAddons() {
                if (!this.pricingMode || !this.selectedPricingId) {
                    this.addons = [];
                    return;
                }

                // Use selectedDate if available, otherwise use today's date
                const dateToUse = this.selectedDate || new Date().toISOString().split('T')[0];
                
                // Calculate total participants based on pricing mode
                const totalParticipants = this.pricingMode === 'group' 
                    ? (this.adults + this.children + this.infants)
                    : this.participants;

                this.loadingAddons = true;
                try {
                    const response = await fetch(`/api/v1/tours/${this.tourId}/pricing/${this.pricingMode}/addons?date=${dateToUse}&participants=${totalParticipants}&pricing_id=${this.selectedPricingId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success && result.addons) {
                        this.addons = result.addons;
                        this.selectedAddons = {};
                        this.addons.forEach(addon => {
                            // S'assurer que les addons requis et inclus sont toujours sélectionnés
                            if (addon.is_required || addon.is_included) {
                                this.selectedAddons[addon.id] = 1;
                            }
                        });
                        
                        // Vérifier que tous les addons requis sont bien sélectionnés
                        const requiredAddons = this.addons.filter(a => a.is_required && !a.is_included);
                        requiredAddons.forEach(addon => {
                            if (!this.selectedAddons[addon.id]) {
                                this.selectedAddons[addon.id] = 1;
                            }
                        });
                        
                        // Toujours rester sur Step 1 (les options supplémentaires sont maintenant dans Step 1)
                        this.calculateTotal();
                    } else {
                        this.addons = [];
                    }
                } catch (error) {
                    console.error('Error loading addons:', error);
                    this.addons = [];
                } finally {
                    this.loadingAddons = false;
                }
            },

            async loadAccommodations() {
                if (!this.pricingMode || !this.selectedPricingId) {
                    this.accommodations = [];
                    return;
                }

                // Use selectedDate if available, otherwise use today's date
                const dateToUse = this.selectedDate || new Date().toISOString().split('T')[0];

                this.loadingAccommodations = true;
                try {
                    const response = await fetch(`/api/v1/tours/${this.tourId}/pricing/${this.pricingMode}/accommodations?date=${dateToUse}&pricing_id=${this.selectedPricingId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success && result.accommodations) {
                        this.accommodations = result.accommodations;
                        // Réinitialiser la sélection d'hébergement si nécessaire
                        if (this.accommodations.length === 0) {
                            this.selectedRooms = {};
                        }
                        this.calculateTotal();
                    } else {
                        this.accommodations = [];
                        this.selectedRooms = {};
                    }
                } catch (error) {
                    console.error('Error loading accommodations:', error);
                    this.accommodations = [];
                    this.selectedRooms = {};
                } finally {
                    this.loadingAccommodations = false;
                }
            },

            getAddonPriceLabel(addon) {
                if (addon.pricing_type === 'per_person') {
                    return `+{{ $currencySymbol }}${parseFloat(addon.price).toFixed(2)}`;
                } else if (addon.pricing_type === 'per_group') {
                    return `+{{ $currencySymbol }}${parseFloat(addon.price).toFixed(2)}`;
                }
                return @js(__('FREE'));
            },
            
            // Vérifier que tous les addons requis sont sélectionnés
            areRequiredAddonsSelected() {
                const requiredAddons = this.addons.filter(a => a.is_required && !a.is_included);
                return requiredAddons.every(addon => this.selectedAddons[addon.id] && this.selectedAddons[addon.id] > 0);
            },
            

            async calculateTotal() {
                const data = {
                    tour_id: this.tourId,
                    date: this.selectedDate,
                    pricing_mode: this.pricingMode,
                    pricing_id: this.selectedPricingId,
                    adults: this.pricingMode === 'group' ? this.adults : this.participants,
                    children: this.pricingMode === 'group' ? this.children : 0,
                    infants: this.pricingMode === 'group' ? this.infants : 0,
                    selected_addons: this.selectedAddons,
                };

                // Add tour_date_id if time is selected
                if (this.selectedTime) {
                    data.tour_date_id = this.selectedTime;
                }

                // Add accommodation data if any rooms are selected
                if (this.selectedRooms && Object.keys(this.selectedRooms).length > 0) {
                    const accommodationRooms = [];
                    Object.keys(this.selectedRooms).forEach(key => {
                        const quantity = this.selectedRooms[key];
                        if (quantity > 0) {
                            const [accommodationId, roomId] = key.split('-');
                            const accommodation = this.accommodations.find(a => a.id == accommodationId);
                            if (accommodation) {
                                const room = accommodation.rooms.find(r => r.id == roomId);
                                if (room) {
                                    accommodationRooms.push({
                                        accommodation_id: parseInt(accommodationId),
                                        accommodation_room_id: parseInt(roomId),
                                        room_type: room.type,
                                        quantity: quantity,
                                        price_per_night: room.price_per_night,
                                        nights: accommodation.nights || 1
                                    });
                                }
                            }
                        }
                    });
                    
                    if (accommodationRooms.length > 0) {
                        data.accommodation_rooms = accommodationRooms;
                    }
                }

                try {
                    const response = await fetch('/api/v1/calculate-price', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(data)
                    });

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('API returned non-JSON response:', text.substring(0, 200));
                        throw new Error('Server returned HTML instead of JSON. Check server logs for errors.');
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        this.priceData = result.data;
                        this.totalPrice = result.data.total_price;
                        
                        // Émettre un événement pour mettre à jour le bloc fixe en bas
                        window.dispatchEvent(new CustomEvent('total-price-updated', {
                            detail: {
                                totalPrice: this.totalPrice,
                                selectedTime: this.selectedTime,
                                currentStep: this.currentStep
                            }
                        }));
                    } else {
                        console.error('Price calculation failed:', result.message);
                        this.priceData = null;
                        this.totalPrice = null;
                    }
                } catch (error) {
                    console.error('Error calculating price:', error);
                    this.priceData = null;
                    this.totalPrice = null;
                }
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr + 'T00:00:00');
                const locale = @js(app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'));
                return date.toLocaleDateString(locale, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            },

            getSelectedTimeLabel() {
                if (!this.selectedTime || !this.departureTimes.length) return @js(__('Not selected'));
                const time = this.departureTimes.find(t => t.id == this.selectedTime);
                return time ? time.time : @js(__('Not selected'));
            },
            
            getSelectedPricingTitle() {
                if (!this.selectedPricingId) return '';
                const allPricings = [...this.groupPricings, ...this.privatePricings];
                const selected = allPricings.find(p => p.id == this.selectedPricingId);
                return selected && selected.title ? selected.title : '';
            },
            
            // Fonction pour obtenir le prix private selon le nombre de participants
            getPrivatePriceForParticipants(pricing, participants) {
                if (!pricing || pricing.pricing_mode !== 'private' || !pricing.private_price_tiers) {
                    return pricing?.price || 0;
                }
                
                // Trouver le tier qui correspond au nombre de participants
                const tier = pricing.private_price_tiers.find(t => 
                    participants >= t.min_people && participants <= t.max_people
                );
                
                return tier ? tier.price : (pricing.price || 0);
            },
            
            getPricingTotalForDisplay(pricing) {
                if (!pricing || !pricing.price) return @js(__('On request'));
                
                if (pricing.pricing_mode === 'group') {
                    const adultTotal = (this.adults || 0) * pricing.price;
                    const childTotal = (this.children || 0) * (pricing.child_price || pricing.price);
                    // Ne calculer le prix des bébés que si le prix existe et est > 0
                    const infantTotal = (this.infants || 0) * ((pricing.infant_price && pricing.infant_price > 0) ? pricing.infant_price : 0);
                    const total = adultTotal + childTotal + infantTotal;
                    return this.formatPrice(total);
                } else {
                    // Mode privé: utiliser le prix du tier correspondant au nombre de participants
                    const privatePrice = this.getPrivatePriceForParticipants(pricing, this.participants || 1);
                    return this.formatPrice(privatePrice);
                }
            },
            
            getCancellationDate() {
                if (!this.selectedDate) return '';
                const date = new Date(this.selectedDate + 'T00:00:00');
                date.setDate(date.getDate() - 1);
                const locale = @js(app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'));
                return date.toLocaleDateString(locale, { 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric' 
                });
            },

            incrementRoom(accommodationId, roomId) {
                const key = accommodationId + '-' + roomId;
                if (!this.selectedRooms[key]) {
                    this.selectedRooms[key] = 0;
                }
                this.selectedRooms[key] = (this.selectedRooms[key] || 0) + 1;
                this.calculateTotal();
            },

            decrementRoom(accommodationId, roomId) {
                const key = accommodationId + '-' + roomId;
                if (!this.selectedRooms[key] || this.selectedRooms[key] <= 0) {
                    return;
                }
                this.selectedRooms[key] = this.selectedRooms[key] - 1;
                if (this.selectedRooms[key] === 0) {
                    delete this.selectedRooms[key];
                }
                this.calculateTotal();
            },

            async addToCart() {
                if (!this.totalPrice || !this.selectedTime) return;

                // Récupérer le token CSRF dynamiquement
                const csrfToken = window.getCsrfToken ? window.getCsrfToken() : (document.querySelector('meta[name="csrf-token"]')?.content || '');

                const data = {
                    date: this.selectedDate,
                    pricing_mode: this.pricingMode,
                    pricing_id: this.selectedPricingId,
                    tour_date_id: this.selectedTime,
                    adults: this.pricingMode === 'group' ? this.adults : this.participants,
                    children: this.pricingMode === 'group' ? this.children : 0,
                    infants: this.pricingMode === 'group' ? this.infants : 0,
                    selected_addons: this.selectedAddons || {},
                    total_price: this.totalPrice,
                    _token: csrfToken
                };

                // Add accommodation data if any rooms are selected
                if (this.selectedRooms && Object.keys(this.selectedRooms).length > 0) {
                    const accommodationRooms = [];
                    Object.keys(this.selectedRooms).forEach(key => {
                        const quantity = this.selectedRooms[key];
                        if (quantity > 0) {
                            const [accommodationId, roomId] = key.split('-');
                            const accommodation = this.accommodations.find(a => a.id == accommodationId);
                            if (accommodation) {
                                const room = accommodation.rooms.find(r => r.id == roomId);
                                if (room) {
                                    accommodationRooms.push({
                                        accommodation_id: parseInt(accommodationId),
                                        accommodation_room_id: parseInt(roomId),
                                        room_type: room.type,
                                        quantity: quantity,
                                        price_per_night: room.price_per_night,
                                        nights: accommodation.nights || 1
                                    });
                                }
                            }
                        }
                    });
                    
                    if (accommodationRooms.length > 0) {
                        data.accommodation_rooms = accommodationRooms;
                    }
                }

                try {
                    const response = await fetch(`/cart/add/${this.tourId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Rediriger vers le panier avec un message de succès
                        window.location.href = '/cart?added=1';
                    } else {
                        alert(@js(__('Error adding to cart:')) + ' ' + (result.message || 'Erreur inconnue'));
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    alert(@js(__('Error adding to cart. Please try again.')));
                }
            },

            async proceedToBooking() {
                if (!this.totalPrice || !this.selectedTime) {
                    alert(@js(__('Please first select a formula and a departure time.')));
                    return;
                }

                if (!this.selectedPricingId) {
                    alert(@js(__('Please first select a formula.')));
                    return;
                }

                // Récupérer le token CSRF dynamiquement
                const csrfToken = window.getCsrfToken ? window.getCsrfToken() : (document.querySelector('meta[name="csrf-token"]')?.content || '');

                // Préparer les données pour l'ajout au panier
                const cartData = {
                    date: this.selectedDate,
                    pricing_mode: this.pricingMode,
                    pricing_id: this.selectedPricingId,
                    tour_date_id: this.selectedTime,
                    adults: this.pricingMode === 'group' ? this.adults : this.participants,
                    children: this.pricingMode === 'group' ? this.children : 0,
                    infants: this.pricingMode === 'group' ? this.infants : 0,
                    selected_addons: this.selectedAddons || {},
                    total_price: this.totalPrice,
                    _token: csrfToken
                };

                // Add accommodation data if any rooms are selected
                if (this.selectedRooms && Object.keys(this.selectedRooms).length > 0) {
                    const accommodationRooms = [];
                    Object.keys(this.selectedRooms).forEach(key => {
                        const quantity = this.selectedRooms[key];
                        if (quantity > 0) {
                            const [accommodationId, roomId] = key.split('-');
                            const accommodation = this.accommodations.find(a => a.id == accommodationId);
                            if (accommodation) {
                                const room = accommodation.rooms.find(r => r.id == roomId);
                                if (room) {
                                    accommodationRooms.push({
                                        accommodation_id: parseInt(accommodationId),
                                        accommodation_room_id: parseInt(roomId),
                                        room_type: room.type,
                                        quantity: quantity,
                                        price_per_night: room.price_per_night,
                                        nights: accommodation.nights || 1
                                    });
                                }
                            }
                        }
                    });
                    
                    if (accommodationRooms.length > 0) {
                        cartData.accommodation_rooms = accommodationRooms;
                    }
                }

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
                        throw new Error(result.message || @js(__('Error adding to cart:')));
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    alert(@js(__('Error adding to cart. Please try again.')));
                }
            }
        }));
    }

    document.addEventListener('livewire:init', registerBookingStep2);
    document.addEventListener('alpine:init', registerBookingStep2);
    document.addEventListener('DOMContentLoaded', () => {
        registerBookingStep2();
        const el = document.getElementById('booking-steps-container');
        if (el && window.Alpine && !el._x_dataStack) {
            window.Alpine.initTree(el);
        }
    });
</script>
@endpush

