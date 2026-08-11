<?php

use App\Http\Controllers\Api\PricingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public pricing API
    Route::post('/calculate-price', [PricingController::class, 'calculatePrice'])->name('api.calculate-price');
    Route::get('/tours/{tour}/addons', [PricingController::class, 'getTourAddons'])->name('api.tours.addons');
    
    // Tour pricing addons API (based on pricing mode)
    Route::get('/tours/{tour}/pricing/{pricingMode}/addons', [\App\Http\Controllers\Api\TourPricingAddonsController::class, 'getAddons'])->name('api.tours.pricing.addons');
    
    // Tour pricing accommodations API (based on pricing mode)
    Route::get('/tours/{tour}/pricing/{pricingMode}/accommodations', [\App\Http\Controllers\Api\TourPricingAccommodationsController::class, 'getAccommodations'])->name('api.tours.pricing.accommodations');
    
    // Tour dates and departure times API
    Route::get('/tours/{tour}/times', [\App\Http\Controllers\Api\TourDatesController::class, 'getAllDepartureTimes'])->name('api.tours.times');
    Route::get('/tours/{tour}/dates/{date}/times', [\App\Http\Controllers\Api\TourDatesController::class, 'getDepartureTimes'])->name('api.tours.dates.times');
});



