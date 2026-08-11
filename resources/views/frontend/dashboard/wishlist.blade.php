@extends('layouts.app')

@section('title', __('Mes favoris'))

@section('content')
<div class="bg-sand-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.dashboard._sidebar')

            <div class="flex-1">
                <div class="card p-6">
                    <h1 class="text-xl font-display font-bold text-sand-900 mb-6">{{ __('Mes favoris') }}</h1>

                    @if($wishlists->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($wishlists as $wishlist)
                                <x-tour-card :tour="$wishlist->tour" :in-wishlist="true" />
                            @endforeach
                        </div>

                        @if($wishlists->hasPages())
                            <div class="mt-8">
                                {{ $wishlists->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <x-heroicon-o-heart class="w-16 h-16 mx-auto text-sand-300 mb-4" />
                            <h3 class="text-lg font-semibold text-sand-700 mb-2">{{ __('Aucun favori') }}</h3>
                            <p class="text-sand-500 mb-4">{{ __('Ajoutez des tours à vos favoris en cliquant sur le cœur.') }}</p>
                            <a href="{{ route('tours.index') }}" class="btn-primary">
                                {{ __('Découvrir nos tours') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
