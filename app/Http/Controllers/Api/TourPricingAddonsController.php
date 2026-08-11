<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourPricing;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TourPricingAddonsController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Get addons for a specific tour pricing mode
     * 
     * Endpoint: GET /api/tours/{tour}/pricing/{pricingMode}/addons
     * 
     * BUSINESS RULE: Only returns addons attached to the active TourPricing
     * for the specified pricing_mode and season.
     * 
     * @param Tour $tour
     * @param string $pricingMode (group|private)
     * @param Request $request Optional: season, date
     * @return JsonResponse
     */
    public function getAddons(Tour $tour, string $pricingMode, Request $request): JsonResponse
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

        // If no pricing found, return empty addons
        if (!$pricing) {
            return response()->json([
                'success' => true,
                'pricing_mode' => $pricingMode,
                'season' => $season,
                'pricing_id' => null,
                'addons' => []
            ]);
        }

        // Load addons with pivot data and price tiers
        $pricing->load(['addons' => function($query) {
            $query->where('is_active', true)
                  ->orderBy('name')
                  ->with('priceTiers');
        }]);

        // Get total participants from request (for calculating tier prices)
        $totalParticipants = (int) $request->input('participants', 1);

        // Format addons response
        $addons = $pricing->addons->map(function($addon) use ($pricing, $totalParticipants) {
            // Pivot data is automatically loaded with the relationship
            $pivot = $addon->pivot;
            $isIncluded = $pivot ? (bool) $pivot->is_included : false;
            
            // Determine the price to display
            $displayPrice = 0;
            if (!$isIncluded) {
                // If override price exists, use it
                if ($pivot && $pivot->override_price) {
                    $displayPrice = (float) $pivot->override_price;
                } else {
                    // Check if addon has price tiers (for per_person addons)
                    if ($addon->pricing_type === 'per_person') {
                        // Load price tiers if not already loaded
                        if (!$addon->relationLoaded('priceTiers')) {
                            $addon->load('priceTiers');
                        }
                        
                        // If price tiers exist, find the matching tier for the current number of participants
                        if ($addon->priceTiers && $addon->priceTiers->isNotEmpty()) {
                            $sortedTiers = $addon->priceTiers->sortBy('min_people');
                            $matchingTier = $sortedTiers->first(function ($tier) use ($totalParticipants) {
                                return $totalParticipants >= (int)$tier->min_people && $totalParticipants <= (int)$tier->max_people;
                            });
                            
                            if ($matchingTier) {
                                // Use the matching tier price
                                $displayPrice = (float) $matchingTier->price;
                            } else {
                                // No matching tier, use the lowest tier price as fallback
                                $lowestTier = $sortedTiers->first();
                                $displayPrice = (float) $lowestTier->price;
                            }
                        } else {
                            // No tiers, use base_price
                            $displayPrice = (float) $addon->base_price;
                        }
                    } else {
                        // For per_group or free, use base_price
                        $displayPrice = (float) $addon->base_price;
                    }
                }
            }
            
            // Get translated name
            $translation = $addon->translate();
            $translatedName = $translation ? $translation->name : $addon->name;
            
            return [
                'id' => $addon->id,
                'name' => $translatedName,
                'pricing_type' => $addon->pricing_type,
                'base_price' => (float) $addon->base_price,
                // Display price (0 if included, otherwise override_price or tier price matching participants or base_price)
                'price' => $displayPrice,
                'is_required' => $pivot ? (bool) $pivot->is_required : false,
                'is_included' => $isIncluded,
                'override_price' => $pivot && $pivot->override_price ? (float) $pivot->override_price : null,
            ];
        });

        return response()->json([
            'success' => true,
            'pricing_mode' => $pricingMode,
            'season' => $season,
            'pricing_id' => $pricing->id,
            'addons' => $addons->values()->toArray()
        ]);
    }

}

