<div class="max-w-4xl mx-auto">
    {{-- Progress bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            @foreach([
                1 => __('Formule'),
                2 => __('Options'),
                3 => __('Coordonnées'),
                4 => __('Paiement'),
                5 => __('Confirmation'),
            ] as $step => $label)
                <button
                    type="button"
                    wire:click="goToStep({{ $step }})"
                    @class([
                        'flex items-center gap-2 text-sm font-medium transition-colors',
                        'text-primary-600' => $currentStep >= $step,
                        'text-sand-400 cursor-not-allowed' => $currentStep < $step,
                    ])
                    @if($currentStep < $step) disabled @endif
                >
                    <span @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold',
                        'bg-primary-500 text-white' => $currentStep >= $step,
                        'bg-sand-200 text-sand-500' => $currentStep < $step,
                    ])>
                        @if($currentStep > $step)
                            <x-heroicon-s-check class="w-5 h-5" />
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="hidden sm:inline">{{ $label }}</span>
                </button>
                @if($step < 5)
                    <div @class([
                        'flex-1 h-1 mx-2 rounded',
                        'bg-primary-500' => $currentStep > $step,
                        'bg-sand-200' => $currentStep <= $step,
                    ])></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Error message --}}
    @if($errorMessage)
        <x-ui.alert variant="danger" dismissible class="mb-6">
            {{ $errorMessage }}
        </x-ui.alert>
    @endif

    {{-- Step content --}}
    <div class="card p-6 md:p-8">

        {{-- Step 1: Formule --}}
        @if($currentStep === 1)
            <div class="space-y-6" wire:transition>
                <h2 class="text-2xl font-display font-bold text-sand-900">
                    {{ __('Choisissez votre formule') }}
                </h2>

                {{-- Date picker --}}
                <div>
                    <label class="label">{{ __('Date de l\'excursion') }} *</label>
                    <input
                        type="date"
                        wire:model.live="date"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="input"
                        required
                    />
                </div>

                {{-- Pricing mode --}}
                <div>
                    <label class="label">{{ __('Type de tour') }} *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label @class([
                            'relative flex flex-col items-center p-4 rounded-base border-2 cursor-pointer transition-all',
                            'border-primary-500 bg-primary-50' => $pricingMode === 'group',
                            'border-sand-200 hover:border-sand-300' => $pricingMode !== 'group',
                        ])>
                            <input
                                type="radio"
                                wire:model.live="pricingMode"
                                value="group"
                                class="sr-only"
                            />
                            <x-heroicon-o-users class="w-8 h-8 mb-2 text-primary-500" />
                            <span class="font-semibold">{{ __('Groupe') }}</span>
                            <span class="text-sm text-sand-500 text-center">{{ __('Rejoignez d\'autres voyageurs') }}</span>
                        </label>
                        <label @class([
                            'relative flex flex-col items-center p-4 rounded-base border-2 cursor-pointer transition-all',
                            'border-primary-500 bg-primary-50' => $pricingMode === 'private',
                            'border-sand-200 hover:border-sand-300' => $pricingMode !== 'private',
                        ])>
                            <input
                                type="radio"
                                wire:model.live="pricingMode"
                                value="private"
                                class="sr-only"
                            />
                            <x-heroicon-o-user class="w-8 h-8 mb-2 text-primary-500" />
                            <span class="font-semibold">{{ __('Privé') }}</span>
                            <span class="text-sm text-sand-500 text-center">{{ __('Tour exclusif pour vous') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Participants --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="label">{{ __('Adultes') }} *</label>
                        <div class="flex items-center border border-sand-300 rounded-base">
                            <button
                                type="button"
                                wire:click="$set('adults', Math.max(1, adults - 1))"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                                @if($adults <= 1) disabled @endif
                            >
                                <x-heroicon-s-minus class="w-4 h-4" />
                            </button>
                            <input
                                type="number"
                                wire:model.live="adults"
                                min="1"
                                class="w-full text-center border-0 focus:ring-0"
                            />
                            <button
                                type="button"
                                wire:click="$set('adults', adults + 1)"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                            >
                                <x-heroicon-s-plus class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Enfants') }} <span class="text-sand-400">(3-12)</span></label>
                        <div class="flex items-center border border-sand-300 rounded-base">
                            <button
                                type="button"
                                wire:click="$set('children', Math.max(0, children - 1))"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                            >
                                <x-heroicon-s-minus class="w-4 h-4" />
                            </button>
                            <input
                                type="number"
                                wire:model.live="children"
                                min="0"
                                class="w-full text-center border-0 focus:ring-0"
                            />
                            <button
                                type="button"
                                wire:click="$set('children', children + 1)"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                            >
                                <x-heroicon-s-plus class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Bébés') }} <span class="text-sand-400">(0-2)</span></label>
                        <div class="flex items-center border border-sand-300 rounded-base">
                            <button
                                type="button"
                                wire:click="$set('infants', Math.max(0, infants - 1))"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                            >
                                <x-heroicon-s-minus class="w-4 h-4" />
                            </button>
                            <input
                                type="number"
                                wire:model.live="infants"
                                min="0"
                                class="w-full text-center border-0 focus:ring-0"
                            />
                            <button
                                type="button"
                                wire:click="$set('infants', infants + 1)"
                                class="px-4 py-3 text-sand-500 hover:text-sand-700"
                            >
                                <x-heroicon-s-plus class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 2: Addons --}}
        @if($currentStep === 2)
            <div class="space-y-6" wire:transition>
                <h2 class="text-2xl font-display font-bold text-sand-900">
                    {{ __('Options supplémentaires') }}
                </h2>

                @if(count($this->availableAddons) > 0)
                    <div class="space-y-4">
                        @foreach($this->availableAddons as $addon)
                            <label @class([
                                'flex items-start gap-4 p-4 rounded-base border-2 cursor-pointer transition-all',
                                'border-primary-500 bg-primary-50' => isset($selectedAddons[$addon['id']]),
                                'border-sand-200 hover:border-sand-300' => !isset($selectedAddons[$addon['id']]),
                            ])>
                                <input
                                    type="checkbox"
                                    wire:click="toggleAddon({{ $addon['id'] }})"
                                    @checked(isset($selectedAddons[$addon['id']]))
                                    class="mt-1 rounded border-sand-300 text-primary-500 focus:ring-primary-500"
                                />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-sand-900">
                                            {{ $addon['translations'][0]['name'] ?? $addon['name'] ?? __('Option') }}
                                        </span>
                                        <span class="font-bold text-primary-500">
                                            +{{ number_format($addon['base_price'] ?? 0, 2) }} €
                                        </span>
                                    </div>
                                    @if(!empty($addon['translations'][0]['description']))
                                        <p class="text-sm text-sand-500 mt-1">
                                            {{ $addon['translations'][0]['description'] }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-sand-500">
                        <x-heroicon-o-gift class="w-12 h-12 mx-auto mb-3 text-sand-300" />
                        <p>{{ __('Aucune option disponible pour cette formule.') }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Step 3: Coordonnées --}}
        @if($currentStep === 3)
            <div class="space-y-6" wire:transition>
                <h2 class="text-2xl font-display font-bold text-sand-900">
                    {{ __('Vos coordonnées') }}
                </h2>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-ui.input
                            name="customerName"
                            label="{{ __('Nom complet') }}"
                            wire:model="customerName"
                            required
                            icon="user"
                        />
                    </div>
                    <div>
                        <x-ui.input
                            type="email"
                            name="customerEmail"
                            label="{{ __('Email') }}"
                            wire:model="customerEmail"
                            required
                            icon="envelope"
                        />
                    </div>
                    <div>
                        <x-ui.input
                            type="tel"
                            name="customerPhone"
                            label="{{ __('Téléphone (WhatsApp)') }}"
                            wire:model="customerPhone"
                            icon="phone"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.textarea
                            name="specialRequests"
                            label="{{ __('Demandes spéciales') }}"
                            wire:model="specialRequests"
                            rows="3"
                            hint="{{ __('Allergies, régimes alimentaires, besoins particuliers...') }}"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="marketingOptIn" class="mt-1 rounded border-sand-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-sand-600">
                                {{ __('J\'accepte de recevoir un email de rappel si je ne finalise pas ma réservation.') }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 4: Récap & Paiement --}}
        @if($currentStep === 4)
            <div class="space-y-6" wire:transition>
                <h2 class="text-2xl font-display font-bold text-sand-900">
                    {{ __('Récapitulatif & Paiement') }}
                </h2>

                {{-- Booking summary --}}
                @if($priceCalculation)
                    <div class="bg-sand-50 rounded-base p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sand-600">{{ __('Tour') }}</span>
                            <span class="font-medium">{{ $tour->translate()?->title ?? $tour->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sand-600">{{ __('Date') }}</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sand-600">{{ __('Participants') }}</span>
                            <span class="font-medium">
                                {{ $adults }} {{ __('adulte(s)') }}
                                @if($children > 0), {{ $children }} {{ __('enfant(s)') }}@endif
                                @if($infants > 0), {{ $infants }} {{ __('bébé(s)') }}@endif
                            </span>
                        </div>
                        <hr class="border-sand-200" />
                        <div class="flex justify-between">
                            <span class="text-sand-600">{{ __('Sous-total') }}</span>
                            <span>{{ number_format($priceCalculation['base_price'], 2) }} €</span>
                        </div>
                        @if($priceCalculation['addons_total'] > 0)
                            <div class="flex justify-between">
                                <span class="text-sand-600">{{ __('Options') }}</span>
                                <span>{{ number_format($priceCalculation['addons_total'], 2) }} €</span>
                            </div>
                        @endif
                        @if(isset($priceCalculation['discount']))
                            <div class="flex justify-between text-success-600">
                                <span>{{ __('Réduction') }} ({{ $appliedPromo['code'] }})</span>
                                <span>-{{ number_format($priceCalculation['discount'], 2) }} €</span>
                            </div>
                        @endif
                        <hr class="border-sand-200" />
                        <div class="flex justify-between text-lg font-bold">
                            <span>{{ __('Total') }}</span>
                            <span class="text-primary-500">
                                {{ number_format($priceCalculation['total_after_discount'] ?? $priceCalculation['total_price'], 2) }} €
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Promo code --}}
                <div>
                    <label class="label">{{ __('Code promo') }}</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="promoCode"
                            placeholder="{{ __('Entrez votre code') }}"
                            class="input flex-1"
                            @if($appliedPromo) disabled @endif
                        />
                        @if($appliedPromo)
                            <button
                                type="button"
                                wire:click="removePromoCode"
                                class="btn-outline px-4"
                            >
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="applyPromoCode"
                                class="btn-secondary px-6"
                            >
                                {{ __('Appliquer') }}
                            </button>
                        @endif
                    </div>
                    @if($promoError)
                        <p class="text-sm text-danger-500 mt-1">{{ $promoError }}</p>
                    @endif
                    @if($appliedPromo)
                        <p class="text-sm text-success-600 mt-1">
                            <x-heroicon-s-check-circle class="w-4 h-4 inline" />
                            {{ __('Code appliqué !') }}
                        </p>
                    @endif
                </div>

                {{-- Gift card & referral --}}
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="label">{{ __('Carte cadeau') }}</label>
                        <input type="text" wire:model="giftCardCode" placeholder="GC-XXXXXXXX" class="input w-full uppercase">
                    </div>
                    <div>
                        <label class="label">{{ __('Code parrainage') }}</label>
                        <input type="text" wire:model="referralCode" placeholder="REFXXXXXX" class="input w-full uppercase" @if($appliedPromo) disabled @endif>
                    </div>
                </div>

                {{-- Payment type --}}
                @if(config('marketing.deposit.enabled'))
                    <div>
                        <label class="label">{{ __('Mode de paiement') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 p-4 border rounded-base cursor-pointer {{ $paymentType === 'full' ? 'border-primary-500 bg-primary-50' : 'border-sand-200' }}">
                                <input type="radio" wire:model.live="paymentType" value="full" class="mt-1">
                                <span>
                                    <span class="font-semibold block">{{ __('Paiement intégral') }}</span>
                                    <span class="text-sm text-sand-600">{{ __('Payez la totalité maintenant') }}</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3 p-4 border rounded-base cursor-pointer {{ $paymentType === 'deposit' ? 'border-primary-500 bg-primary-50' : 'border-sand-200' }}">
                                <input type="radio" wire:model.live="paymentType" value="deposit" class="mt-1">
                                <span>
                                    <span class="font-semibold block">{{ __('Acompte :percent%', ['percent' => config('marketing.deposit.percent', 20)]) }}</span>
                                    <span class="text-sm text-sand-600">{{ __('Solde sur place le jour J') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Payment provider --}}
                @if(config('services.paypal.client_id'))
                    <div>
                        <label class="label">{{ __('Moyen de paiement') }}</label>
                        <div class="flex gap-3">
                            <button type="button" wire:click="switchPaymentProvider('stripe')"
                                class="flex-1 py-3 px-4 rounded-base border font-medium transition {{ $paymentProvider === 'stripe' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-sand-200' }}">
                                💳 Stripe
                            </button>
                            <button type="button" wire:click="switchPaymentProvider('paypal')"
                                class="flex-1 py-3 px-4 rounded-base border font-medium transition {{ $paymentProvider === 'paypal' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-sand-200' }}">
                                PayPal
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Stripe payment form --}}
                @if($stripeClientSecret)
                    <div
                        x-data="stripePayment('{{ $stripeClientSecret }}', '{{ config('services.stripe.key') }}')"
                        class="space-y-4"
                    >
                        <label class="label">{{ __('Informations de paiement') }}</label>
                        <div id="payment-element" class="p-4 border border-sand-300 rounded-base"></div>
                        <div id="payment-errors" class="text-sm text-danger-500"></div>

                        <button
                            type="button"
                            @click="submitPayment"
                            :disabled="processing"
                            class="w-full btn-primary py-4 text-lg font-semibold"
                        >
                            <span x-show="!processing">
                                {{ __('Payer') }} {{ number_format($payableAmount, 2) }} €
                                @if($paymentType === 'deposit')
                                    <span class="text-sm font-normal">({{ __('acompte') }})</span>
                                @endif
                            </span>
                            <span x-show="processing" class="flex items-center justify-center gap-2">
                                <x-ui.spinner size="sm" />
                                {{ __('Traitement en cours...') }}
                            </span>
                        </button>

                        <p class="text-xs text-sand-500 text-center">
                            <x-heroicon-o-lock-closed class="w-4 h-4 inline" />
                            {{ __('Paiement sécurisé par Stripe') }}
                        </p>
                    </div>
                @elseif($paypalApproveUrl)
                    <a href="{{ $paypalApproveUrl }}"
                       class="w-full btn-primary py-4 text-lg font-semibold text-center block">
                        {{ __('Payer avec PayPal') }} — {{ number_format($this->payableAmount, 2) }} €
                    </a>
                    <p class="text-xs text-sand-500 text-center">
                        {{ __('Vous serez redirigé vers PayPal pour finaliser le paiement.') }}
                    </p>
                @else
                    <button
                        type="button"
                        wire:click="createBooking"
                        wire:loading.attr="disabled"
                        class="w-full btn-primary py-4 text-lg font-semibold"
                    >
                        <span wire:loading.remove>{{ __('Procéder au paiement') }}</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            <x-ui.spinner size="sm" />
                            {{ __('Préparation...') }}
                        </span>
                    </button>
                @endif
            </div>
        @endif

        {{-- Step 5: Confirmation --}}
        @if($currentStep === 5)
            <div class="text-center py-8 space-y-6" wire:transition>
                <div class="w-20 h-20 mx-auto bg-success-100 rounded-full flex items-center justify-center">
                    <x-heroicon-s-check class="w-10 h-10 text-success-500" />
                </div>

                <div>
                    <h2 class="text-2xl font-display font-bold text-sand-900 mb-2">
                        {{ __('Réservation confirmée !') }}
                    </h2>
                    <p class="text-sand-600">
                        {{ __('Merci pour votre réservation. Vous allez recevoir un email de confirmation.') }}
                    </p>
                </div>

                @if($booking)
                    <div class="bg-sand-50 rounded-base p-6 inline-block">
                        <p class="text-sm text-sand-500 mb-1">{{ __('Référence de réservation') }}</p>
                        <p class="text-2xl font-bold text-primary-500">{{ $booking->reference }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a
                            href="{{ route('booking.voucher', $booking->reference) }}"
                            class="btn-primary"
                        >
                            <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                            {{ __('Télécharger le voucher') }}
                        </a>
                        <a
                            href="{{ route('home', app()->getLocale()) }}"
                            class="btn-outline"
                        >
                            {{ __('Retour à l\'accueil') }}
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Navigation buttons --}}
        @if($currentStep < 5)
            <div class="flex justify-between mt-8 pt-6 border-t border-sand-200">
                @if($currentStep > 1)
                    <button
                        type="button"
                        wire:click="previousStep"
                        class="btn-ghost"
                    >
                        <x-heroicon-o-arrow-left class="w-5 h-5" />
                        {{ __('Retour') }}
                    </button>
                @else
                    <div></div>
                @endif

                @if($currentStep < 4)
                    <button
                        type="button"
                        wire:click="nextStep"
                        class="btn-primary"
                    >
                        {{ __('Continuer') }}
                        <x-heroicon-o-arrow-right class="w-5 h-5" />
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Price summary sidebar (desktop) --}}
    @if($currentStep < 5 && $priceCalculation)
        <div class="hidden lg:block fixed bottom-6 right-6 w-72 card p-4 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sand-600">{{ __('Total') }}</span>
                <span class="text-2xl font-bold text-primary-500">
                    {{ number_format($priceCalculation['total_after_discount'] ?? $priceCalculation['total_price'], 2) }} €
                </span>
            </div>
            <p class="text-xs text-success-600">
                <x-heroicon-s-check-circle class="w-4 h-4 inline" />
                {{ __('Annulation gratuite 24h avant') }}
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    function stripePayment(clientSecret, publishableKey) {
        return {
            stripe: null,
            elements: null,
            paymentElement: null,
            processing: false,

            init() {
                this.stripe = Stripe(publishableKey);
                this.elements = this.stripe.elements({
                    clientSecret: clientSecret,
                    appearance: {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: '#C1440E',
                            colorBackground: '#ffffff',
                            colorText: '#443D2C',
                            colorDanger: '#EF4444',
                            fontFamily: 'Inter, system-ui, sans-serif',
                            borderRadius: '0.75rem',
                        }
                    }
                });

                this.paymentElement = this.elements.create('payment');
                this.paymentElement.mount('#payment-element');
            },

            async submitPayment() {
                this.processing = true;
                document.getElementById('payment-errors').textContent = '';

                const { error } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    confirmParams: {
                        return_url: window.location.href,
                    },
                    redirect: 'if_required'
                });

                if (error) {
                    document.getElementById('payment-errors').textContent = error.message;
                    this.processing = false;
                    Livewire.dispatch('payment-failed', { error: error.message });
                } else {
                    Livewire.dispatch('payment-succeeded', { paymentIntentId: clientSecret.split('_secret_')[0] });
                }
            }
        }
    }
</script>
@endpush
