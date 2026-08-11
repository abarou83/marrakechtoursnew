@extends('layouts.app')

@section('title', __('Mes avis'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.dashboard._sidebar')

            <div class="flex-1 space-y-6">
                {{-- Pending Reviews --}}
                @if($pendingReviews->count() > 0)
                    <div class="card p-6 border-l-4 border-primary-500">
                        <h2 class="text-lg font-semibold text-sand-900 mb-4">
                            {{ __('Avis en attente') }}
                            <span class="text-sm font-normal text-sand-500">({{ $pendingReviews->count() }})</span>
                        </h2>
                        <p class="text-sm text-sand-600 mb-4">{{ __('Partagez votre expérience avec la communauté !') }}</p>
                        
                        <div class="space-y-3">
                            @foreach($pendingReviews as $booking)
                                <div class="flex items-center justify-between p-3 bg-sand-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        @if($booking->tour->getFirstMediaUrl('images', 'thumb'))
                                            <img src="{{ $booking->tour->getFirstMediaUrl('images', 'thumb') }}" 
                                                 alt="{{ $booking->tour->translate()?->title }}"
                                                 class="w-12 h-12 rounded object-cover">
                                        @endif
                                        <div>
                                            <p class="font-medium text-sand-900">{{ $booking->tour->translate()?->title }}</p>
                                            <p class="text-xs text-sand-500">
                                                {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('d F Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('dashboard.reviews.create', $booking) }}" class="btn-primary btn-sm">
                                        {{ __('Donner mon avis') }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- My Reviews --}}
                <div class="card p-6">
                    <h1 class="text-xl font-display font-bold text-sand-900 mb-6">{{ __('Mes avis') }}</h1>

                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-sand-100 pb-6 last:border-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <x-heroicon-s-star class="w-5 h-5 {{ $i <= $review->rating ? 'text-accent-500' : 'text-sand-300' }}" />
                                                    @endfor
                                                </div>
                                                <span @class([
                                                    'text-xs px-2 py-0.5 rounded',
                                                    'bg-green-100 text-green-700' => $review->status === 'approved',
                                                    'bg-yellow-100 text-yellow-700' => $review->status === 'pending',
                                                    'bg-red-100 text-red-700' => $review->status === 'rejected',
                                                ])>
                                                    {{ match($review->status) {
                                                        'approved' => __('Publié'),
                                                        'pending' => __('En attente'),
                                                        'rejected' => __('Refusé'),
                                                        default => $review->status,
                                                    } }}
                                                </span>
                                            </div>
                                            <a href="{{ route('tours.show', $review->tour->slug ?? $review->tour->id) }}" 
                                               class="font-semibold text-sand-900 hover:text-primary-600">
                                                {{ $review->tour->translate()?->title }}
                                            </a>
                                            @if($review->title)
                                                <p class="font-medium text-sand-800 mt-2">{{ $review->title }}</p>
                                            @endif
                                            <p class="text-sand-600 mt-1">{{ $review->comment }}</p>
                                            <p class="text-xs text-sand-400 mt-2">
                                                {{ $review->created_at->translatedFormat('d F Y') }}
                                            </p>

                                            @if($review->admin_response)
                                                <div class="mt-4 p-3 bg-primary-50 rounded-lg">
                                                    <p class="text-xs font-semibold text-primary-700 mb-1">{{ __('Réponse de l\'équipe') }}</p>
                                                    <p class="text-sm text-sand-700">{{ $review->admin_response }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if($review->status !== 'approved')
                                            <div class="flex gap-2 ml-4">
                                                <a href="{{ route('dashboard.reviews.edit', $review) }}" 
                                                   class="text-sand-400 hover:text-primary-600" title="{{ __('Modifier') }}">
                                                    <x-heroicon-o-pencil class="w-5 h-5" />
                                                </a>
                                                <form method="POST" action="{{ route('dashboard.reviews.destroy', $review) }}" 
                                                      onsubmit="return confirm('{{ __('Supprimer cet avis ?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sand-400 hover:text-red-600" title="{{ __('Supprimer') }}">
                                                        <x-heroicon-o-trash class="w-5 h-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($reviews->hasPages())
                            <div class="mt-6">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <x-heroicon-o-star class="w-16 h-16 mx-auto text-sand-300 mb-4" />
                            <h3 class="text-lg font-semibold text-sand-700 mb-2">{{ __('Aucun avis') }}</h3>
                            <p class="text-sand-500">{{ __('Vous n\'avez pas encore laissé d\'avis.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
