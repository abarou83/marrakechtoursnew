@extends('admin.layout')

@section('title', 'Marketing')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Marketing</h1>
            <p class="text-gray-600 text-sm mt-1">
                {{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}
            </p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-input text-sm">
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-input text-sm">
            <button type="submit" class="btn-primary text-sm">Filtrer</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenue'], 2) }} €</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Réservations payées</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['booking_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Panier moyen</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_order'], 2) }} €</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Newsletter</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['newsletter_subscribers'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">CA par canal</h2>
            @if($stats['by_channel']->isEmpty())
                <p class="text-gray-500 text-sm">Aucune donnée</p>
            @else
                <div class="space-y-3">
                    @foreach($stats['by_channel'] as $row)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium capitalize">{{ $row->channel ?? 'direct' }}</span>
                            <span class="text-sm text-gray-600">{{ $row->count }} résa · {{ number_format($row->revenue, 2) }} €</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Sources UTM</h2>
            @if($stats['by_utm_source']->isEmpty())
                <p class="text-gray-500 text-sm">Aucune donnée UTM</p>
            @else
                <div class="space-y-3">
                    @foreach($stats['by_utm_source'] as $row)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">{{ $row->utm_source }}</span>
                            <span class="text-sm text-gray-600">{{ $row->count }} · {{ number_format($row->revenue, 2) }} €</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Paniers abandonnés</p>
            <p class="text-xl font-bold">{{ $stats['abandoned_carts'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Paniers récupérés</p>
            <p class="text-xl font-bold">{{ $stats['recovered_carts'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Parrainages</p>
            <p class="text-xl font-bold">{{ $stats['referral_count'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">CA par langue</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($stats['by_locale'] as $row)
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">{{ $row->locale }}</p>
                    <p class="font-bold">{{ number_format($row->revenue, 0) }} €</p>
                    <p class="text-xs text-gray-400">{{ $row->count }} résa</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
