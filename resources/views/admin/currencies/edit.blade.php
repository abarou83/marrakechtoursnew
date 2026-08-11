@extends('admin.layout')

@section('title', 'Modifier la devise ' . $currency->code)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.currencies.index') }}" class="text-indigo-600 hover:underline">← Retour</a>
    <h1 class="text-2xl font-bold mt-3 mb-6">Modifier: {{ $currency->code }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Code</label>
                <input type="text" value="{{ $currency->code }}" class="w-full border-gray-300 rounded-lg bg-gray-100" disabled>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $currency->name) }}" class="w-full border-gray-300 rounded-lg" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Symbole</label>
                <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Taux (vs devise de base) <span class="text-red-500">*</span></label>
                <input type="number" step="0.000001" name="rate_to_base" value="{{ old('rate_to_base', $currency->rate_to_base) }}" class="w-full border-gray-300 rounded-lg" required>
            </div>
            <div class="flex items-center">
                <label class="inline-flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-green-600 border-gray-300 rounded" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex space-x-4 mt-6">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
            <a href="{{ route('admin.currencies.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
