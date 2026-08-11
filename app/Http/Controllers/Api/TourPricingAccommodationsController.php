<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TourPricingAccommodationsController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Get accommodations for a specific tour pricing mode
     * 
     * Endpoint: GET /api/tours/{tour}/pricing/{pricingMode}/accommodations
     * 
     * BUSINESS RULE: Only returns accommodations attached to the active TourPricing
     * for the specified pricing_mode and season.
     * 
     * @param Tour $tour
     * @param string $pricingMode (group|private)
     * @param Request $request Optional: season, date
     * @return JsonResponse
     */
    public function getAccommodations(Tour $tour, string $pricingMode, Request $request): JsonResponse
    {
        // Validate pricing mode (MANDATORY)
        if (!in_array($pricingMode, ['group', 'private'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pricing mode. Must be "group" or "private".'
            ], 400);
        }

        // Determine season using PricingService for consistency
        $season = 'all';
        if ($request->has('date')) {
            try {
                $date = Carbon::parse($request->date);
                $season = $this->pricingService->determineSeason($date);
            } catch (\Exception $e) {
                // Invalid date, use 'all' season
            }
        } elseif ($request->has('season')) {
            $season = $request->season;
        }

        // Get active pricing (specific formula if pricing_id provided)
        $pricing = $this->pricingService->resolvePricing(
            $tour,
            $pricingMode,
            $request->filled('pricing_id') ? (int) $request->input('pricing_id') : null,
            $season !== 'all' ? $season : null,
            $request->has('date') ? Carbon::parse($request->date) : null
        );

        // If no pricing found, return empty accommodations
        if (!$pricing) {
            return response()->json([
                'success' => true,
                'pricing_mode' => $pricingMode,
                'season' => $season,
                'pricing_id' => null,
                'accommodations' => []
            ]);
        }

        // Load accommodations with rooms
        $pricing->load(['accommodations' => function($query) {
            $query->where('is_active', true)
                  ->with(['rooms' => function($q) {
                      $q->where('is_active', true)
                        ->orderBy('room_type');
                  }])
                  ->orderBy('pricing_accommodations.display_order');
        }]);

        // Format accommodations response
        $accommodations = $pricing->accommodations->map(function($accommodation) {
            $pivot = $accommodation->pivot;
            
            // Get available room types
            $rooms = $accommodation->rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'type' => $room->room_type,
                    'type_name' => $room->room_type_name,
                    'price_per_night' => (float) $room->price_per_night,
                    'max_occupancy' => (int) $room->max_occupancy,
                    'description' => $room->description,
                ];
            });
            
            $translation = $accommodation->translate();
            return [
                'id' => $accommodation->id,
                'name' => $translation ? $translation->name : $accommodation->name,
                'description' => $translation ? ($translation->description ?: $accommodation->description) : $accommodation->description,
                'location' => $translation ? ($translation->location ?: $accommodation->location) : $accommodation->location,
                'address' => $accommodation->address,
                'stars' => $accommodation->stars,
                'is_optional' => $pivot ? (bool) $pivot->is_optional : true,
                'nights' => $pivot ? (int) ($pivot->nights ?? 1) : 1,
                'rooms' => $rooms->values()->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'pricing_mode' => $pricingMode,
            'season' => $season,
            'pricing_id' => $pricing->id,
            'accommodations' => $accommodations->values()->toArray()
        ]);
    }
}