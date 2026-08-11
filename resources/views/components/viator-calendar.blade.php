@props([
    'name' => 'date',
    'productId' => null,
    'defaultDate' => null,
    'minDate' => null,
    'availableDates' => [],
    'prices' => []
])

@php
    $minDate = $minDate ?? date('Y-m-d');
    $calendarId = 'viator-calendar-' . uniqid();
@endphp

<div x-data="viatorCalendar({
    name: '{{ $name }}',
    defaultDate: @js($defaultDate),
    minDate: '{{ $minDate }}',
    availableDates: @js($availableDates),
    prices: @js($prices)
})" 
     class="viator-calendar-container relative"
     x-init="init()"
     @click.away="isOpen = false">
    
    {{-- Hidden input for form submission --}}
    <input type="hidden" 
           :name="name" 
           :id="name + '_value'"
           :value="selectedDate"
           x-model="selectedDate">
    
    {{-- Input field that opens calendar --}}
    <div class="relative">
        <input type="text" 
               @click="isOpen = !isOpen"
               :value="selectedDate ? formatDisplayDate(selectedDate) : ''"
               placeholder="Sélectionner une date"
               readonly
               class="w-full p-3 border-2 border-gray-300 rounded-lg text-lg focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20 cursor-pointer bg-white pr-10">
        <i class="fas fa-calendar-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
    </div>
    
    {{-- Calendar wrapper (shown when isOpen is true) --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-init="
            $watch('isOpen', value => {
                if (value) {
                    setTimeout(() => {
                        const calendar = $el;
                        const input = $el.previousElementSibling?.querySelector('input');
                        if (!input) return;
                        
                        const inputRect = input.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        const viewportWidth = window.innerWidth;
                        const calendarHeight = calendar.offsetHeight || 600;
                        const calendarWidth = calendar.offsetWidth || 850;
                        const spaceBelow = viewportHeight - inputRect.bottom;
                        const spaceAbove = inputRect.top;
                        const spaceLeft = inputRect.left;
                        const spaceRight = viewportWidth - inputRect.right;
                        
                        // Positionnement vertical : si pas assez d'espace en bas, ouvrir vers le haut
                        if (spaceBelow < calendarHeight && spaceAbove > spaceBelow) {
                            calendar.style.bottom = '100%';
                            calendar.style.top = 'auto';
                            calendar.style.marginBottom = '0.5rem';
                            calendar.style.marginTop = '0';
                        } else {
                            calendar.style.top = '100%';
                            calendar.style.bottom = 'auto';
                            calendar.style.marginTop = '0.5rem';
                            calendar.style.marginBottom = '0';
                        }
                        
                        // Positionnement horizontal : aligner le bord droit du calendrier avec le bord droit du champ date
                        // Le calendrier s'ouvre à gauche, mais son bord droit est aligné avec le bord droit de l'input
                        calendar.style.right = (viewportWidth - inputRect.right) + 'px';
                        calendar.style.left = 'auto';
                        
                        // Vérifier si le calendrier dépasse à gauche de l'écran
                        const calendarLeft = viewportWidth - inputRect.right - calendarWidth;
                        if (calendarLeft < 0) {
                            // Si le calendrier dépasse, ajuster pour qu'il reste visible
                            calendar.style.right = '0';
                            calendar.style.left = 'auto';
                            calendar.style.maxWidth = (inputRect.right - 20) + 'px'; // 20px de marge
                        }
                        
                        // Limiter la hauteur maximale pour éviter le dépassement
                        const maxHeight = Math.min(calendarHeight, viewportHeight - 40);
                        calendar.style.maxHeight = maxHeight + 'px';
                    }, 50);
                }
            })
         "
         class="absolute z-50 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden w-[calc(100vw-2rem)] sm:w-auto sm:min-w-[800px] sm:max-w-[900px] max-h-[80vh] overflow-y-auto"
         style="max-width: calc(100vw - 2rem);">
        {{-- Calendar Header --}}
        <div class="px-3 sm:px-4 py-2 sm:py-3" style="background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ primary_color() }}dd 100%);">
            <div class="flex items-center justify-between">
                <button @click="previousMonth()" 
                        class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition-colors"
                        type="button">
                    <i class="fas fa-chevron-left text-xs sm:text-sm"></i>
                </button>
                
                <div class="flex items-center gap-2 sm:gap-4">
                    <h3 class="text-white font-semibold text-xs sm:text-base px-1 sm:px-2" x-text="currentMonthLabel"></h3>
                    <span class="text-white/80 text-xs sm:text-sm hidden sm:inline">•</span>
                    <h3 class="text-white font-semibold text-xs sm:text-base px-1 sm:px-2 hidden sm:inline" x-text="nextMonthLabel"></h3>
                </div>
                
                <button @click="nextMonth()" 
                        class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition-colors"
                        type="button">
                    <i class="fas fa-chevron-right text-xs sm:text-sm"></i>
                </button>
            </div>
        </div>
        
        {{-- Calendar Grid --}}
        <div class="p-2 sm:p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                {{-- Premier mois --}}
                <div>
                    <h4 class="text-xs sm:text-sm font-semibold text-gray-700 mb-2 text-center" x-text="currentMonthLabel"></h4>
                    {{-- Weekday headers --}}
                    <div class="grid grid-cols-7 gap-0.5 sm:gap-1 mb-1 sm:mb-2">
                        <template x-for="day in weekDays" :key="day">
                            <div class="text-center text-[10px] sm:text-xs font-semibold text-gray-600 py-1" x-text="day"></div>
                        </template>
                    </div>
                    
                    {{-- Calendar days --}}
                    <div class="grid grid-cols-7 gap-0.5 sm:gap-1" x-show="calendarDays.length > 0">
                        <template x-for="(day, index) in calendarDays" :key="index">
                    <button
                        type="button"
                        @click="selectDate(day)"
                        :disabled="!day.available"
                        :class="{
                            'text-white font-semibold ring-1 sm:ring-2 ring-offset-1': day.selected,
                            'bg-gray-50 text-gray-400 cursor-not-allowed': !day.available,
                            'hover:bg-gray-50 border-1 sm:border-2': day.available && !day.selected && !day.isToday,
                            'border-1 sm:border-2': day.isToday && !day.selected,
                            'bg-white text-gray-900': day.available && !day.selected && !day.isToday
                        }"
                        :style="day.selected ? {
                            backgroundColor: '{{ primary_color() }}',
                            borderColor: '{{ primary_color() }}',
                            '--tw-ring-color': '{{ primary_color() }}'
                        } : day.available && !day.selected && !day.isToday ? {
                            borderColor: '{{ primary_color() }}33'
                        } : day.isToday ? {
                            borderColor: '{{ primary_color() }}'
                        } : {}"
                        class="relative flex items-center justify-center p-1 sm:p-2 rounded sm:rounded-md transition-all duration-200 min-h-[32px] sm:min-h-[40px] border border-transparent text-xs sm:text-sm"
                        x-transition>
                        
                        {{-- Day number --}}
                        <span class="text-sm font-medium" x-text="day.day"></span>
                        
                        {{-- Unavailable indicator --}}
                        <template x-if="!day.available">
                            <i class="fas fa-times text-xs text-gray-300"></i>
                        </template>
                    </button>
                </template>
                    </div>
                </div>
                
                {{-- Deuxième mois --}}
                <div>
                    <h4 class="text-xs sm:text-sm font-semibold text-gray-700 mb-2 text-center" x-text="nextMonthLabel"></h4>
                    {{-- Weekday headers --}}
                    <div class="grid grid-cols-7 gap-0.5 sm:gap-1 mb-1 sm:mb-2">
                        <template x-for="day in weekDays" :key="day">
                            <div class="text-center text-[10px] sm:text-xs font-semibold text-gray-600 py-1" x-text="day"></div>
                        </template>
                    </div>
                    
                    {{-- Calendar days --}}
                    <div class="grid grid-cols-7 gap-0.5 sm:gap-1" x-show="calendarDays2.length > 0">
                        <template x-for="(day, index) in calendarDays2" :key="index">
                            <button
                                type="button"
                                @click="selectDate(day)"
                                :disabled="!day.available"
                                :class="{
                                    'text-white font-semibold ring-1 sm:ring-2 ring-offset-1': day.selected,
                                    'bg-gray-50 text-gray-400 cursor-not-allowed': !day.available,
                                    'hover:bg-gray-50 border-1 sm:border-2': day.available && !day.selected && !day.isToday,
                                    'border-1 sm:border-2': day.isToday && !day.selected,
                                    'bg-white text-gray-900': day.available && !day.selected && !day.isToday
                                }"
                                :style="day.selected ? {
                                    backgroundColor: '{{ primary_color() }}',
                                    borderColor: '{{ primary_color() }}',
                                    '--tw-ring-color': '{{ primary_color() }}'
                                } : day.available && !day.selected && !day.isToday ? {
                                    borderColor: '{{ primary_color() }}33'
                                } : day.isToday ? {
                                    borderColor: '{{ primary_color() }}'
                                } : {}"
                                class="relative flex items-center justify-center p-1 sm:p-2 rounded sm:rounded-md transition-all duration-200 min-h-[32px] sm:min-h-[40px] border border-transparent text-xs sm:text-sm"
                                x-transition>
                                
                                {{-- Day number --}}
                                <span class="text-sm font-medium" x-text="day.day"></span>
                                
                                {{-- Unavailable indicator --}}
                                <template x-if="!day.available">
                                    <i class="fas fa-times text-xs text-gray-300"></i>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            
            {{-- Loading state --}}
            <div x-show="loading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                <p class="text-gray-600 mt-2">Chargement des disponibilités...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .viator-calendar-container button:disabled {
        pointer-events: none;
    }
    
    .viator-calendar-container button[disabled] {
        opacity: 0.5;
    }
</style>

@once
@push('scripts')
<script>
(function() {
    if (typeof window.viatorCalendarInitialized !== 'undefined') {
        return;
    }
    window.viatorCalendarInitialized = true;
    
    const initViatorCalendar = () => {
        if (typeof Alpine === 'undefined') {
            return;
        }
        
        Alpine.data('viatorCalendar', (config) => ({
        name: config.name || 'date',
        selectedDate: config.defaultDate || null,
        currentDate: new Date(),
        calendarDays: [],
        calendarDays2: [], // Deuxième mois
        loading: false,
        isOpen: false,
        availableDates: config.availableDates || [],
        prices: config.prices || {},
        
        weekDays: [@js(__('Sun')), @js(__('Mon')), @js(__('Tue')), @js(__('Wed')), @js(__('Thu')), @js(__('Fri')), @js(__('Sat'))],
        
        get currentMonthLabel() {
            const months = [@js(__('January')), @js(__('February')), @js(__('March')), @js(__('April')), @js(__('May')), @js(__('June')), @js(__('July')), @js(__('August')), @js(__('September')), @js(__('October')), @js(__('November')), @js(__('December'))];
            return months[this.currentDate.getMonth()] + ' ' + this.currentDate.getFullYear();
        },
        
        get nextMonthLabel() {
            const months = [@js(__('January')), @js(__('February')), @js(__('March')), @js(__('April')), @js(__('May')), @js(__('June')), @js(__('July')), @js(__('August')), @js(__('September')), @js(__('October')), @js(__('November')), @js(__('December'))];
            const nextMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            return months[nextMonth.getMonth()] + ' ' + nextMonth.getFullYear();
        },
        
        init() {
            this.generateCalendar();
        },
        
        generateCalendar() {
            this.generateMonth(this.currentDate, 'calendarDays');
            // Générer le mois suivant
            const nextMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            this.generateMonth(nextMonth, 'calendarDays2');
        },
        
        generateMonth(monthDate, targetArray) {
            this.loading = true;
            
            const year = monthDate.getFullYear();
            const month = monthDate.getMonth();
            
            // First day of the month
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
            const startDay = firstDay.getDay();
            
            // Number of days in the month
            const daysInMonth = lastDay.getDate();
            
            // Calculate previous month's days to fill the first week
            const prevMonth = new Date(year, month, 0);
            const daysInPrevMonth = prevMonth.getDate();
            
            // Initialize the target array
            if (targetArray === 'calendarDays') {
                this.calendarDays = [];
            } else {
                this.calendarDays2 = [];
            }
            
            // Add previous month's trailing days
            for (let i = startDay - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const date = new Date(year, month - 1, day);
                const dateStr = this.formatDate(date);
                
                if (targetArray === 'calendarDays') {
                    this.calendarDays.push({
                        day: day,
                        date: dateStr,
                        available: false,
                        isCurrentMonth: false,
                        isToday: false,
                        selected: false,
                        price: null
                    });
                } else {
                    this.calendarDays2.push({
                        day: day,
                        date: dateStr,
                        available: false,
                        isCurrentMonth: false,
                        isToday: false,
                        selected: false,
                        price: null
                    });
                }
            }
            
            // Add current month's days
            const today = new Date();
            const todayStr = this.formatDate(today);
            const minDateStr = config.minDate || todayStr;
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = this.formatDate(date);
                const isToday = dateStr === todayStr;
                const isPast = dateStr < minDateStr;
                
                // Toutes les dates futures sont disponibles (sélection libre)
                const isAvailable = !isPast;
                
                // Get price for this date (utiliser le prix par défaut ou le prix spécifique si disponible)
                let price = this.prices[dateStr];
                if (!price && this.prices.default !== undefined) {
                    price = this.prices.default;
                } else if (!price && Object.keys(this.prices).length > 0) {
                    // Utiliser le premier prix disponible
                    price = Object.values(this.prices)[0];
                }
                
                const dayObj = {
                    day: day,
                    date: dateStr,
                    available: isAvailable,
                    isCurrentMonth: true,
                    isToday: isToday,
                    selected: this.selectedDate === dateStr,
                    price: price ? this.formatPrice(price) : null
                };
                
                if (targetArray === 'calendarDays') {
                    this.calendarDays.push(dayObj);
                } else {
                    this.calendarDays2.push(dayObj);
                }
            }
            
            // Fill remaining days to complete the last week (6 rows = 42 days)
            const targetDays = targetArray === 'calendarDays' ? this.calendarDays : this.calendarDays2;
            const totalDays = targetDays.length;
            const remainingDays = 42 - totalDays;
            
            for (let day = 1; day <= remainingDays; day++) {
                const date = new Date(year, month + 1, day);
                const dateStr = this.formatDate(date);
                
                const dayObj = {
                    day: day,
                    date: dateStr,
                    available: false,
                    isCurrentMonth: false,
                    isToday: false,
                    selected: false,
                    price: null
                };
                
                if (targetArray === 'calendarDays') {
                    this.calendarDays.push(dayObj);
                } else {
                    this.calendarDays2.push(dayObj);
                }
            }
            
            // Set loading to false only after both months are generated
            if (targetArray === 'calendarDays2') {
                this.loading = false;
            }
        },
        
        selectDate(day) {
            if (!day.available) return;
            
            // Update selected date
            this.selectedDate = day.date;
            
            // Update calendar to reflect selection
            this.calendarDays.forEach(d => {
                d.selected = d.date === day.date;
            });
            this.calendarDays2.forEach(d => {
                d.selected = d.date === day.date;
            });
            
            // Close calendar after selection
            this.isOpen = false;
            
            // Emit event for parent component
            this.$dispatch('date-selected', {
                date: day.date,
                price: day.price
            });
        },
        
        formatDisplayDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr + 'T00:00:00');
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },
        
        previousMonth() {
            this.currentDate = new Date(
                this.currentDate.getFullYear(),
                this.currentDate.getMonth() - 1,
                1
            );
            this.generateCalendar();
        },
        
        nextMonth() {
            this.currentDate = new Date(
                this.currentDate.getFullYear(),
                this.currentDate.getMonth() + 1,
                1
            );
            this.generateCalendar();
        },
        
        formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        
        formatPrice(price) {
            if (typeof price === 'number') {
                // Utiliser la devise active depuis le cookie/session
                const currency = document.cookie
                    .split('; ')
                    .find(row => row.startsWith('currency='))
                    ?.split('=')[1] || 'EUR';
                
                // Symbols pour les devises principales
                const symbols = {
                    'EUR': '€',
                    'USD': '$',
                    'MAD': 'MAD',
                    'GBP': '£'
                };
                
                const symbol = symbols[currency] || '€';
                const formatted = Math.round(price).toLocaleString('fr-FR');
                return formatted + ' ' + symbol;
            }
            return price;
        }
    }));
    };
    
    if (typeof Alpine !== 'undefined') {
        initViatorCalendar();
    } else {
        document.addEventListener('alpine:init', () => {
            initViatorCalendar();
        });
    }
})();
</script>
@endpush
@endonce

