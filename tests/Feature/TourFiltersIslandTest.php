<?php

use App\Livewire\Islands\TourFiltersIsland;
use Livewire\Livewire;

beforeEach(function () {
    seedMinimalSite();
});

test('tour filters island renders search fields', function () {
    Livewire::test(TourFiltersIsland::class)
        ->assertOk()
        ->assertSee(__('Filter Tours'));
});

test('tour filters island redirects with query params', function () {
    Livewire::test(TourFiltersIsland::class)
        ->set('q', 'desert')
        ->call('search')
        ->assertRedirect(route('tours.index', ['q' => 'desert']));
});
