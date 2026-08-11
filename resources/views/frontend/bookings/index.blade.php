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

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Icon -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: {{ primary_color() }}20;">
                    <i class="fas fa-calendar-check text-3xl" style="color: {{ primary_color() }};"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Mes Réservations</h1>
                <p class="text-gray-600">Suivez l'état de toutes vos réservations en un coup d'œil</p>
            </div>

            @forelse($bookings as $booking)
                @php
                    $tourImage = $booking->tour->primaryImage ?? $booking->tour->images->first();
                    $imageUrl = $tourImage ? Storage::url($tourImage->path) : null;
                    $totalPrice = $booking->total_price ?? $booking->total_amount ?? 0;
                    $formattedPrice = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert((float)$totalPrice));
                    $dateDisplay = $booking->tourDate ? $booking->tourDate->start_at->format('d/m/Y H:i') : ($booking->preferred_date ? \Carbon\Carbon::parse($booking->preferred_date)->format('d/m/Y') : 'N/A');
                @endphp

                <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
                    <div class="flex">
                        <!-- Image -->
                        @if($imageUrl)
                            <div class="w-32 md:w-40 flex-shrink-0 relative">
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $booking->tour->title }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute top-2 left-2">
                                    @if($booking->status === 'pending')
                                        <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">EN ATTENTE</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">CONFIRMÉ</span>
                                    @elseif($booking->status === 'canceled')
                                        <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">ANNULÉ</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="flex-1 p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $booking->tour->title }}</h3>
                                    <p class="text-sm text-gray-500">Référence: #{{ $booking->id }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold" style="color: {{ primary_color() }};">{{ $formattedPrice }}</div>
                                </div>
                            </div>

                            <!-- Info Cards -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Date</div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $dateDisplay }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Participants</div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $booking->adults }}
                                        @if($booking->children > 0 || $booking->infants > 0)
                                            +{{ $booking->children + $booking->infants }}
                                        @endif
                                    </div>
                                </div>
                                @if($booking->tour->location)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs text-gray-500 mb-1">Lieu</div>
                                        <div class="text-sm font-semibold text-gray-900">{{ Str::limit($booking->tour->location, 15) }}</div>
                                    </div>
                                @endif
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Paiement</div>
                                    <div class="text-sm font-semibold">
                                        @if($booking->payment)
                                            @if($booking->payment->status === 'paid')
                                                <span class="text-green-600">Payé</span>
                                            @else
                                                <span class="text-yellow-600">En attente</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Détails:</span> 
                                    {{ $booking->adults }} adulte(s)
                                    @if($booking->children > 0), {{ $booking->children }} enfant(s)@endif
                                    @if($booking->infants > 0), {{ $booking->infants }} bébé(s)@endif
                                </div>
                                <a href="{{ route('tours.show', $booking->tour->url_key) }}" 
                                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:scale-105"
                                   style="background-color: {{ primary_color() }}; color: white;">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-md p-16 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full flex items-center justify-center" style="background-color: {{ primary_color() }}10;">
                            <i class="fas fa-calendar-times text-5xl" style="color: {{ primary_color() }};"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Aucune réservation trouvée</h3>
                        <p class="text-gray-600 mb-8">Commencez votre aventure en réservant votre premier tour !</p>
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg transform hover:scale-105"
                           style="background-color: {{ primary_color() }};">
                            <i class="fas fa-compass mr-2"></i>
                            Découvrir les tours
                        </a>
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="mt-10 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        {{ $bookings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
