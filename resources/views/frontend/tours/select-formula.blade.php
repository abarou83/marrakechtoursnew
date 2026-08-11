<x-app-layout>
    @push('styles')
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
    </style>
    @endpush

    <div class="bg-gray-50 min-h-screen pb-32 sm:pb-24 pt-6 md:pt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <!-- Lien de retour vers le tour -->
                <a href="{{ route('tours.show', $tour->url_key) }}" 
                   class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-primary transition-colors mb-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>{{ __('Back to tour') }}</span>
                </a>
                
                <!-- Titre de la page -->
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        {{ translate_model($tour, 'title') }}
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600">
                        {{ __('Choose your formula and complete your booking') }}
                    </p>
                </div>
            </div>
            
            <!-- Booking Steps Component -->
            <div id="booking-steps-container"
                 x-data="bookingStep2({{ $tour->id }})" 
                 data-group-pricings='@json($groupPricings)'
                 data-private-pricings='@json($privatePricings)'
                 x-init="
                    groupPricings = JSON.parse($el.dataset.groupPricings || '[]');
                    privatePricings = JSON.parse($el.dataset.privatePricings || '[]');
                    selectedDate = '{{ $selectedDate }}';
                    participants = {{ $participants }};
                    adults = {{ $adults }};
                    children = {{ $children }};
                    infants = {{ $infants }};
                    showStep2 = true;
                    currentStep = 1;
                    // Restaurer la sélection de formule si présente dans l'URL
                    @if(request('pricing_id'))
                        selectedPricingId = parseInt('{{ request('pricing_id') }}') || null;
                    @endif
                    @if(request('selected_time'))
                        selectedTime = parseInt('{{ request('selected_time') }}') || null;
                    @endif
                 ">
                @include('frontend.tours.booking-steps', [
                    'tour' => $tour,
                    'groupPricings' => $groupPricings,
                    'privatePricings' => $privatePricings,
                    'visible' => true,
                ])
            </div>

            @if($groupPricings->isEmpty() && $privatePricings->isEmpty())
                <div class="bg-white rounded-xl border border-amber-200 p-8 text-center text-gray-700">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-3"></i>
                    <p>{{ __('No pricing configured for this tour') }}</p>
                    <p class="text-sm mt-2">{{ __('Run') }} <code class="text-xs bg-gray-100 px-1 rounded">php artisan tours:setup-booking</code></p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modifier la réservation - Fixe en bas -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-2 shadow-2xl z-50" 
         style="border-color: {{ primary_color() }};"
         x-data="updateBookingForm({{ $tour->id }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <!-- Mode Affichage (Total + Boutons) -->
            <div x-show="!showEditMode" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <!-- Total -->
                <div class="flex-1 w-full sm:w-auto text-center sm:text-left">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('TOTAL') }}</div>
                    <div class="text-2xl sm:text-3xl md:text-4xl font-bold mt-1"
                         style="color: {{ primary_color() }};"
                         x-text="totalPrice ? formatPrice(totalPrice) : @js(__('Calculating...'))">
                    </div>
                </div>
                
                <!-- Boutons à droite -->
                <div class="flex items-center gap-3 w-full sm:w-auto sm:ml-auto">
                    <button type="button"
                            @click="showEditMode = true"
                            class="flex-1 sm:flex-none px-5 sm:px-6 py-2.5 sm:py-3 border-2 rounded-lg font-semibold text-sm sm:text-base text-gray-700 bg-white hover:bg-gray-50 transition-all shadow-sm hover:shadow-md"
                            style="border-color: {{ primary_color() }};">
                        <i class="fas fa-edit sm:mr-2"></i>
                        <span class="hidden sm:inline">{{ __('Modify') }}</span>
                        <span class="sm:hidden">{{ __('Modify') }}</span>
                    </button>
                    <button type="button"
                            @click="proceedToBooking()"
                            :disabled="!totalPrice || !selectedTime"
                            class="flex-1 sm:flex-none px-6 sm:px-8 py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50"
                            style="background-color: {{ primary_color() }}; color: white;">
                        <i class="fas fa-check-circle sm:mr-2"></i>
                        <span class="hidden sm:inline">{{ __('Book now') }}</span>
                        <span class="sm:hidden">{{ __('Book') }}</span>
                    </button>
                </div>
            </div>

            <!-- Mode Édition (Champs Date + Participants) -->
            <div x-show="showEditMode" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                
                <!-- Date Field -->
                <div class="flex-1 sm:flex-none sm:w-64 lg:w-80 relative flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3" x-data="{ showDateDropdown: false }" @click.away="showDateDropdown = false">
                    <label class="text-sm font-semibold text-gray-700 flex-shrink-0 whitespace-nowrap">
                        <i class="fas fa-calendar-alt mr-2" style="color: {{ primary_color() }};"></i>{{ __('Date') }}
                    </label>
                    <button type="button" 
                            @click="showDateDropdown = !showDateDropdown; if(showDateDropdown) initCalendar();"
                            class="flex-1 flex items-center justify-between p-3 border-2 rounded-lg hover:border-primary transition-all bg-white text-left text-sm font-medium"
                            style="border-color: {{ primary_color() }};">
                        <div class="flex-1 min-w-0">
                            <div class="text-gray-900 truncate" x-text="selectedDate ? formatDate(selectedDate) : @js(__('Select'))"></div>
                        </div>
                        <i class="fas fa-calendar-alt ml-2 flex-shrink-0" style="color: {{ primary_color() }};"></i>
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
                         class="fixed sm:absolute bottom-full mb-2 left-4 right-4 sm:left-auto sm:right-auto sm:w-80 bg-white rounded-lg shadow-xl border border-gray-200 p-3 sm:p-4"
                         style="transform-origin: bottom; z-index: 60;">
                        
                        <!-- Month Navigation -->
                        <div class="flex items-center justify-between mb-4">
                            <button type="button" 
                                    @click="previousMonth()"
                                    :disabled="isPastMonth()"
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                    :style="!isPastMonth() ? 'color: {{ primary_color() }};' : ''">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="text-base font-bold text-gray-900" x-text="getCurrentMonthYear()"></div>
                            <button type="button" 
                                    @click="nextMonth()"
                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    style="color: {{ primary_color() }};">
                                <i class="fas fa-chevron-right"></i>
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
                            <template x-for="(day, index) in calendarDays" :key="day.key || `${day.date}-${index}`">
                                <button type="button"
                                        @click="handleDateClick(day)"
                                        :disabled="day.disabled"
                                        class="aspect-square p-1.5 rounded-lg text-xs font-medium transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                                        :class="{
                                            'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 active:bg-gray-300': day.isOtherMonth && !day.disabled && !day.isSelected,
                                            'bg-gray-200 text-gray-500 cursor-not-allowed': day.disabled,
                                            'hover:bg-gray-100 text-gray-600 cursor-pointer': !day.isSelected && !day.disabled && !day.isOtherMonth,
                                            'bg-white text-gray-900 border-2': day.isToday && !day.isSelected && !day.disabled && !day.isOtherMonth
                                        }"
                                        :style="day.isSelected ? 'background-color: {{ primary_color() }}; color: white;' : (day.isOtherMonth && !day.disabled ? 'cursor: pointer !important;' : '')">
                                    <span x-text="day.day"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Participants Field -->
                <div class="flex-1 sm:flex-none sm:w-64 lg:w-80 relative flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3" @click.away="showParticipantsDropdown = false">
                    <label class="text-sm font-semibold text-gray-700 flex-shrink-0 whitespace-nowrap">
                        <i class="fas fa-users mr-2" style="color: {{ primary_color() }};"></i>{{ __('Participants') }}
                    </label>
                    <button type="button" 
                            @click="showParticipantsDropdown = !showParticipantsDropdown"
                            class="flex-1 flex items-center justify-between p-3 border-2 rounded-lg hover:border-primary transition-all bg-white text-left text-sm font-medium"
                            style="border-color: {{ primary_color() }};">
                        <div class="flex-1 min-w-0">
                            <div class="text-gray-900 truncate" x-text="getParticipantsSummary()"></div>
                        </div>
                        <i class="fas fa-chevron-down ml-2 transition-transform flex-shrink-0" 
                           style="color: {{ primary_color() }};"
                           :class="showParticipantsDropdown ? 'rotate-180' : ''"></i>
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
                         class="absolute bottom-full mb-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 p-4"
                         style="transform-origin: bottom; z-index: 50;">
                        
                        <!-- Adults -->
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 text-sm">{{ __('Adults') }}</div>
                                <div class="text-xs text-gray-500">{{ __('13 years and older') }}</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        @click="if(adults > 1) { adults--; updateBookingStep2Data(); }"
                                        :disabled="adults <= 1"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                        :style="adults > 1 ? 'border-color: {{ primary_color() }}; color: {{ primary_color() }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-10 text-center font-bold text-base text-gray-900" x-text="adults"></span>
                                <button type="button" 
                                        @click="adults++; updateBookingStep2Data();"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                        style="border-color: {{ primary_color() }}; color: {{ primary_color() }};">
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
                                        @click="if(children > 0) { children--; updateBookingStep2Data(); }"
                                        :disabled="children <= 0"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                        :style="children > 0 ? 'border-color: {{ primary_color() }}; color: {{ primary_color() }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-10 text-center font-bold text-base text-gray-900" x-text="children"></span>
                                <button type="button" 
                                        @click="children++; updateBookingStep2Data();"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                        style="border-color: {{ primary_color() }}; color: {{ primary_color() }};">
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
                                        @click="if(infants > 0) { infants--; updateBookingStep2Data(); }"
                                        :disabled="infants <= 0"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                        :style="infants > 0 ? 'border-color: {{ primary_color() }}; color: {{ primary_color() }};' : 'border-color: #d1d5db; color: #9ca3af;'">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-10 text-center font-bold text-base text-gray-900" x-text="infants"></span>
                                <button type="button" 
                                        @click="infants++; updateBookingStep2Data();"
                                        class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all"
                                        style="border-color: {{ primary_color() }}; color: {{ primary_color() }};">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons Valider/Annuler à droite -->
                <div class="flex items-center gap-3 flex-shrink-0 sm:ml-auto w-full sm:w-auto">
                    <button type="button"
                            @click="showEditMode = false"
                            class="flex-1 sm:flex-none px-5 sm:px-6 py-2.5 sm:py-3 border-2 rounded-lg font-semibold text-sm sm:text-base text-gray-700 bg-white hover:bg-gray-50 transition-all shadow-sm hover:shadow-md"
                            style="border-color: {{ primary_color() }};">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" 
                            @click="updateBooking()"
                            :disabled="!selectedDate || totalParticipants < 1"
                            class="flex-1 sm:flex-none px-6 sm:px-8 py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all disabled:bg-gray-400 disabled:cursor-not-allowed disabled:opacity-50"
                            style="background-color: {{ primary_color() }}; color: white;">
                        <i class="fas fa-check sm:mr-2"></i>
                        {{ __('Confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
    function registerUpdateBookingForm() {
        if (typeof window.Alpine === 'undefined') {
            return;
        }
        window.Alpine.data('updateBookingForm', (tourId) => ({
            tourId: tourId,
            selectedDate: '{{ $selectedDate }}',
            adults: {{ $adults }},
            children: {{ $children }},
            infants: {{ $infants }},
            showEditMode: false,
            showParticipantsDropdown: false,
            totalPrice: null,
            selectedTime: null,
            currentStep: 1,
            currentMonth: new Date('{{ $selectedDate }}').getMonth(),
            currentYear: new Date('{{ $selectedDate }}').getFullYear(),
            minDate: '',

            init() {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                this.minDate = today.toISOString().split('T')[0];
                const selectedDateObj = new Date(this.selectedDate);
                this.currentMonth = selectedDateObj.getMonth();
                this.currentYear = selectedDateObj.getFullYear();
                
                // Écouter les mises à jour du total depuis bookingStep2
                window.addEventListener('total-price-updated', (e) => {
                    this.totalPrice = e.detail.totalPrice;
                    this.selectedTime = e.detail.selectedTime;
                    // Toujours rester sur Step 1 (plus besoin de Step 2)
                    this.currentStep = 1;
                });

                // Écouter les demandes de données depuis le bloc fixe
                window.addEventListener('request-booking-data', () => {
                    this.provideBookingData();
                });
            },

            // Fournir les données de réservation à bookingStep2
            provideBookingData() {
                const bookingContainer = document.getElementById('booking-steps-container');
                if (!bookingContainer) return;

                // Accéder à l'instance Alpine.js de bookingStep2
                const bookingData = Alpine.$data(bookingContainer);
                if (!bookingData) return;

                // Mettre en cache les données pour updateBooking
                window.bookingDataCache = {
                    selectedDate: bookingData.selectedDate,
                    selectedTime: bookingData.selectedTime,
                    selectedPricingId: bookingData.selectedPricingId,
                    pricingMode: bookingData.pricingMode,
                    participants: bookingData.participants,
                    adults: bookingData.adults,
                    children: bookingData.children,
                    infants: bookingData.infants,
                    selectedAddons: bookingData.selectedAddons
                };
            },

            get calendarDays() {
                const days = [];
                const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay() + (firstDay.getDay() === 0 ? -6 : 1));

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const todayStr = today.toISOString().split('T')[0];
                const todayTime = today.getTime();
                
                for (let i = 0; i < 42; i++) {
                    const date = new Date(startDate);
                    date.setDate(startDate.getDate() + i);
                    date.setHours(0, 0, 0, 0);
                    const dateStr = date.toISOString().split('T')[0];
                    const dateTime = date.getTime();
                    const isToday = dateStr === todayStr;
                    const isSelected = dateStr === this.selectedDate;
                    const isDisabled = dateTime < todayTime || dateStr < todayStr;
                    const isOtherMonth = date.getMonth() !== this.currentMonth;

                    // Créer une clé unique en combinant la date avec l'index pour éviter les doublons
                    const uniqueKey = `${this.currentYear}-${this.currentMonth}-${i}-${dateStr}`;

                    days.push({
                        key: uniqueKey,
                        date: dateStr,
                        day: date.getDate(),
                        isToday,
                        isSelected,
                        disabled: isDisabled,
                        isOtherMonth
                    });
                }
                return days;
            },

            initCalendar() {
                // Calendar already initialized
            },

            isPastMonth() {
                const today = new Date();
                const currentMonth = today.getMonth();
                const currentYear = today.getFullYear();
                return (this.currentYear < currentYear) || 
                       (this.currentYear === currentYear && this.currentMonth < currentMonth);
            },

            previousMonth() {
                if (this.isPastMonth()) {
                    return;
                }
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                if (this.isPastMonth()) {
                    const today = new Date();
                    this.currentMonth = today.getMonth();
                    this.currentYear = today.getFullYear();
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

            getCurrentMonthYear() {
                const months = [@js(__('January')), @js(__('February')), @js(__('March')), @js(__('April')), @js(__('May')), @js(__('June')), @js(__('July')), @js(__('August')), @js(__('September')), @js(__('October')), @js(__('November')), @js(__('December'))];
                return months[this.currentMonth] + ' ' + this.currentYear;
            },

            handleDateClick(day) {
                // Empêcher le clic si le jour est désactivé
                if (day.disabled) {
                    return false;
                }
                
                // Si c'est un jour du mois suivant/précédent, changer le mois affiché d'abord
                if (day.isOtherMonth) {
                    const selectedDateObj = new Date(day.date + 'T00:00:00');
                    this.currentMonth = selectedDateObj.getMonth();
                    this.currentYear = selectedDateObj.getFullYear();
                }
                
                // Sélectionner la date
                this.selectedDate = day.date;
                
                // Fermer le dropdown et mettre à jour
                this.showDateDropdown = false;
                this.updateBookingStep2Data();
                
                return true;
            },

            selectDate(date) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const todayStr = today.toISOString().split('T')[0];
                const todayTime = today.getTime();
                
                const selectedDate = new Date(date);
                selectedDate.setHours(0, 0, 0, 0);
                const selectedStr = selectedDate.toISOString().split('T')[0];
                const selectedTime = selectedDate.getTime();
                
                if (selectedTime < todayTime || selectedStr < todayStr) {
                    return;
                }
                
                this.selectedDate = date;
                
                // Mettre à jour la date dans bookingStep2 et recalculer
                this.updateBookingStep2Data();
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

            get totalParticipants() {
                return this.adults + this.children + this.infants;
            },

            // Mettre à jour les données dans bookingStep2 et recalculer
            async updateBookingStep2Data() {
                const bookingContainer = document.getElementById('booking-steps-container');
                if (!bookingContainer) return;

                // Accéder à l'instance Alpine.js de bookingStep2
                const bookingData = Alpine.$data(bookingContainer);
                if (!bookingData) return;

                // Mettre à jour les valeurs dans bookingStep2
                bookingData.selectedDate = this.selectedDate;
                bookingData.adults = this.adults;
                bookingData.children = this.children;
                bookingData.infants = this.infants;
                bookingData.participants = this.totalParticipants;

                // Recharger les addons si une formule est sélectionnée (pour mettre à jour les prix selon le nombre de participants)
                if (bookingData.selectedPricingId) {
                    await bookingData.loadAddons();
                }
                
                // Recalculer le prix si une formule et une heure sont sélectionnées
                if (bookingData.selectedPricingId && bookingData.selectedTime) {
                    bookingData.calculateTotal();
                }
            },

            getParticipantsSummary() {
                const total = this.totalParticipants;
                const participantLabel = total === 1 ? @js(__('participant')) : @js(__('participants'));
                return `${total} ${participantLabel}`;
            },

            getParticipantsDetail() {
                const parts = [];
                const adultLabel = @js(__('adult'));
                const adultsLabel = @js(__('adults'));
                const childLabel = @js(__('child'));
                const childrenLabel = @js(__('children'));
                const babyLabel = @js(__('baby'));
                const babiesLabel = @js(__('babies'));
                const noParticipantLabel = @js(__('No participant'));
                
                if (this.adults > 0) {
                    parts.push(`${this.adults} ${this.adults === 1 ? adultLabel : adultsLabel}`);
                }
                if (this.children > 0) {
                    parts.push(`${this.children} ${this.children === 1 ? childLabel : childrenLabel}`);
                }
                if (this.infants > 0) {
                    parts.push(`${this.infants} ${this.infants === 1 ? babyLabel : babiesLabel}`);
                }
                return parts.length > 0 ? parts.join(', ') : noParticipantLabel;
            },

            formatPrice(price) {
                if (!price || price === null) return @js(__('On request'));
                @php
                    $currentCurrency = \App\Helpers\CurrencyHelper::current();
                    $currencySymbol = $currentCurrency?->symbol ?? '€';
                @endphp
                return '{{ $currencySymbol }}' + parseFloat(price).toFixed(2);
            },

            updateBooking() {
                if (!this.selectedDate || this.totalParticipants < 1) {
                    alert(@js(__('Please select a date and at least one participant.')));
                    return;
                }

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const selectedDate = new Date(this.selectedDate);
                selectedDate.setHours(0, 0, 0, 0);
                
                if (selectedDate.getTime() < today.getTime()) {
                    alert(@js(__('You cannot select a past date. Please choose a date from today.')));
                    this.selectedDate = today.toISOString().split('T')[0];
                    return;
                }

                // Récupérer les données de sélection depuis bookingStep2
                const event = new CustomEvent('request-booking-data');
                window.dispatchEvent(event);
                
                // Attendre un peu pour que bookingStep2 réponde
                setTimeout(() => {
                    const bookingData = window.bookingDataCache || null;
                    
                    const params = new URLSearchParams({
                        date: this.selectedDate,
                        participants: this.totalParticipants,
                        adults: this.adults,
                        children: this.children,
                        infants: this.infants
                    });
                    
                    // Préserver la sélection de formule si elle existe
                    if (bookingData && bookingData.selectedPricingId) {
                        params.append('pricing_id', bookingData.selectedPricingId);
                    }
                    if (bookingData && bookingData.selectedTime) {
                        params.append('selected_time', bookingData.selectedTime);
                    }

                    this.showEditMode = false;
                    window.location.href = `/tours/${this.tourId}/select-formula?${params.toString()}`;
                }, 100);
            },

            async proceedToBooking() {
                // Émettre un événement pour demander les données à bookingStep2
                const event = new CustomEvent('request-booking-data');
                window.dispatchEvent(event);
                
                // Attendre un peu pour que bookingStep2 réponde
                setTimeout(async () => {
                    const bookingData = window.bookingDataCache || null;
                    
                    if (!bookingData || !bookingData.selectedTime) {
                        alert(@js(__('Please first select a formula and a departure time.')));
                        return;
                    }

                    if (!bookingData.selectedPricingId) {
                        alert(@js(__('Please first select a formula.')));
                        return;
                    }

                    // Récupérer le prix total depuis bookingStep2
                    const bookingContainer = document.getElementById('booking-steps-container');
                    const bookingStep2Data = bookingContainer ? Alpine.$data(bookingContainer) : null;
                    const totalPrice = bookingStep2Data?.totalPrice || 0;

                    if (!totalPrice) {
                        alert(@js(__('Error calculating price. Please try again.')));
                        return;
                    }

                    // Récupérer le token CSRF dynamiquement - toujours récupérer au moment de la requête
                    function getCsrfToken() {
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag && metaTag.content) {
                            return metaTag.content;
                        }
                        // Fallback: essayer de récupérer depuis window.getCsrfToken si disponible
                        if (window.getCsrfToken && typeof window.getCsrfToken === 'function') {
                            const token = window.getCsrfToken();
                            if (token) return token;
                        }
                        // Dernier recours: chercher dans tous les formulaires de la page
                        const formToken = document.querySelector('input[name="_token"]');
                        if (formToken && formToken.value) {
                            return formToken.value;
                        }
                        return '';
                    }
                    
                    const csrfToken = getCsrfToken();
                    
                    if (!csrfToken) {
                        alert(@js(__('Error: Missing security token. Please refresh the page.')));
                        console.error('CSRF token is empty');
                        return;
                    }

                    // Préparer les données pour l'ajout au panier
                    const cartData = {
                        date: bookingData.selectedDate,
                        pricing_mode: bookingData.pricingMode,
                        pricing_id: bookingData.selectedPricingId,
                        tour_date_id: bookingData.selectedTime,
                        adults: bookingData.pricingMode === 'group' ? bookingData.adults : bookingData.participants,
                        children: bookingData.pricingMode === 'group' ? bookingData.children : 0,
                        infants: bookingData.pricingMode === 'group' ? bookingData.infants : 0,
                        selected_addons: bookingData.selectedAddons || {},
                        total_price: totalPrice,
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

                        // Vérifier si c'est une erreur CSRF (419)
                        if (response.status === 419) {
                            // Mettre à jour le token depuis le meta tag et réessayer
                            const newToken = document.querySelector('meta[name="csrf-token"]')?.content;
                            if (newToken && newToken !== csrfToken) {
                                // Token mis à jour, réessayer avec le nouveau token
                                cartData._token = newToken;
                                const retryResponse = await fetch(`/cart/add/${this.tourId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': newToken,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify(cartData)
                                });
                                const retryResult = await retryResponse.json();
                                if (retryResponse.ok && retryResult.success) {
                                    window.location.href = '/cart?added=1';
                                    return;
                                }
                            }
                            alert(@js(__('Session expired. Please refresh the page and try again.')));
                            console.error('CSRF token mismatch (419)');
                            return;
                        }

                        const result = await response.json();

                        if (response.ok && result.success) {
                            // Rediriger vers le panier
                            window.location.href = '/cart?added=1';
                        } else {
                            throw new Error(result.message || @js(__('Error adding to cart')));
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        if (error.message.includes('CSRF') || error.message.includes('token')) {
                            alert(@js(__('Security error. Please refresh the page and try again.')));
                        } else {
                            alert(@js(__('Error adding to cart. Please try again.')));
                        }
                    }
                }, 100);
            }
        }));
    }

    document.addEventListener('livewire:init', registerUpdateBookingForm);
    document.addEventListener('alpine:init', registerUpdateBookingForm);
    document.addEventListener('DOMContentLoaded', () => {
        registerUpdateBookingForm();
        document.querySelectorAll('[x-data^="updateBookingForm"]').forEach((el) => {
            if (window.Alpine && !el._x_dataStack) {
                window.Alpine.initTree(el);
            }
        });
    });
    </script>
    @endpush

</x-app-layout>

