<div>
    @unless($dismissed)
    <div x-data="{
            show: @entangle('show'),
            init() {
                if (window.innerWidth < 768) return;
                document.addEventListener('mouseout', (e) => {
                    if (e.clientY <= 0 && !this.show) {
                        this.show = true;
                        @this.set('show', true);
                    }
                });
            }
        }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50"
        style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative" @click.away="$wire.dismiss()">
            <button type="button" wire:click="dismiss" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Avant de partir...') }}</h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Profitez de :percent% de réduction sur votre première réservation avec le code :', ['percent' => $promoPercent]) }}
                </p>
                <div class="bg-gray-100 rounded-lg px-4 py-3 mb-6">
                    <code class="text-lg font-bold text-primary-600">{{ $promoCode }}</code>
                </div>
                <a href="{{ route('tours.index') }}"
                   class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg transition mb-3">
                    {{ __('Découvrir nos tours') }}
                </a>
                <button type="button" wire:click="dismiss" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Non merci') }}
                </button>
            </div>
        </div>
    </div>
    @endunless
</div>
