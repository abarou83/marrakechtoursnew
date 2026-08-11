@extends('admin.layout')

@section('title', 'Saisie OTA')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.channels.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">← Retour aux canaux</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Enregistrer une réservation OTA</h1>

        <form method="POST" action="{{ route('admin.channels.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-bold mb-1">Canal *</label>
                <select name="channel" class="form-input w-full" required>
                    @foreach($channels as $value => $label)
                        <option value="{{ $value }}" @selected(old('channel') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Réf. externe (Viator/GYG)</label>
                    <input type="text" name="channel_external_id" value="{{ old('channel_external_id') }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Statut paiement *</label>
                    <select name="payment_status" class="form-input w-full" required>
                        <option value="paid" @selected(old('payment_status') === 'paid')>Payé</option>
                        <option value="pending" @selected(old('payment_status') === 'pending')>En attente</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Tour *</label>
                <select name="tour_id" class="form-input w-full" required>
                    <option value="">— Choisir —</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}" @selected(old('tour_id') == $tour->id)>
                            {{ $tour->translate()?->title ?? $tour->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Date du tour *</label>
                    <input type="date" name="travel_date" value="{{ old('travel_date') }}" class="form-input w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Montant total (€) *</label>
                    <input type="number" step="0.01" min="0" name="total_price" value="{{ old('total_price') }}" class="form-input w-full" required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Adultes *</label>
                    <input type="number" name="adults" min="1" value="{{ old('adults', 1) }}" class="form-input w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Enfants</label>
                    <input type="number" name="children" min="0" value="{{ old('children', 0) }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Bébés</label>
                    <input type="number" name="infants" min="0" value="{{ old('infants', 0) }}" class="form-input w-full">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Nom client *</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-input w-full" required>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Email *</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-input w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Téléphone</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input w-full">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Notes canal</label>
                <textarea name="channel_notes" rows="3" class="form-input w-full">{{ old('channel_notes') }}</textarea>
            </div>

            <button type="submit" class="btn-primary w-full">Enregistrer la réservation</button>
        </form>
    </div>
</div>
@endsection
