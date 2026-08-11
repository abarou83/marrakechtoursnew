@extends('admin.layout')

@section('title', 'Manage Tour Addons - ' . translate_model($tour, 'title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tours.index') }}" class="text-indigo-600 hover:underline">← Back to Tours</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Manage Addons for: {{ translate_model($tour, 'title') }}</h2>
    <p class="text-sm text-gray-500">Attach addons to this tour and set required/optional status</p>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Attached Addons -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attached Addons</h3>
        
        @if($tour->tourAddons->count() > 0)
            <div class="space-y-4">
                @foreach($tour->tourAddons as $tourAddon)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $tourAddon->addon->name }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ str_replace('_', ' ', ucfirst($tourAddon->addon->pricing_type)) }}
                                </p>
                            </div>
                            <form action="{{ route('admin.tours.addons.detach', [$tour, $tourAddon->addon]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($tourAddon->override_price)
                                <span class="font-medium">Override Price: €{{ number_format($tourAddon->override_price, 2) }}</span>
                                <span class="text-gray-400">(Base: €{{ number_format($tourAddon->addon->base_price, 2) }})</span>
                            @else
                                <span>Base Price: €{{ number_format($tourAddon->addon->base_price, 2) }}</span>
                            @endif
                        </div>
                        @if($tourAddon->is_required)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                                Required
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                                Optional
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm">No addons attached yet.</p>
        @endif
    </div>

    <!-- Available Addons -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attach New Addon</h3>
        
        <form action="{{ route('admin.tours.addons.attach', $tour) }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="addon_id" class="block text-sm font-medium text-gray-700 mb-2">Select Addon *</label>
                <select name="addon_id" id="addon_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Choose an addon...</option>
                    @foreach($allAddons as $addon)
                        @if(!$tour->tourAddons->contains('addon_id', $addon->id))
                            <option value="{{ $addon->id }}">{{ $addon->name }} ({{ str_replace('_', ' ', $addon->pricing_type) }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label for="override_price" class="block text-sm font-medium text-gray-700 mb-2">Override Price (€)</label>
                <input type="number" name="override_price" id="override_price" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-sm text-gray-500">Leave empty to use base price</p>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_required" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Required (must be selected)</span>
                </label>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Attach Addon
            </button>
        </form>
    </div>
</div>
@endsection
