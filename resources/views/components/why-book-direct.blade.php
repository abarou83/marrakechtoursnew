@props([])

<section {{ $attributes->merge(['class' => 'py-16 bg-sand-100']) }}>
    <div class="container-app">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h2 class="section-title mb-4">{{ __('Pourquoi réserver en direct ?') }}</h2>
            <p class="section-subtitle mx-auto">
                {{ __('Nous sommes une agence locale à Marrakech, pas un intermédiaire.') }}
            </p>
        </div>

        {{-- Comparison table --}}
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-base shadow-card overflow-hidden">
                {{-- Header row --}}
                <div class="grid grid-cols-3 bg-sand-50 border-b border-sand-200">
                    <div class="p-4"></div>
                    <div class="p-4 text-center border-x border-sand-200">
                        <span class="text-sm font-medium text-sand-500">{{ __('OTA') }}</span>
                        <p class="text-xs text-sand-400">(Viator, GYG...)</p>
                    </div>
                    <div class="p-4 text-center bg-primary-50">
                        <span class="text-sm font-bold text-primary-600">marrakechtours</span>
                        <p class="text-xs text-primary-500">{{ __('Réservation directe') }}</p>
                    </div>
                </div>

                {{-- Comparison rows --}}
                @php
                    $comparisons = [
                        [
                            'label' => __('Type d\'entreprise'),
                            'ota' => __('Intermédiaire international'),
                            'direct' => __('Agence locale à Marrakech'),
                            'highlight' => true,
                        ],
                        [
                            'label' => __('Prix'),
                            'ota' => __('Commission de 20-30%'),
                            'direct' => __('Prix direct sans intermédiaire'),
                            'highlight' => true,
                        ],
                        [
                            'label' => __('Support client'),
                            'ota' => __('Ticket support international'),
                            'direct' => __('WhatsApp direct avec notre équipe'),
                            'highlight' => true,
                        ],
                        [
                            'label' => __('Personnalisation'),
                            'ota' => __('Tours standardisés'),
                            'direct' => __('Itinéraire modifiable sur demande'),
                            'highlight' => true,
                        ],
                        [
                            'label' => __('Annulation'),
                            'ota' => __('24h avant'),
                            'direct' => __('Gratuite jusqu\'à 24h avant'),
                            'highlight' => false,
                        ],
                        [
                            'label' => __('Connaissance locale'),
                            'ota' => __('Descriptions génériques'),
                            'direct' => __('Conseils d\'experts locaux'),
                            'highlight' => true,
                        ],
                    ];
                @endphp

                @foreach($comparisons as $item)
                    <div class="grid grid-cols-3 border-b border-sand-100 last:border-b-0 hover:bg-sand-50/50 transition-colors">
                        <div class="p-4 flex items-center">
                            <span class="font-medium text-sand-700">{{ $item['label'] }}</span>
                        </div>
                        <div class="p-4 text-center border-x border-sand-100 flex items-center justify-center">
                            <span class="text-sm text-sand-500">{{ $item['ota'] }}</span>
                        </div>
                        <div class="p-4 text-center {{ $item['highlight'] ? 'bg-primary-50/50' : '' }} flex items-center justify-center gap-2">
                            @if($item['highlight'])
                                <x-heroicon-s-check-circle class="w-5 h-5 text-success-500 flex-shrink-0" />
                            @endif
                            <span class="text-sm font-medium {{ $item['highlight'] ? 'text-primary-700' : 'text-sand-700' }}">
                                {{ $item['direct'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Trust badges --}}
        <div class="mt-12 flex flex-wrap justify-center gap-8">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-accent-100 flex items-center justify-center">
                    <x-heroicon-o-map-pin class="w-6 h-6 text-accent-600" />
                </div>
                <div>
                    <p class="font-semibold text-sand-900">{{ __('Basés à Marrakech') }}</p>
                    <p class="text-sm text-sand-500">{{ __('Équipe locale depuis 2010') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-accent-100 flex items-center justify-center">
                    <x-heroicon-o-star class="w-6 h-6 text-accent-600" />
                </div>
                <div>
                    <p class="font-semibold text-sand-900">4.9/5</p>
                    <p class="text-sm text-sand-500">{{ __('2 300+ avis vérifiés') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-accent-100 flex items-center justify-center">
                    <x-heroicon-o-users class="w-6 h-6 text-accent-600" />
                </div>
                <div>
                    <p class="font-semibold text-sand-900">15 000+</p>
                    <p class="text-sm text-sand-500">{{ __('Voyageurs accompagnés') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
