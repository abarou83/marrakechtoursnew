@extends('admin.layout')

@section('title', 'Tour Pricings - ' . translate_model($tour, 'title'))

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.tours.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tours
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Tour Pricings</h1>
                <p class="text-gray-600 text-sm md:text-base">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                    {{ translate_model($tour, 'title') }}
                </p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        Type: {{ $tour->type instanceof \BackedEnum ? $tour->type->label() : ucfirst($tour->type ?? 'activity') }}
                    </span>
                    @if($tour->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            <i class="fas fa-check-circle mr-1 text-green-600"></i>Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            <i class="fas fa-times-circle mr-1 text-gray-400"></i>Inactive
                        </span>
                    @endif
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-gray-100 rounded-lg p-4">
                    <i class="fas fa-euro-sign text-4xl text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
@endif

<!-- Two Column Layout: Private (Left) and Group (Right) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Private Prices Block (Left) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-user-lock text-gray-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Price Private</h3>
                    <p class="text-sm text-gray-500">Per group pricing</p>
                </div>
            </div>
            <a href="{{ route('admin.tour-pricings.create', $tour) }}?mode=private" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-semibold">
                <i class="fas fa-plus mr-2"></i>Add Price
            </a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($privatePricings as $pricing)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                @if($pricing->title)
                                    <h4 class="text-base font-bold text-gray-900 mb-2">{{ $pricing->title }}</h4>
                                @endif
                                <div class="flex items-center gap-2 mb-3">
                                    @if($pricing->season === 'all')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-globe mr-1.5 text-xs"></i>All Seasons
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-calendar-alt mr-1.5 text-xs"></i>{{ ucfirst($pricing->season) }}
                                        </span>
                                    @endif
                                    @if($pricing->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-check-circle mr-1.5 text-xs text-green-600"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-2 mt-3">
                                    @foreach($pricing->privatePrices as $privatePrice)
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-3 border border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-gray-100 rounded p-2">
                                                    <i class="fas fa-users text-gray-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900 text-sm">{{ $privatePrice->min_people }}-{{ $privatePrice->max_people }} people</span>
                                                    <p class="text-xs text-gray-500 mt-0.5">Group size</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-gray-900">€{{ number_format($privatePrice->price, 2) }}</div>
                                                <p class="text-xs text-gray-500">per group</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 mt-4">
                            <a href="{{ route('admin.tour-pricings.translations', $pricing) }}" class="inline-flex items-center px-4 py-2 bg-white border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-all font-medium text-sm" title="Traductions">
                                <i class="fas fa-language mr-2"></i>Traductions
                            </a>
                            <a href="{{ route('admin.tour-pricings.edit', [$tour, $pricing]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium text-sm">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <form action="{{ route('admin.tour-pricings.destroy', [$tour, $pricing]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this pricing?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-medium text-sm">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-6 mb-4">
                                <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Private Prices Yet</h3>
                            <p class="text-sm text-gray-500 mb-6">Start by creating your first private pricing configuration</p>
                            <a href="{{ route('admin.tour-pricings.create', $tour) }}?mode=private" class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-medium">
                                <i class="fas fa-plus mr-2"></i>Add Your First Private Price
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Group Prices Block (Right) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-users text-gray-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Price Group</h3>
                    <p class="text-sm text-gray-500">Per person pricing</p>
                </div>
            </div>
            <a href="{{ route('admin.tour-pricings.create', $tour) }}?mode=group" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-semibold">
                <i class="fas fa-plus mr-2"></i>Add Price
            </a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($groupPricings as $pricing)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                @if($pricing->title)
                                    <h4 class="text-base font-bold text-gray-900 mb-2">{{ $pricing->title }}</h4>
                                @endif
                                <div class="flex items-center gap-2 mb-3">
                                    @if($pricing->season === 'all')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-globe mr-1.5 text-xs"></i>All Seasons
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-calendar-alt mr-1.5 text-xs"></i>{{ ucfirst($pricing->season) }}
                                        </span>
                                    @endif
                                    @if($pricing->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-check-circle mr-1.5 text-xs text-green-600"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-2 mt-3">
                                    @foreach($pricing->groupPrices as $groupPrice)
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-3 border border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-gray-100 rounded p-2">
                                                    @if($groupPrice->category === 'adult')
                                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                                    @elseif($groupPrice->category === 'child')
                                                        <i class="fas fa-child text-gray-600 text-sm"></i>
                                                    @else
                                                        <i class="fas fa-baby text-gray-600 text-sm"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900 text-sm capitalize">{{ $groupPrice->category }}</span>
                                                    @if($groupPrice->age_min || $groupPrice->age_max)
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $groupPrice->age_min ?? '0' }}-{{ $groupPrice->age_max ?? '+' }} years</p>
                                                    @else
                                                        <p class="text-xs text-gray-500 mt-0.5">All ages</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-gray-900">€{{ number_format($groupPrice->price, 2) }}</div>
                                                <p class="text-xs text-gray-500">per person</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 mt-4">
                            <a href="{{ route('admin.tour-pricings.translations', $pricing) }}" class="inline-flex items-center px-4 py-2 bg-white border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-all font-medium text-sm" title="Traductions">
                                <i class="fas fa-language mr-2"></i>Traductions
                            </a>
                            <a href="{{ route('admin.tour-pricings.edit', [$tour, $pricing]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium text-sm">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <form action="{{ route('admin.tour-pricings.destroy', [$tour, $pricing]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this pricing?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-medium text-sm">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-6 mb-4">
                                <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Group Prices Yet</h3>
                            <p class="text-sm text-gray-500 mb-6">Start by creating your first group pricing configuration</p>
                            <a href="{{ route('admin.tour-pricings.create', $tour) }}?mode=group" class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all font-medium">
                                <i class="fas fa-plus mr-2"></i>Add Your First Group Price
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
