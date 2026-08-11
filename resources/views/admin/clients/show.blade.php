@extends('admin.layout')

@section('title', 'Client #' . $client->id)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">{{ $client->name }}</h2>
        <p class="text-sm text-gray-500">Client #{{ $client->id }} — inscrit le {{ $client->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-indigo-500 mr-2"></i>Informations
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Nom</label>
                    <div class="text-sm font-semibold text-gray-900">{{ $client->name }}</div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                    <div class="text-sm">
                        <a href="mailto:{{ $client->email }}" class="text-indigo-600 hover:underline">{{ $client->email }}</a>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Téléphone</label>
                    <div class="text-sm text-gray-900">
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" class="text-indigo-600 hover:underline">{{ $client->phone }}</a>
                        @else
                            <span class="text-gray-400">Non renseigné</span>
                        @endif
                    </div>
                </div>
                @if($client->address || $client->city || $client->country)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Adresse</label>
                        <div class="text-sm text-gray-900">
                            @if($client->address){{ $client->address }}<br>@endif
                            @if($client->postal_code || $client->city){{ $client->postal_code }} {{ $client->city }}<br>@endif
                            @if($client->country){{ $client->country }}@endif
                        </div>
                    </div>
                @endif
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Type de compte</label>
                    <div class="mt-1">
                        @if($client->google_id)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fab fa-google mr-1"></i> Google
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                Email / mot de passe
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Email vérifié</label>
                    <div class="text-sm text-gray-900">
                        {{ $client->email_verified_at ? $client->email_verified_at->format('d/m/Y H:i') : 'Non' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Activité
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-indigo-600">{{ $client->bookings_count }}</div>
                    <div class="text-xs text-gray-500 uppercase mt-1">Réservations</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-indigo-600">{{ $client->reviews_count }}</div>
                    <div class="text-xs text-gray-500 uppercase mt-1">Avis</div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-check text-indigo-500 mr-2"></i>Réservations
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[480px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Réf.</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tour</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">#{{ $booking->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $booking->tour?->title ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    @php
                                        $amount = (float) $booking->total_amount;
                                        $fmt = \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert($amount));
                                    @endphp
                                    {{ $fmt }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'canceled' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucune réservation pour ce client.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
