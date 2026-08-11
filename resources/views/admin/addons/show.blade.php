@extends('admin.layout')

@section('title', 'View Addon - ' . $addon->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.addons.index') }}" class="text-indigo-600 hover:underline">← Back to Addons</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Addon Details</h2>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $addon->name }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <p class="text-sm text-gray-900">{{ $addon->slug }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Type</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $addon->pricing_type === 'per_person' ? 'bg-blue-100 text-blue-800' : ($addon->pricing_type === 'per_group' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                    {{ str_replace('_', ' ', ucfirst($addon->pricing_type)) }}
                </span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                <p class="text-sm text-gray-900">
                    @if($addon->pricing_type === 'free')
                        Free
                    @else
                        €{{ number_format($addon->base_price, 2) }}
                    @endif
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                @if($addon->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Inactive
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if($addon->tours->count() > 0)
        <div class="border-t pt-6 mt-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Attached to Tours ({{ $addon->tours->count() }})</h4>
            <div class="space-y-2">
                @foreach($addon->tours as $tour)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div>
                            <a href="{{ route('admin.tours.edit', $tour) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                {{ $tour->title }}
                            </a>
                            <div class="text-xs text-gray-500 mt-1">
                                @if($tour->pivot->is_required)
                                    <span class="text-red-600">Required</span>
                                @else
                                    <span class="text-gray-600">Optional</span>
                                @endif
                                @if($tour->pivot->override_price)
                                    | Override: €{{ number_format($tour->pivot->override_price, 2) }}
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.tours.addons', $tour) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Manage
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="border-t pt-6 mt-6">
            <p class="text-gray-500 text-sm">This addon is not attached to any tours yet.</p>
        </div>
    @endif

    <div class="flex justify-end space-x-4 mt-6 pt-6 border-t">
        <a href="{{ route('admin.addons.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Back to List
        </a>
        <a href="{{ route('admin.addons.edit', $addon) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Edit Addon
        </a>
    </div>
</div>
@endsection




