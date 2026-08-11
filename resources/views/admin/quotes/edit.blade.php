@extends('admin.layout')

@section('title', 'Éditer le devis #' . $quote->id)

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Éditer le devis #{{ $quote->id }}</h2>
        <p class="text-sm text-gray-500">Modifier les informations du devis</p>
    </div>
    <div>
        <a href="{{ route('admin.quotes.show', $quote) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.quotes.update', $quote) }}" class="bg-white rounded-xl shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Statut --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border-gray-300 rounded-lg px-3 py-2" required>
                @foreach(['pending' => 'En attente', 'viewed' => 'Vue', 'contacted' => 'Contactée', 'accepted' => 'Acceptée', 'rejected' => 'Refusée'] as $statusValue => $label)
                    <option value="{{ $statusValue }}" {{ $quote->status === $statusValue ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tour --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tour (optionnel)</label>
            <select name="tour_id" class="w-full border-gray-300 rounded-lg px-3 py-2">
                <option value="">— Aucun tour —</option>
                @foreach($tours as $tour)
                    <option value="{{ $tour->id }}" {{ $quote->tour_id == $tour->id ? 'selected' : '' }}>
                        {{ translate_model($tour, 'title') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Notes admin --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Notes administrateur</label>
        <textarea name="admin_notes" rows="6" class="w-full border-gray-300 rounded-lg px-3 py-2" placeholder="Ajoutez des notes internes sur ce devis...">{{ old('admin_notes', $quote->admin_notes) }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Ces notes sont visibles uniquement par les administrateurs.</p>
    </div>

    <div class="flex space-x-4 pt-4 border-t border-gray-200">
        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">
            <i class="fas fa-save mr-2"></i>Enregistrer les modifications
        </button>
        <a href="{{ route('admin.quotes.show', $quote) }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
            Annuler
        </a>
    </div>
</form>
@endsection

