@extends('admin.layout')

@section('title', 'Réservation ' . $booking->reference)

@section('content')
<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.bookings.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $booking->reference }}</h1>
                <p class="text-sm text-gray-500">Créée {{ $booking->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-outline">
            <i class="fas fa-edit mr-2"></i>
            Modifier
        </a>
        @if($booking->payment_intent_id && in_array($booking->payment_status->value ?? $booking->payment_status, ['paid']))
            <button onclick="document.getElementById('refundModal').classList.remove('hidden')" 
                    class="btn-danger">
                <i class="fas fa-undo mr-2"></i>
                Rembourser
            </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Tour Info --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-primary-500"></i>
                Détails du tour
            </h2>
            <div class="flex gap-4">
                @if($booking->tour->getFirstMediaUrl('images', 'thumb'))
                    <img src="{{ $booking->tour->getFirstMediaUrl('images', 'thumb') }}" 
                         alt="{{ $booking->tour->title }}"
                         class="w-24 h-24 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h3 class="font-semibold text-lg text-gray-900">
                        {{ $booking->tour->translate()?->title ?? $booking->tour->title }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $booking->pricing?->translate()?->name ?? ($booking->pricing_mode === 'private' ? 'Tour Privé' : 'Tour en Groupe') }}
                    </p>
                    <div class="flex gap-4 mt-3 text-sm">
                        <span class="text-gray-600">
                            <i class="far fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->format('d/m/Y') }}
                        </span>
                        <span class="text-gray-600">
                            <i class="far fa-clock mr-1"></i>
                            {{ $booking->tour->duration_formatted ?? 'Journée complète' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Participants --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-primary-500"></i>
                Participants
            </h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-3xl font-bold text-gray-900">{{ $booking->adults ?? 1 }}</p>
                    <p class="text-sm text-gray-500">Adulte(s)</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-3xl font-bold text-gray-900">{{ $booking->children ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Enfant(s)</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-3xl font-bold text-gray-900">{{ $booking->infants ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Bébé(s)</p>
                </div>
            </div>
        </div>

        {{-- Addons --}}
        @if($booking->addons && $booking->addons->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-primary-500"></i>
                    Options supplémentaires
                </h2>
                <div class="space-y-3">
                    @foreach($booking->addons as $bookingAddon)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-700">
                                {{ $bookingAddon->addon->translate()?->name ?? $bookingAddon->addon->name }}
                                @if($bookingAddon->quantity > 1)
                                    <span class="text-gray-400">x{{ $bookingAddon->quantity }}</span>
                                @endif
                            </span>
                            <span class="font-medium">{{ number_format($bookingAddon->total_price, 2) }}€</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Special Requests --}}
        @if($booking->special_requests)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-comment-alt text-primary-500"></i>
                    Demandes spéciales
                </h2>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $booking->special_requests }}</p>
            </div>
        @endif

        {{-- Price Breakdown --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-receipt text-primary-500"></i>
                Récapitulatif des prix
            </h2>
            <div class="space-y-3">
                @if($booking->price_breakdown)
                    @php $breakdown = is_array($booking->price_breakdown) ? $booking->price_breakdown : json_decode($booking->price_breakdown, true); @endphp
                    @if(isset($breakdown['base_price']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tarif de base</span>
                            <span>{{ number_format($breakdown['base_price'], 2) }}€</span>
                        </div>
                    @endif
                @endif
                @if(($booking->addons_total ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Options</span>
                        <span>{{ number_format($booking->addons_total, 2) }}€</span>
                    </div>
                @endif
                @if(($booking->discount_amount ?? 0) > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Réduction {{ $booking->promoCode ? '(' . $booking->promoCode->code . ')' : '' }}</span>
                        <span>-{{ number_format($booking->discount_amount, 2) }}€</span>
                    </div>
                @endif
                <hr class="border-gray-200">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span class="text-primary-600">{{ number_format($booking->total_ttc ?? $booking->total_price, 2) }}€</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Status Card --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Statut</h2>
            @php
                $statusValue = $booking->status->value ?? $booking->status ?? 'pending';
                $paymentValue = $booking->payment_status->value ?? $booking->payment_status ?? 'pending';
            @endphp
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Réservation</p>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'completed' => 'bg-blue-100 text-blue-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($statusValue) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Paiement</p>
                    @php
                        $paymentColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-700',
                            'refunded' => 'bg-purple-100 text-purple-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $paymentColors[$paymentValue] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($paymentValue) }}
                    </span>
                </div>
            </div>

            <hr class="my-4 border-gray-200">

            <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <label class="text-xs text-gray-500 uppercase">Changer le statut</label>
                <select name="status" class="input text-sm">
                    <option value="pending" @selected($statusValue === 'pending')>En attente</option>
                    <option value="confirmed" @selected($statusValue === 'confirmed')>Confirmée</option>
                    <option value="cancelled" @selected($statusValue === 'cancelled')>Annulée</option>
                    <option value="completed" @selected($statusValue === 'completed')>Terminée</option>
                </select>
                <button type="submit" class="btn-primary w-full">
                    Mettre à jour
                </button>
            </form>
        </div>

        {{-- Customer Info --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-primary-500"></i>
                Client
            </h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Nom</p>
                    <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <a href="mailto:{{ $booking->customer_email }}" class="text-primary-600 hover:underline">
                        {{ $booking->customer_email }}
                    </a>
                </div>
                @if($booking->customer_phone)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Téléphone</p>
                        <a href="tel:{{ $booking->customer_phone }}" class="text-primary-600 hover:underline">
                            {{ $booking->customer_phone }}
                        </a>
                    </div>
                @endif
                @if($booking->country_code)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Pays</p>
                        <p class="text-gray-700">{{ $booking->country_code }}</p>
                    </div>
                @endif
            </div>
            @if($booking->client)
                <hr class="my-4 border-gray-200">
                <a href="{{ route('admin.clients.show', $booking->client) }}" class="text-sm text-primary-600 hover:underline">
                    Voir le profil client →
                </a>
            @endif
        </div>

        {{-- Channel --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Canal de vente</h2>
            <form method="POST" action="{{ route('admin.bookings.updateChannel', $booking) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="label">Canal</label>
                    <select name="channel" class="input w-full" required>
                        @foreach($channels as $value => $label)
                            <option value="{{ $value }}" @selected(($booking->channel ?? 'direct') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Réf. externe (OTA)</label>
                    <input type="text" name="channel_external_id" value="{{ $booking->channel_external_id }}" class="input w-full" placeholder="ID Viator / GYG">
                </div>
                <div>
                    <label class="label">Notes</label>
                    <textarea name="channel_notes" rows="2" class="input w-full">{{ $booking->channel_notes }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full text-sm">Mettre à jour le canal</button>
            </form>
        </div>

        {{-- Payment Info --}}
        @if($booking->payment_intent_id)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fab fa-stripe text-primary-500"></i>
                    Paiement Stripe
                </h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Payment Intent</p>
                        <p class="font-mono text-xs text-gray-600 break-all">{{ $booking->payment_intent_id }}</p>
                    </div>
                    @if($booking->confirmed_at)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Payé le</p>
                            <p class="text-gray-700">{{ $booking->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    @if($booking->refunded_at)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Remboursé le</p>
                            <p class="text-gray-700">{{ $booking->refunded_at->format('d/m/Y H:i') }}</p>
                            <p class="text-red-600 font-medium">{{ number_format($booking->refund_amount, 2) }}€</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Timeline --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Historique</h2>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-gray-300 mt-2"></div>
                    <div>
                        <p class="text-sm text-gray-700">Réservation créée</p>
                        <p class="text-xs text-gray-400">{{ $booking->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($booking->confirmed_at)
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                        <div>
                            <p class="text-sm text-gray-700">Réservation confirmée</p>
                            <p class="text-xs text-gray-400">{{ $booking->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($booking->cancelled_at)
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500 mt-2"></div>
                        <div>
                            <p class="text-sm text-gray-700">Réservation annulée</p>
                            <p class="text-xs text-gray-400">{{ $booking->cancelled_at->format('d/m/Y H:i') }}</p>
                            @if($booking->cancellation_reason)
                                <p class="text-xs text-gray-500">{{ $booking->cancellation_reason }}</p>
                            @endif
                        </div>
                    </div>
                @endif
                @if($booking->refunded_at)
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-purple-500 mt-2"></div>
                        <div>
                            <p class="text-sm text-gray-700">Remboursement effectué</p>
                            <p class="text-xs text-gray-400">{{ $booking->refunded_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Refund Modal --}}
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rembourser la réservation</h3>
        <form method="POST" action="{{ route('admin.bookings.refund', $booking) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="label">Montant à rembourser</label>
                    <input type="number" name="amount" step="0.01" 
                           value="{{ $booking->total_ttc ?? $booking->total_price }}" 
                           max="{{ $booking->total_ttc ?? $booking->total_price }}"
                           class="input">
                    <p class="text-xs text-gray-500 mt-1">Max: {{ number_format($booking->total_ttc ?? $booking->total_price, 2) }}€</p>
                </div>
                <div>
                    <label class="label">Raison (optionnel)</label>
                    <textarea name="reason" rows="2" class="input" placeholder="Raison du remboursement..."></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('refundModal').classList.add('hidden')" 
                        class="btn-outline flex-1">
                    Annuler
                </button>
                <button type="submit" class="btn-danger flex-1">
                    Confirmer le remboursement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
