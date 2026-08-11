@extends('admin.layout')

@section('title', 'Addons Management')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Addons Management</h2>
        <p class="text-sm text-gray-500">Manage add-ons that can be attached to tours (lunch, guide, camel ride, etc.)</p>
    </div>
    <a href="{{ route('admin.addons.create') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
        <i class="fas fa-plus mr-2"></i>New Addon
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pricing Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Base Price</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tours</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($addons as $addon)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-semibold">{{ $addon->name }}</div>
                        <div class="text-xs text-gray-500">{{ $addon->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $addon->pricing_type === 'per_person' ? 'bg-blue-100 text-blue-800' : ($addon->pricing_type === 'per_group' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                            {{ str_replace('_', ' ', ucfirst($addon->pricing_type)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if($addon->pricing_type === 'free')
                            <span class="text-gray-400">Free</span>
                        @else
                            €{{ number_format($addon->base_price, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($addon->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $addon->tours()->count() }} tour(s)
                    </td>
                    <td class="px-6 py-4 text-sm text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.addons.translations', $addon) }}" class="text-purple-600 hover:text-purple-900" title="Traductions">
                                <i class="fas fa-language"></i>
                            </a>
                            <a href="{{ route('admin.addons.edit', $addon) }}" class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this addon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No addons created yet. <a href="{{ route('admin.addons.create') }}" class="text-indigo-600 hover:underline">Create one</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($addons->hasPages())
    <div class="mt-4">
        {{ $addons->links() }}
    </div>
@endif
@endsection




