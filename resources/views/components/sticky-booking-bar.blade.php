@props([
    'tour',
    'minPrice' => null,
    'currency' => 'EUR',
])

@php
    $price = $minPrice ?? $tour->price_from ?? $tour->getMinPrice() ?? $tour->price ?? 0;
    $currencySymbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'MAD' => 'DH'];
    $symbol = $currencySymbols[$currency] ?? '€';

    $priceDisplay = in_array($currency, ['USD', 'GBP'])
        ? $symbol . number_format($price, 0, ',', ' ')
        : number_format($price, 0, ',', ' ') . $symbol;
@endphp

<div
    x-data="{
        visible: false,
        init() {
            this.checkVisibility();
            window.addEventListener('scroll', () => this.checkVisibility());
        },
        checkVisibility() {
            const heroSection = document.getElementById('tour-hero');
            if (heroSection) {
                const rect = heroSection.getBoundingClientRect();
                this.visible = rect.bottom < 100;
            } else {
                this.visible = window.scrollY > 300;
            }
        }
    }"
>
    {{-- Mobile sticky booking bar --}}
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 inset-x-0 z-40 lg:hidden bg-white border-t border-sand-200 shadow-lg safe-area-inset-bottom"
        x-cloak
    >
        <div class="container-app py-3">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-1.5 flex-wrap">
                        <span class="text-xs text-sand-500">{{ __('À partir de') }}</span>
                        <span class="text-xl font-bold text-primary-500">{{ $priceDisplay }}</span>
                        <span class="text-sm text-sand-500">/ {{ __('pers.') }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-success-600 mt-0.5">
                        <x-heroicon-s-check-circle class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate">{{ __('Annulation gratuite 24h') }}</span>
                    </div>
                </div>

                <a
                    href="{{ route('tours.booking.wizard', $tour) }}"
                    class="flex-shrink-0 btn-primary px-6 py-3 text-base font-semibold whitespace-nowrap"
                >
                    {{ __('Réserver') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Spacer to prevent content from being hidden behind the bar --}}
    <div class="h-20 lg:hidden" x-show="visible" x-cloak></div>
</div>
