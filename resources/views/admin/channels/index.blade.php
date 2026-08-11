@extends('admin.layout')

@section('title', 'Canaux de vente')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Canaux de vente (OTA)</h1>
            <p class="text-gray-600 text-sm mt-1">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.channels.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Saisie manuelle OTA
            </a>
            <a href="{{ route('admin.marketing.index') }}" class="btn-outline">Dashboard marketing</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex items-center gap-2 bg-white p-4 rounded-xl shadow">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-input text-sm">
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-input text-sm">
        <button type="submit" class="btn-primary text-sm">Filtrer</button>
    </form>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">CA par canal</h2>
        @if($byChannel->isEmpty())
            <p class="text-gray-500 text-sm">Aucune donnée sur la période.</p>
        @else
            <div class="space-y-3">
                @foreach($byChannel as $row)
                    @php
                        $channelLabel = $channels[$row->channel ?? 'direct'] ?? ucfirst($row->channel ?? 'direct');
                    @endphp
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="font-medium">{{ $channelLabel }}</span>
                        <span class="text-sm text-gray-600">{{ $row->count }} résa · {{ number_format((float) $row->revenue, 2) }} €</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-2 items-center justify-between">
            <h2 class="text-lg font-semibold">Réservations OTA / hors site</h2>
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="from" value="{{ $from->format('Y-m-d') }}">
                <input type="hidden" name="to" value="{{ $to->format('Y-m-d') }}">
                <select name="channel" class="form-input text-sm" onchange="this.form.submit()">
                    <option value="">Tous les canaux</option>
                    @foreach($channels as $value => $label)
                        @if(!in_array($value, ['direct', 'gift_card']))
                            <option value="{{ $value }}" @selected(request('channel') === $value)>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </form>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réf.</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Canal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref. externe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tour</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($otaBookings as $booking)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 font-medium">{{ $booking->reference }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $channels[$booking->channel] ?? $booking->channel }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $booking->channel_external_id ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $booking->tour->translate()?->title ?? $booking->tour->title }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ number_format($booking->total_ttc ?? $booking->total_price ?? 0, 2) }} €</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune réservation OTA.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($otaBookings->hasPages())
            <div class="p-4">{{ $otaBookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
