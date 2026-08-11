<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        
        /* intl-tel-input custom styles */
        .iti {
            width: 100%;
        }
        .iti__flag-container {
            z-index: 10;
        }
        .iti__selected-flag {
            padding: 0 12px 0 16px;
            border-right: 1px solid #d1d5db;
        }
        .iti__selected-flag:hover {
            background-color: #f9fafb;
        }
        .iti__country-list {
            z-index: 50;
            max-height: 200px;
            overflow-y: auto;
        }
        #guest_phone {
            padding-left: 60px;
        }
    </style>
    @endpush

    @php
        $currencySymbol = \App\Helpers\CurrencyHelper::current()?->symbol ?? '€';
    @endphp

    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ __('Complete your order') }}
                </h1>
                <p class="text-gray-600">{{ count($cartItems) }} {{ __('tour(s) in your cart') }}</p>
            </div>

            <form action="{{ route('cart.checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Block 1: Client Information -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                        <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user text-primary mr-3 text-2xl"></i>
                            {{ __('Customer information') }}
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> {{ __('Full name') }}
                                </label>
                                <input type="text" 
                                       name="guest_name" 
                                       value="{{ auth('client')->user()?->name ?? old('guest_name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                                @error('guest_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> {{ __('Email') }}
                                </label>
                                <input type="email" 
                                       name="guest_email" 
                                       value="{{ auth('client')->user()?->email ?? old('guest_email') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                                @error('guest_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> {{ __('Phone') }}
                                </label>
                                <input type="tel" 
                                       id="guest_phone"
                                       name="guest_phone" 
                                       value="{{ auth('client')->user()?->phone ?? old('guest_phone') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                                <input type="hidden" name="guest_phone_country_code" id="guest_phone_country_code" value="">
                                @error('guest_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Block 2: Récapitulatif -->
                    <div class="space-y-6">
                        <!-- Récapitulatif des tours -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                            <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-list text-primary mr-3 text-2xl"></i>
                                {{ __('Summary') }}
                            </h2>

                            <div class="space-y-6">
                                @foreach($cartItems as $itemId => $item)
                                    @php
                                        $tour = $item['tour'];
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-start gap-4 mb-4">
                                            @php
                                                $tourImage = $tour->primaryImage ?? $tour->images->first();
                                                $imageUrl = $tourImage ? Storage::url($tourImage->path) : null;
                                            @endphp
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $tour->name }}" 
                                                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 mb-2">{{ $tour->name }}</h3>
                                                <div class="space-y-1 text-sm text-gray-600">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar-alt mr-2 text-primary w-4"></i>
                                                        <span>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                                                        @if(isset($item['departure_time']))
                                                            <span class="ml-2 font-semibold">• {{ $item['departure_time'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-users mr-2 text-primary w-4"></i>
                                                        <span>
                                                            {{ $item['adults'] }} {{ __('adult(s)') }}
                                                            @if($item['children'] > 0), {{ $item['children'] }} {{ __('child(ren)') }}@endif
                                                            @if($item['infants'] > 0), {{ $item['infants'] }} {{ __('baby(ies)') }}@endif
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-tag mr-2 text-primary w-4"></i>
                                                        <span>
                                                            @if(isset($item['pricing_title']))
                                                                {{ $item['pricing_title'] }}
                                                            @else
                                                                {{ $item['pricing_mode'] === 'group' ? __('Group Rate') : __('Private Rate') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-gray-900 text-lg">
                                                    {{ $currencySymbol }}{{ number_format($item['total_price'], 2, ',', ' ') }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Détail du prix -->
                                        @if(isset($item['price_data']['base_breakdown']))
                                            <div class="mt-4 pt-4 border-t border-gray-300">
                                                <div class="text-xs font-semibold text-gray-700 mb-2">{{ __('Price detail:') }}</div>
                                                <div class="space-y-1.5 text-xs text-gray-600">
                                                    @if($item['pricing_mode'] === 'group')
                                                        @if(isset($item['price_data']['base_breakdown']['adults']) && $item['price_data']['base_breakdown']['adults']['quantity'] > 0)
                                                            <div class="flex justify-between">
                                                                <span>{{ $item['price_data']['base_breakdown']['adults']['quantity'] }} {{ __('Adult(s)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['adults']['unit_price'], 2, ',', ' ') }}</span>
                                                                <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['adults']['total'], 2, ',', ' ') }}</span>
                                                            </div>
                                                        @endif
                                                        @if(isset($item['price_data']['base_breakdown']['children']) && $item['price_data']['base_breakdown']['children']['quantity'] > 0)
                                                            <div class="flex justify-between">
                                                                <span>{{ $item['price_data']['base_breakdown']['children']['quantity'] }} {{ __('Child(ren)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['children']['unit_price'], 2, ',', ' ') }}</span>
                                                                <span class="font-semibold text-green-700">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['children']['total'], 2, ',', ' ') }}</span>
                                                            </div>
                                                        @endif
                                                        @if(isset($item['price_data']['base_breakdown']['infants']) && $item['price_data']['base_breakdown']['infants']['quantity'] > 0)
                                                            <div class="flex justify-between">
                                                                <span>{{ $item['price_data']['base_breakdown']['infants']['quantity'] }} {{ __('Baby(ies)') }}</span>
                                                                <span class="font-semibold text-blue-700">
                                                                    @if($item['price_data']['base_breakdown']['infants']['unit_price'] > 0)
                                                                        {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['infants']['total'], 2, ',', ' ') }}
                                                                    @else
                                                                        {{ __('FREE') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="flex justify-between">
                                                            <span>{{ $item['price_data']['base_breakdown']['people'] }} {{ __('person(s)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['unit_price'], 2, ',', ' ') }}</span>
                                                            <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['total'], 2, ',', ' ') }}</span>
                                                        </div>
                                                    @endif
                                                    @if(isset($item['price_data']['addons_total']) && $item['price_data']['addons_total'] > 0)
                                                        <div class="flex justify-between pt-1 border-t border-gray-200">
                                                            <span>{{ __('Additional options') }}</span>
                                                            <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['addons_total'], 2, ',', ' ') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Options supplémentaires -->
                                        @if(isset($item['price_data']['addons']) && count($item['price_data']['addons']) > 0)
                                            <div class="mt-4 pt-4 border-t border-gray-300">
                                                <div class="text-xs font-semibold text-gray-700 mb-2">{{ __('Additional options:') }}</div>
                                                <div class="space-y-1">
                                                    @foreach($item['price_data']['addons'] as $addon)
                                                        <div class="text-xs text-gray-600 flex justify-between">
                                                            <span>• {{ $addon['addon_name'] }}</span>
                                                            @if($addon['total_price'] > 0)
                                                                <span class="font-semibold">{{ $currencySymbol }}{{ number_format($addon['total_price'], 2, ',', ' ') }}</span>
                                                            @else
                                                                <span class="text-green-600 font-semibold">{{ __('Included') }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-6 border-t-2 border-gray-300">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-poppins font-bold text-gray-900">{{ __('Total') }}</span>
                                    <span class="text-2xl font-poppins font-bold text-primary">
                                        {{ $currencySymbol }}{{ number_format($totalAmount, 2, ',', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <button type="submit" 
                                class="w-full px-6 py-4 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg text-center text-lg"
                                style="background-color: {{ primary_color() }};">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ __('Confirm order') }}
                        </button>

                        <a href="{{ route('cart.index') }}" 
                           class="block text-center text-gray-600 hover:text-primary transition-colors text-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            {{ __('Back to cart') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('guest_phone');
            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "fr", // Pays par défaut (France)
                    preferredCountries: ["fr", "ma", "dz", "tn", "be", "ch", "ca", "us", "gb"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js",
                    nationalMode: false,
                    formatOnDisplay: true,
                });

                // Mettre à jour le champ hidden avec le code pays
                phoneInput.addEventListener('countrychange', function() {
                    const countryData = iti.getSelectedCountryData();
                    const countryCodeInput = document.getElementById('guest_phone_country_code');
                    if (countryCodeInput) {
                        countryCodeInput.value = '+' + countryData.dialCode;
                    }
                });

                // Initialiser le code pays au chargement
                const countryData = iti.getSelectedCountryData();
                const countryCodeInput = document.getElementById('guest_phone_country_code');
                if (countryCodeInput) {
                    countryCodeInput.value = '+' + countryData.dialCode;
                }

                // Si le numéro existe déjà, formater avec le code pays
                if (phoneInput.value) {
                    const currentValue = phoneInput.value;
                    // Si le numéro commence déjà par +, le formater correctement
                    if (currentValue.startsWith('+')) {
                        iti.setNumber(currentValue);
                    } else {
                        // Sinon, utiliser le code pays par défaut
                        const countryData = iti.getSelectedCountryData();
                        phoneInput.value = '+' + countryData.dialCode + ' ' + currentValue;
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

