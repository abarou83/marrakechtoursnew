<div class="bg-white rounded-2xl shadow-lg p-8">
    @if($paid && $giftCard)
        <div class="text-center py-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Carte cadeau activée !') }}</h2>
            <div class="bg-gray-100 rounded-xl px-6 py-4 my-4 inline-block">
                <code class="text-2xl font-bold text-primary-600 tracking-wider">{{ $giftCard->code }}</code>
            </div>
            <p class="text-lg font-semibold">{{ number_format($giftCard->initial_amount, 2) }} {{ $giftCard->currency }}</p>
        </div>
    @elseif($stripeClientSecret)
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Paiement sécurisé') }}</h2>
        <div x-data="giftCardStripe('{{ $stripeClientSecret }}', '{{ config('services.stripe.key') }}')" class="space-y-4">
            <div id="gift-card-payment-element" class="p-4 border border-gray-200 rounded-lg"></div>
            <div id="gift-card-payment-errors" class="text-sm text-red-600"></div>
            <button type="button" @click="submitPayment" :disabled="processing"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg transition">
                <span x-show="!processing">{{ __('Payer') }} {{ number_format($amount, 2) }} €</span>
                <span x-show="processing">{{ __('Traitement...') }}</span>
            </button>
        </div>
    @else
        <form wire:submit="startCheckout" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Montant') }}</label>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                    @foreach($amounts as $preset)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="amount" value="{{ $preset }}" class="sr-only peer">
                            <span class="block text-center py-3 border-2 rounded-lg peer-checked:border-primary-600 peer-checked:bg-primary-50 font-semibold">{{ $preset }} €</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom du destinataire') }}</label>
                    <input type="text" wire:model="recipientName" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email du destinataire') }}</label>
                    <input type="email" wire:model="recipientEmail" class="w-full rounded-lg border-gray-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Message') }}</label>
                <textarea wire:model="message" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
            </div>
            @if($errorMessage)
                <p class="text-red-600 text-sm">{{ $errorMessage }}</p>
            @endif
            <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg transition">
                {{ __('Continuer vers le paiement') }}
            </button>
        </form>
    @endif
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    function giftCardStripe(clientSecret, publishableKey) {
        return {
            stripe: null,
            elements: null,
            processing: false,
            init() {
                this.stripe = Stripe(publishableKey);
                this.elements = this.stripe.elements({ clientSecret });
                this.elements.create('payment').mount('#gift-card-payment-element');
            },
            async submitPayment() {
                this.processing = true;
                const { error } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    redirect: 'if_required'
                });
                if (error) {
                    document.getElementById('gift-card-payment-errors').textContent = error.message;
                    this.processing = false;
                } else {
                    Livewire.dispatch('gift-card-payment-succeeded');
                }
            }
        };
    }
</script>
@endpush
