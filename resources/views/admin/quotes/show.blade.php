@extends('admin.layout')

@section('title', 'Détails du devis #' . $quote->id)

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Devis #{{ $quote->id }}</h2>
        <p class="text-sm text-gray-500">Détails de la demande de devis</p>
    </div>
    <div>
        <a href="{{ route('admin.quotes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
        <a href="{{ route('admin.quotes.edit', $quote) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm ml-2">
            <i class="fas fa-edit mr-2"></i>Modifier
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Informations principales --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Informations client --}}
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-indigo-500 mr-2"></i>Informations client
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Nom</label>
                    <div class="text-sm font-semibold text-gray-900">{{ $quote->name }}</div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                    <div class="text-sm text-gray-900">
                        <a href="mailto:{{ $quote->email }}" class="text-indigo-600 hover:underline">{{ $quote->email }}</a>
                    </div>
                </div>
                @if($quote->phone)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Téléphone</label>
                    <div class="text-sm text-gray-900">
                        <a href="tel:{{ $quote->phone }}" class="text-indigo-600 hover:underline">{{ $quote->phone }}</a>
                    </div>
                </div>
                @endif
                @if($quote->user)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Compte utilisateur</label>
                    <div class="text-sm text-gray-900">
                        <a href="{{ route('admin.users.edit', $quote->user) }}" class="text-indigo-600 hover:underline">
                            {{ $quote->user->name }} ({{ $quote->user->email }})
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Message --}}
        @if($quote->message)
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-comment text-indigo-500 mr-2"></i>Message
            </h3>
            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $quote->message }}</div>
        </div>
        @endif

        {{-- Détails du devis --}}
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Détails du devis
            </h3>
            <div class="grid grid-cols-2 gap-4">
                @if($quote->tour)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Tour</label>
                    <div class="text-sm font-semibold text-gray-900">
                        <a href="{{ route('admin.tours.edit', $quote->tour) }}" class="text-indigo-600 hover:underline">
                            {{ translate_model($quote->tour, 'title') }}
                        </a>
                    </div>
                </div>
                @endif
                @if($quote->participants)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Nombre de participants</label>
                    <div class="text-sm text-gray-900">{{ $quote->participants }}</div>
                </div>
                @endif
                @if($quote->preferred_date)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Date préférée</label>
                    <div class="text-sm text-gray-900">{{ $quote->preferred_date->format('d/m/Y') }}</div>
                </div>
                @endif
                @if($quote->estimated_budget)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Budget estimé</label>
                    @php
                        $from = $quote->currency ?: null;
                        $converted = \App\Helpers\CurrencyHelper::convert((float)$quote->estimated_budget, $from);
                        $formatted = \App\Helpers\CurrencyHelper::format($converted);
                    @endphp
                    <div class="text-sm font-semibold text-gray-900">{{ $formatted }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Statut --}}
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut</h3>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'viewed' => 'bg-blue-100 text-blue-800',
                    'contacted' => 'bg-indigo-100 text-indigo-800',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-700',
                ];
                $statusLabels = [
                    'pending' => 'En attente',
                    'viewed' => 'Vue',
                    'contacted' => 'Contactée',
                    'accepted' => 'Acceptée',
                    'rejected' => 'Refusée',
                ];
            @endphp
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statusLabels[$quote->status] ?? ucfirst($quote->status) }}
                </span>
            </div>
            <form method="POST" action="{{ route('admin.quotes.updateStatus', $quote) }}">
                @csrf
                @method('PATCH')
                <label class="block text-xs font-semibold text-gray-700 mb-2">Changer le statut</label>
                <select name="status" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['pending' => 'En attente', 'viewed' => 'Vue', 'contacted' => 'Contactée', 'accepted' => 'Acceptée', 'rejected' => 'Refusée'] as $statusValue => $label)
                        <option value="{{ $statusValue }}" {{ $quote->status === $statusValue ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Dates --}}
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Créé le</label>
                    <div class="text-gray-900">{{ $quote->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($quote->viewed_at)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Vu le</label>
                    <div class="text-gray-900">{{ $quote->viewed_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                @if($quote->contacted_at)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Contacté le</label>
                    <div class="text-gray-900">{{ $quote->contacted_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Notes admin --}}
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes administrateur</h3>
            @if($quote->admin_notes)
                <div class="text-sm text-gray-700 whitespace-pre-wrap mb-3">{{ $quote->admin_notes }}</div>
            @else
                <p class="text-sm text-gray-400 mb-3">Aucune note</p>
            @endif
            <a href="{{ route('admin.quotes.edit', $quote) }}" class="text-sm text-indigo-600 hover:underline">
                <i class="fas fa-edit mr-1"></i>Ajouter/Modifier
            </a>
        </div>
    </div>
</div>
@endsection

