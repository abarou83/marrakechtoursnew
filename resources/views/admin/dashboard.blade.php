@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- Total Tours -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-500 text-xs md:text-sm font-medium uppercase truncate">Tours</p>
                    <p class="text-2xl md:text-4xl font-bold text-gray-900 mt-1 md:mt-2">{{ $totalTours }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-2 md:p-4 flex-shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-500 text-xs md:text-sm font-medium uppercase truncate">Réservations</p>
                    <p class="text-2xl md:text-4xl font-bold text-gray-900 mt-1 md:mt-2">{{ $totalBookings }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-2 md:p-4 flex-shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-500 text-xs md:text-sm font-medium uppercase truncate">Utilisateurs</p>
                    <p class="text-2xl md:text-4xl font-bold text-gray-900 mt-1 md:mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2 md:p-4 flex-shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-500 text-xs md:text-sm font-medium uppercase truncate">Revenu Total</p>
                    <p class="text-2xl md:text-4xl font-bold text-gray-900 mt-1 md:mt-2">{{ number_format($totalRevenue, 0) }}€</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-2 md:p-4 flex-shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- Pending Bookings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3 mb-3 md:mb-4">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 flex-1 min-w-0 truncate">Réservations en attente</h3>
                <span class="bg-yellow-100 text-yellow-800 text-base md:text-lg font-bold px-3 md:px-4 py-1.5 md:py-2 rounded-full flex-shrink-0">
                    {{ $pendingBookings }}
                </span>
            </div>
            <p class="text-sm md:text-base text-gray-600">Nécessitent votre attention</p>
        </div>

        <!-- Confirmed Bookings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex items-center justify-between gap-3 mb-3 md:mb-4">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 flex-1 min-w-0 truncate">Réservations confirmées</h3>
                <span class="bg-green-100 text-green-800 text-base md:text-lg font-bold px-3 md:px-4 py-1.5 md:py-2 rounded-full flex-shrink-0">
                    {{ $confirmedBookings }}
                </span>
            </div>
            <p class="text-sm md:text-base text-gray-600">Paiements validés</p>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 border-b border-gray-200 px-4 md:px-6 py-4 md:py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="bg-gray-200 rounded-lg p-1.5 md:p-2">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-gray-900">Réservations récentes</h2>
                        <p class="text-xs md:text-sm text-gray-500 mt-0.5">Dernières réservations enregistrées</p>
                    </div>
                </div>
                @if($recentBookings->count() > 0)
                    <span class="bg-gray-200 text-gray-700 px-3 md:px-4 py-1 md:py-1.5 rounded-full text-xs md:text-sm font-semibold">
                        {{ $recentBookings->count() }} réservation{{ $recentBookings->count() > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Réf.</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Client</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">Tour</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell">Date</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Montant</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <span class="text-xs md:text-sm font-mono font-semibold text-gray-700">#{{ $booking->id }}</span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <div class="flex items-center space-x-2 md:space-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-700 text-xs md:text-sm font-bold">{{ strtoupper(substr($booking->guest_name ?? $booking->user?->name ?? 'I', 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs md:text-sm font-semibold text-gray-900 truncate">{{ $booking->guest_name ?? $booking->user?->name ?? 'Invité' }}</div>
                                        <div class="text-xs text-gray-500 truncate hidden sm:block">{{ $booking->guest_email ?? $booking->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 hidden sm:table-cell">
                                <div class="text-xs md:text-sm font-semibold text-gray-900 truncate max-w-xs">{{ $booking->tour?->title }}</div>
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $booking->tourDate?->start_at?->format('d/m/Y') ?? 'Date libre' }}
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="text-xs md:text-sm text-gray-900 font-medium">
                                    {{ $booking->preferred_date ? \Carbon\Carbon::parse($booking->preferred_date)->format('d/m/Y') : '—' }}
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm font-bold text-gray-900">
                                    @php
                                        $amount = (float)$booking->total_amount;
                                        $fmt = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($amount));
                                    @endphp
                                    {{ $fmt }}
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
                                        'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle'],
                                        'canceled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                                    ];
                                    $status = $statusConfig[$booking->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-circle'];
                                @endphp
                                <span class="inline-flex items-center px-2 md:px-3 py-1 md:py-1.5 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                                    <i class="fas {{ $status['icon'] }} mr-1 md:mr-1.5"></i>
                                    <span class="hidden sm:inline">{{ ucfirst($booking->status) }}</span>
                                    <span class="sm:hidden">{{ substr(ucfirst($booking->status), 0, 1) }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 md:px-6 py-12 md:py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Aucune réservation trouvée</p>
                                    <p class="text-sm text-gray-400 mt-1">Les nouvelles réservations apparaîtront ici</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Footer -->
        @if($recentBookings->count() > 0)
            <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 font-semibold transition-colors duration-150 text-sm md:text-base">
                    <span>Voir toutes les réservations</span>
                    <svg class="w-4 h-4 md:w-5 md:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
@endsection
