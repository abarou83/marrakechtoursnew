@extends('admin.layout')

@section('title', 'Réservations')

@section('content')
{{-- Header --}}
<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Réservations</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez toutes les réservations clients</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.bookings.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle réservation
        </a>
        <a href="{{ route('admin.bookings.export', request()->query()) }}" class="btn-outline">
            <i class="fas fa-download mr-2"></i>
            Exporter CSV
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 shadow-sm border">
        <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm border border-yellow-200">
        <p class="text-xs font-medium text-yellow-600 uppercase">En attente</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm border border-green-200">
        <p class="text-xs font-medium text-green-600 uppercase">Confirmées</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['confirmed']) }}</p>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm border">
        <p class="text-xs font-medium text-gray-500 uppercase">CA Aujourd'hui</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_revenue'], 0) }}€</p>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm border">
        <p class="text-xs font-medium text-gray-500 uppercase">CA Mois</p>
        <p class="text-2xl font-bold text-primary-600">{{ number_format($stats['month_revenue'], 0) }}€</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow-sm border mb-6 p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label class="label">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Réf, nom, email..." class="input">
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="status" class="input">
                <option value="">Tous</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Paiement</label>
            <select name="payment_status" class="input">
                <option value="">Tous</option>
                @foreach($paymentStatuses as $status)
                    <option value="{{ $status->value }}" @selected(request('payment_status') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Tour</label>
            <select name="tour_id" class="input">
                <option value="">Tous les tours</option>
                @foreach($tours as $tour)
                    <option value="{{ $tour->id }}" @selected(request('tour_id') == $tour->id)>
                        {{ Str::limit($tour->translate()?->title ?? $tour->title, 30) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Canal</label>
            <select name="channel" class="input">
                <option value="">Tous</option>
                @foreach($channels as $value => $label)
                    <option value="{{ $value }}" @selected(request('channel') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Date du</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
        </div>
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="label">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
            </div>
            <button type="submit" class="btn-primary px-4">
                <i class="fas fa-search"></i>
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="btn-outline px-4">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden lg:table-cell">Tour</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">Pax</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Montant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">Paiement</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="font-mono font-semibold text-primary-600 hover:underline">
                                {{ $booking->reference }}
                            </a>
                            <div class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 truncate max-w-[150px]">
                                {{ $booking->customer_name }}
                            </div>
                            <div class="text-xs text-gray-500 truncate max-w-[150px]">
                                {{ $booking->customer_email }}
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="text-sm text-gray-900 truncate max-w-[200px]">
                                {{ $booking->tour->translate()?->title ?? $booking->tour->title }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $booking->pricing_mode === 'private' ? 'Privé' : 'Groupe' }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $travelDate = $booking->travel_date ?? $booking->booking_date;
                            @endphp
                            @if($travelDate)
                                <div class="text-sm font-medium">{{ \Carbon\Carbon::parse($travelDate)->format('d/m/Y') }}</div>
                                @if(\Carbon\Carbon::parse($travelDate)->isToday())
                                    <span class="text-xs text-red-600 font-semibold">Aujourd'hui!</span>
                                @elseif(\Carbon\Carbon::parse($travelDate)->isTomorrow())
                                    <span class="text-xs text-orange-600 font-semibold">Demain</span>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-600">
                            {{ $booking->adults ?? 1 }}A
                            @if(($booking->children ?? 0) > 0)
                                +{{ $booking->children }}E
                            @endif
                            @if(($booking->infants ?? 0) > 0)
                                +{{ $booking->infants }}B
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">
                                {{ number_format($booking->total_ttc ?? $booking->total_price ?? 0, 2) }}€
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusValue = $booking->status->value ?? $booking->status ?? 'pending';
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($statusValue) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @php
                                $paymentValue = $booking->payment_status->value ?? $booking->payment_status ?? 'pending';
                                $paymentColors = [
                                    'pending' => 'text-yellow-600',
                                    'paid' => 'text-green-600',
                                    'failed' => 'text-red-600',
                                    'refunded' => 'text-purple-600',
                                ];
                            @endphp
                            <span class="text-xs font-medium {{ $paymentColors[$paymentValue] ?? 'text-gray-500' }}">
                                {{ ucfirst($paymentValue) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                   class="text-gray-400 hover:text-primary-600" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" 
                                   class="text-gray-400 hover:text-blue-600" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($statusValue === 'pending')
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="text-gray-400 hover:text-green-600" title="Confirmer">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                            Aucune réservation trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="px-4 py-4 bg-gray-50 border-t">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
