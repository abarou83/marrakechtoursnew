<div>
    @if($show)
        <div 
            x-data="{ expanded: @entangle('showDetails') }"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-0 inset-x-0 z-50 p-4"
        >
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-2xl border border-sand-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                            <x-heroicon-o-shield-check class="w-6 h-6 text-primary-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-sand-900 mb-2">
                                {{ __('Nous utilisons des cookies') }}
                            </h3>
                            <p class="text-sand-600 text-sm">
                                {{ __('Nous utilisons des cookies pour améliorer votre expérience, analyser le trafic et personnaliser le contenu. Vous pouvez accepter tous les cookies ou personnaliser vos préférences.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Cookie Details --}}
                    <div x-show="expanded" x-collapse class="mt-6 pt-6 border-t border-sand-200">
                        <div class="space-y-4">
                            {{-- Necessary --}}
                            <label class="flex items-start gap-4 p-4 bg-sand-50 rounded-lg">
                                <input 
                                    type="checkbox" 
                                    checked 
                                    disabled
                                    class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500"
                                >
                                <div class="flex-1">
                                    <span class="font-medium text-sand-900">{{ __('Cookies nécessaires') }}</span>
                                    <span class="text-xs text-sand-500 ml-2">{{ __('(toujours actifs)') }}</span>
                                    <p class="text-sm text-sand-600 mt-1">
                                        {{ __('Ces cookies sont indispensables au fonctionnement du site (session, sécurité, panier).') }}
                                    </p>
                                </div>
                            </label>

                            {{-- Analytics --}}
                            <label class="flex items-start gap-4 p-4 bg-sand-50 rounded-lg cursor-pointer hover:bg-sand-100 transition">
                                <input 
                                    type="checkbox" 
                                    wire:model="analytics"
                                    class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500"
                                >
                                <div class="flex-1">
                                    <span class="font-medium text-sand-900">{{ __('Cookies analytiques') }}</span>
                                    <p class="text-sm text-sand-600 mt-1">
                                        {{ __('Nous aident à comprendre comment vous utilisez le site pour l\'améliorer (Google Analytics).') }}
                                    </p>
                                </div>
                            </label>

                            {{-- Marketing --}}
                            <label class="flex items-start gap-4 p-4 bg-sand-50 rounded-lg cursor-pointer hover:bg-sand-100 transition">
                                <input 
                                    type="checkbox" 
                                    wire:model="marketing"
                                    class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500"
                                >
                                <div class="flex-1">
                                    <span class="font-medium text-sand-900">{{ __('Cookies marketing') }}</span>
                                    <p class="text-sm text-sand-600 mt-1">
                                        {{ __('Permettent de vous proposer des publicités personnalisées (Facebook Pixel, Google Ads).') }}
                                    </p>
                                </div>
                            </label>

                            {{-- Preferences --}}
                            <label class="flex items-start gap-4 p-4 bg-sand-50 rounded-lg cursor-pointer hover:bg-sand-100 transition">
                                <input 
                                    type="checkbox" 
                                    wire:model="preferences"
                                    class="mt-1 w-5 h-5 rounded border-sand-300 text-primary-500 focus:ring-primary-500"
                                >
                                <div class="flex-1">
                                    <span class="font-medium text-sand-900">{{ __('Cookies de préférences') }}</span>
                                    <p class="text-sm text-sand-600 mt-1">
                                        {{ __('Mémorisent vos choix (langue, devise, thème) pour une meilleure expérience.') }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex flex-col sm:flex-row items-center gap-3">
                        <button 
                            wire:click="acceptAll"
                            class="w-full sm:w-auto btn-primary px-6"
                        >
                            {{ __('Tout accepter') }}
                        </button>
                        
                        <button 
                            wire:click="acceptNecessary"
                            class="w-full sm:w-auto btn-outline px-6"
                        >
                            {{ __('Nécessaires uniquement') }}
                        </button>

                        <button 
                            wire:click="toggleDetails"
                            class="w-full sm:w-auto text-sm text-primary-600 hover:underline"
                        >
                            <span x-text="expanded ? '{{ __('Masquer les détails') }}' : '{{ __('Personnaliser') }}'"></span>
                        </button>

                        <button 
                            x-show="expanded"
                            wire:click="saveCustom"
                            class="w-full sm:w-auto btn-secondary px-6"
                        >
                            {{ __('Enregistrer mes choix') }}
                        </button>
                    </div>

                    {{-- Links --}}
                    <div class="mt-4 text-center">
                        <a href="{{ route('privacy') }}" class="text-xs text-sand-500 hover:text-primary-600 hover:underline">
                            {{ __('Politique de confidentialité') }}
                        </a>
                        <span class="text-sand-300 mx-2">|</span>
                        <a href="{{ route('pages.show', 'mentions-legales') }}" class="text-xs text-sand-500 hover:text-primary-600 hover:underline">
                            {{ __('Mentions légales') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
