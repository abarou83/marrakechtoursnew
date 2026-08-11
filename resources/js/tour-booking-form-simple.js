import { readTourPageConfig } from './tour-page-config';

/** Date locale YYYY-MM-DD (évite le décalage UTC de toISOString). */
export function localDateString(date = new Date()) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${y}-${m}-${day}`;
}

function simpleBookingFormFactory(tourIdFromDom) {
    const config = readTourPageConfig();
    const formConfig = config?.bookingForm ?? {};
    const labels = formConfig.labels ?? {};
    const tourId = tourIdFromDom ?? formConfig.tourId;
    const tourSlug = formConfig.tourSlug ?? tourId;
    const dateLocale = formConfig.dateLocale ?? 'fr-FR';
    const months = formConfig.months ?? [];

    return {
        tourId,
        tourSlug,
        selectedDate: '',
        adults: 1,
        children: 0,
        infants: 0,
        showDateDropdown: false,
        showParticipantsDropdown: false,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        minDate: '',

        init() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            this.minDate = localDateString(today);
            this.selectedDate = this.minDate;
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
        },

        get calendarDays() {
            const days = [];
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay() + (firstDay.getDay() === 0 ? -6 : 1));

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const todayStr = localDateString(today);
            const todayTime = today.getTime();

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                date.setHours(0, 0, 0, 0);
                const dateStr = localDateString(date);
                const dateTime = date.getTime();
                const isToday = dateStr === todayStr;
                const isSelected = dateStr === this.selectedDate;
                const isDisabled = dateTime < todayTime;
                const isOtherMonth = date.getMonth() !== this.currentMonth;
                const uniqueKey = `${this.currentYear}-${this.currentMonth}-${i}-${dateStr}`;

                days.push({
                    key: uniqueKey,
                    date: dateStr,
                    day: date.getDate(),
                    isToday,
                    isSelected,
                    disabled: isDisabled,
                    isOtherMonth,
                });
            }

            return days;
        },

        initCalendar() {},

        isPastMonth() {
            const today = new Date();

            return (
                this.currentYear < today.getFullYear()
                || (this.currentYear === today.getFullYear() && this.currentMonth < today.getMonth())
            );
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
            const name = months[this.currentMonth] ?? '';

            return `${name} ${this.currentYear}`.trim();
        },

        handleDateClick(day) {
            if (day.disabled) {
                return false;
            }
            if (day.isOtherMonth) {
                const selectedDateObj = new Date(`${day.date}T12:00:00`);
                this.currentMonth = selectedDateObj.getMonth();
                this.currentYear = selectedDateObj.getFullYear();
            }
            this.selectedDate = day.date;
            this.showDateDropdown = false;

            return true;
        },

        formatDate(dateStr) {
            if (!dateStr) {
                return '';
            }
            const date = new Date(`${dateStr}T12:00:00`);

            return date.toLocaleDateString(dateLocale, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },

        getDateDayName(dateStr) {
            if (!dateStr) {
                return '';
            }
            const date = new Date(`${dateStr}T12:00:00`);

            return date.toLocaleDateString(dateLocale, { weekday: 'long' });
        },

        get totalParticipants() {
            return this.adults + this.children + this.infants;
        },

        getParticipantsSummary() {
            const total = this.totalParticipants;
            if (total === 1) {
                return labels.oneParticipant ?? '1 participant';
            }

            return `${total} ${labels.participants ?? 'participants'}`;
        },

        getParticipantsDetail() {
            const parts = [];
            if (this.adults > 0) {
                parts.push(
                    `${this.adults} ${this.adults === 1 ? (labels.adult ?? 'adult') : (labels.adults ?? 'adults')}`,
                );
            }
            if (this.children > 0) {
                parts.push(
                    `${this.children} ${this.children === 1 ? (labels.child ?? 'child') : (labels.children ?? 'children')}`,
                );
            }
            if (this.infants > 0) {
                parts.push(
                    `${this.infants} ${this.infants === 1 ? (labels.baby ?? 'baby') : (labels.babies ?? 'babies')}`,
                );
            }

            return parts.length > 0 ? parts.join(', ') : (labels.noParticipant ?? '');
        },

        validateStep1() {
            if (!this.selectedDate || this.totalParticipants < 1) {
                alert(labels.selectDateParticipants ?? 'Please select a date and at least one participant.');

                return;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(`${this.selectedDate}T12:00:00`);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate.getTime() < today.getTime()) {
                alert(labels.noPastDate ?? 'You cannot select a past date.');
                this.selectedDate = localDateString(today);

                return;
            }

            const params = new URLSearchParams({
                date: this.selectedDate,
                participants: String(this.totalParticipants),
                adults: String(this.adults),
                children: String(this.children),
                infants: String(this.infants),
            });

            window.location.href = `/tours/${this.tourSlug}/select-formula?${params.toString()}`;
        },
    };
}

function registerSimpleBookingForm() {
    if (typeof window.Alpine === 'undefined') {
        return;
    }

    window.Alpine.data('simpleBookingForm', simpleBookingFormFactory);
}

function initBookingFormTrees() {
    if (typeof window.Alpine === 'undefined') {
        return;
    }

    document.querySelectorAll('#booking-form-container[x-data]').forEach((el) => {
        if (!el._x_dataStack) {
            window.Alpine.initTree(el);
        }
    });
}

document.addEventListener('livewire:init', registerSimpleBookingForm);
document.addEventListener('alpine:init', registerSimpleBookingForm);
document.addEventListener('DOMContentLoaded', () => {
    registerSimpleBookingForm();
    initBookingFormTrees();
});
