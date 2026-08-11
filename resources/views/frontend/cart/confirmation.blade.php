<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
    @endpush

    @php
        $currencySymbol = \App\Helpers\CurrencyHelper::current()?->symbol ?? '€';
    @endphp

    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header avec icône de succès -->
            <div class="text-center mb-8">
                <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-green-600 text-5xl"></i>
                </div>
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ __('Order confirmed!') }}
                </h1>
                <p class="text-gray-600 text-lg">
                    {{ __('Thank you for your booking. A confirmation email has been sent to') }} <strong>{{ $guest_email }}</strong>
                </p>
            </div>

            <!-- Informations client -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8 mb-6">
                <h2 class="font-poppins text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-primary mr-3"></i>
                    {{ __('Customer information') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500 mb-1">{{ __('Name') }}</div>
                        <div class="font-semibold text-gray-900">{{ $guest_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">{{ __('Email') }}</div>
                        <div class="font-semibold text-gray-900">{{ $guest_email }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">{{ __('Phone') }}</div>
                        <div class="font-semibold text-gray-900">{{ $guest_phone }}</div>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif des réservations -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8 mb-6">
                <h2 class="font-poppins text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-list text-primary mr-3"></i>
                    {{ __('Your bookings summary') }}
                </h2>

                <div class="space-y-6">
                    @foreach($bookings as $booking)
                        @php
                            $tour = $booking->tour;
                            $tourImage = $tour->primaryImage ?? $tour->images->first();
                            $imageUrl = $tourImage ? Storage::url($tourImage->path) : null;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-start gap-4 mb-4">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $tour->name }}" 
                                         class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-2 text-lg">{{ $tour->name }}</h3>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2 text-primary w-4"></i>
                                            <span>{{ \Carbon\Carbon::parse($booking->preferred_date)->format('d/m/Y') }}</span>
                                            @if($booking->tourDate)
                                                <span class="ml-2 font-semibold">• {{ $booking->tourDate->start_at->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-primary w-4"></i>
                                            <span>
                                                {{ $booking->adults }} {{ __('adult(s)') }}
                                                @if($booking->children > 0), {{ $booking->children }} {{ __('child(ren)') }}@endif
                                                @if($booking->infants > 0), {{ $booking->infants }} {{ __('baby(ies)') }}@endif
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-tag mr-2 text-primary w-4"></i>
                                            <span class="capitalize">{{ $booking->pricing_mode === 'group' ? __('Group Rate') : __('Private Rate') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-hashtag mr-2 text-primary w-4"></i>
                                            <span>{{ __('Reference:') }} <strong>#{{ $booking->id }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900 text-lg">
                                        {{ $currencySymbol }}{{ number_format($booking->total_price, 2, ',', ' ') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($booking->status === 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">{{ __('Pending') }}</span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">{{ __('Confirmed') }}</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Détail du prix -->
                            <div class="mt-4 pt-4 border-t border-gray-300">
                                <div class="text-xs font-semibold text-gray-700 mb-2">{{ __('Price detail:') }}</div>
                                <div class="space-y-1.5 text-xs text-gray-600">
                                    @php
                                        // Calculer les prix unitaires approximatifs à partir du base_price
                                        $totalPeople = $booking->adults + $booking->children + ($booking->infants > 0 ? 1 : 0);
                                        if ($booking->pricing_mode === 'group' && $totalPeople > 0) {
                                            // Pour le mode groupe, estimer les prix unitaires
                                            // On utilise une approximation basée sur le base_price total
                                            $estimatedAdultPrice = $totalPeople > 0 ? ($booking->base_price / $totalPeople) : 0;
                                            $estimatedChildPrice = $estimatedAdultPrice * 0.7; // Approximation : enfant = 70% du prix adulte
                                        }
                                    @endphp
                                    
                                    @if($booking->pricing_mode === 'group')
                                        @php
                                            // Pour le mode groupe, afficher simplement le base_price réparti
                                            // On ne peut pas calculer précisément sans les prix unitaires, donc on affiche le total
                                        @endphp
                                        <div class="flex justify-between">
                                            <span>
                                                {{ $booking->adults }} {{ __('adult(s)') }}
                                                @if($booking->children > 0), {{ $booking->children }} {{ __('child(ren)') }}@endif
                                                @if($booking->infants > 0), {{ $booking->infants }} {{ __('baby(ies)') }}@endif
                                            </span>
                                            <span class="font-semibold">{{ $currencySymbol }}{{ number_format($booking->base_price, 2, ',', ' ') }}</span>
                                        </div>
                                    @else
                                        <div class="flex justify-between">
                                            <span>{{ $booking->adults }} {{ __('person(s)') }}</span>
                                            <span class="font-semibold">{{ $currencySymbol }}{{ number_format($booking->base_price, 2, ',', ' ') }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($booking->bookingAddons->count() > 0)
                                        <div class="pt-2 border-t border-gray-200 mt-2">
                                            <div class="text-xs font-semibold text-gray-700 mb-1">{{ __('Additional options:') }}</div>
                                            @foreach($booking->bookingAddons as $bookingAddon)
                                                <div class="flex justify-between text-xs">
                                                    <span>• {{ translate_model($bookingAddon->addon, 'name') }}</span>
                                                    @if($bookingAddon->total_price > 0)
                                                        <span class="font-semibold">{{ $currencySymbol }}{{ number_format($bookingAddon->total_price, 2, ',', ' ') }}</span>
                                                    @else
                                                        <span class="text-green-600 font-semibold">Inclus</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            <div class="flex justify-between pt-1 border-t border-gray-100 mt-1">
                                                <span>{{ __('Total additional options') }}</span>
                                                <span class="font-semibold">{{ $currencySymbol }}{{ number_format($booking->addons_total, 2, ',', ' ') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between pt-2 border-t-2 border-gray-300 mt-2">
                                        <span class="font-bold text-gray-900">{{ __('Total') }}</span>
                                        <span class="font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($booking->total_price, 2, ',', ' ') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total général -->
                <div class="mt-6 pt-6 border-t-2 border-gray-300">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-poppins font-bold text-gray-900">{{ __('Grand total') }}</span>
                        <span class="text-3xl font-poppins font-bold text-primary">
                            {{ $currencySymbol }}{{ number_format($total_amount, 2, ',', ' ') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" 
                   class="px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg text-center"
                   style="background-color: {{ primary_color() }};">
                    <i class="fas fa-list mr-2"></i>
                    {{ __('View my bookings') }}
                </a>
                <a href="{{ route('tours.index') }}" 
                   class="px-6 py-3 rounded-lg font-semibold text-gray-700 bg-white border-2 transition-all duration-300 hover:shadow-lg text-center"
                   style="border-color: {{ primary_color() }};">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('Back to tours') }}
                </a>
            </div>

            <!-- Message d'information -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                    <div>
                        <strong>{{ __('Important:') }}</strong> {{ __('You will receive a confirmation email with all the details of your booking. If you have an account, you can also check your bookings in your personal space.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

