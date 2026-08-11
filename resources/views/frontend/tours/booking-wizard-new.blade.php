@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .text-primary { color: {{ primary_color() }}; }
    .bg-primary { background-color: {{ primary_color() }}; }
    .border-primary { border-color: {{ primary_color() }}; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
</style>
@endpush

@php
    $currentCurrency = \App\Helpers\CurrencyHelper::current();
    $currencySymbol = $currentCurrency?->symbol ?: '€';
@endphp

<x-app-layout>
    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Finaliser votre réservation
                </h1>
                <p class="text-gray-600">{{ translate_model($tour, 'title') }}</p>
            </div>

            <form action="{{ route('bookings.store', $tour) }}" method="POST" id="bookingForm">
                @csrf
                
                <!-- Hidden fields from step 1 and 2 -->
                <input type="hidden" name="date" value="{{ request('date') }}">
                <input type="hidden" name="pricing_mode" value="{{ request('pricing_mode') }}">
                <input type="hidden" name="adults" value="{{ request('adults') }}">
                <input type="hidden" name="children" value="{{ request('children', 0) }}">
                <input type="hidden" name="infants" value="{{ request('infants', 0) }}">
                <input type="hidden" name="total_people" value="{{ request('total_people') }}">
                <input type="hidden" name="addons" value="{{ request('addons') }}">
                <input type="hidden" name="tour_date_id" value="{{ request('tour_date_id') }}">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Block 1: Client Information -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                        <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user text-primary mr-3 text-2xl"></i>
                            Informations client
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Nom complet
                                </label>
                                <input type="text" 
                                       name="guest_name" 
                                       value="{{ old('guest_name', auth('client')->user()->name ?? '') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="Jean Dupont" 
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Email
                                </label>
                                <input type="email" 
                                       name="guest_email" 
                                       value="{{ old('guest_email', auth('client')->user()->email ?? '') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="jean@email.com" 
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Téléphone
                                </label>
                                <input type="tel" 
                                       name="guest_phone" 
                                       value="{{ old('guest_phone') }}" 
                                       class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                       placeholder="+33 6 12 34 56 78" 
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Commentaires ou demandes spéciales (optionnel)
                                </label>
                                <textarea name="comments" 
                                          rows="4" 
                                          class="w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary py-3 px-4" 
                                          placeholder="Ex: allergies, besoins spéciaux, questions...">{{ old('comments') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Block 2: Reservation Details -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                        <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-receipt text-primary mr-3 text-2xl"></i>
                            Détail de la réservation
                        </h2>

                        <div class="space-y-4">
                            <!-- Tour Info -->
                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-200">
                                @if($tour->primaryImage)
                                    <img src="{{ Storage::url($tour->primaryImage->path) }}" 
                                         alt="{{ $tour->title }}" 
                                         class="w-20 h-20 rounded-lg object-cover">
                                @endif
                                <div class="flex-1">
                                    <div class="font-bold text-lg text-gray-900">{{ translate_model($tour, 'title') }}</div>
                                    <div class="text-gray-600 text-sm mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ translate_model($tour, 'location') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-semibold text-gray-900">{{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('d/m/Y') : '-' }}</span>
                            </div>

                            <!-- Participants -->
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Participants:</span>
                                <span class="font-semibold text-gray-900">{{ request('total_people', 1) }} personne(s)</span>
                            </div>

                            <!-- Type -->
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Type:</span>
                                <span class="font-semibold text-gray-900 capitalize">{{ request('pricing_mode', 'group') === 'group' ? 'Groupe' : 'Privé' }}</span>
                            </div>

                            <!-- Price Calculation -->
                            <div x-data="bookingSummary({{ $tour->id }})" 
                                 x-init="calculatePrice()"
                                 class="pt-4">
                                <div x-show="loading" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin text-xl" style="color: {{ primary_color() }};"></i>
                                </div>
                                
                                <div x-show="!loading && priceData" class="space-y-2">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Prix de base:</span>
                                        <span class="font-semibold" x-text="priceData ? formatPrice(priceData.base_price) : 'À consulter'"></span>
                                    </div>
                                    
                                    <div x-show="priceData && priceData.addons_total > 0" class="flex justify-between text-gray-700">
                                        <span>Options supplémentaires:</span>
                                        <span class="font-semibold" x-text="priceData ? formatPrice(priceData.addons_total) : ''"></span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center pt-4 mt-4 border-t-2 border-gray-300">
                                        <span class="text-xl font-bold text-gray-900">Total:</span>
                                        <span class="text-2xl font-extrabold" 
                                              style="color: {{ primary_color() }};"
                                              x-text="priceData ? formatPrice(priceData.total_price) : 'À consulter'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full mt-6 px-6 py-4 bg-primary text-white rounded-lg hover:opacity-90 font-bold text-lg shadow-lg hover:shadow-xl transition-all"
                                style="background-color: {{ primary_color() }};">
                            <i class="fas fa-check-circle mr-2"></i>
                            Enregistrer la réservation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingSummary', (tourId) => ({
                tourId: tourId,
                loading: true,
                priceData: null,

                async calculatePrice() {
                    this.loading = true;
                    
                    const data = {
                        tour_id: this.tourId,
                        date: '{{ request('date') }}',
                        pricing_mode: '{{ request('pricing_mode', 'group') }}',
                        adults: {{ request('adults', request('total_people', 1)) }},
                        children: {{ request('children', 0) }},
                        infants: {{ request('infants', 0) }},
                        selected_addons: @json(json_decode(request('addons', '{}'), true)),
                    };

                    try {
                        const response = await fetch('/api/v1/calculate-price', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            this.priceData = result.data;
                        } else {
                            console.error('Price calculation error:', result.message);
                        }
                    } catch (error) {
                        console.error('Error calculating price:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                formatPrice(price) {
                    if (!price) return '';
                    return `{{ $currencySymbol }}${parseFloat(price).toFixed(2)}`;
                }
            }));
        });
    </script>
</x-app-layout>



