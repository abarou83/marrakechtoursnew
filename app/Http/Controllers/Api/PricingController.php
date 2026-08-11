<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculatePriceRequest;
use App\Models\Tour;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Calculate price for a booking
     * 
     * POST /api/calculate-price
     */
    public function calculatePrice(CalculatePriceRequest $request): JsonResponse
    {
        try {
            $tour = Tour::findOrFail($request->tour_id);

            if (!$tour->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tour is not active',
                ], 400);
            }

            $date = Carbon::parse($request->date);
            $adults = $request->adults ?? 0;
            $children = $request->children ?? 0;
            $infants = $request->infants ?? 0;
            $selectedAddons = $request->selected_addons ?? [];

            // Validate people count for private mode
            if ($request->pricing_mode === 'private') {
                // For private mode, adults contains the total people count
                $totalPeople = $adults;
                if ($totalPeople < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'At least one person is required for private pricing',
                    ], 400);
                }
                // Reset children and infants for private mode
                $children = 0;
                $infants = 0;
            }

            // Prepare accommodation data if provided (support multiple rooms)
            $accommodationData = null;
            if ($request->has('accommodation_rooms') && is_array($request->accommodation_rooms) && count($request->accommodation_rooms) > 0) {
                $accommodationData = [
                    'rooms' => $request->accommodation_rooms,
                    'nights' => $request->nights ?? 1,
                ];
            } elseif ($request->has('accommodation_id') && $request->has('accommodation_room_id')) {
                // Legacy support: single room
                $accommodationData = [
                    'rooms' => [[
                        'accommodation_id' => $request->accommodation_id,
                        'accommodation_room_id' => $request->accommodation_room_id,
                        'room_type' => $request->room_type,
                        'quantity' => 1,
                    ]],
                    'nights' => $request->nights ?? 1,
                ];
            }

            $result = $this->pricingService->calculatePrice(
                $tour,
                $request->pricing_mode,
                $date,
                $adults,
                $children,
                $infants,
                $selectedAddons,
                $request->pricing_id,
                $accommodationData
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            \Log::error('Price calculation error', [
                'tour_id' => $request->tour_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available addons for a tour
     * 
     * GET /api/tours/{tour}/addons
     */
    public function getTourAddons(Tour $tour): JsonResponse
    {
        $addons = $this->pricingService->getAvailableAddons($tour);

        return response()->json([
            'success' => true,
            'data' => $addons->map(function ($addon) {
                $translation = $addon->translate();
                return [
                    'id' => $addon->id,
                    'name' => $translation ? $translation->name : $addon->name,
                    'pricing_type' => $addon->pricing_type,
                    'base_price' => $addon->base_price,
                    'is_required' => $addon->pivot->is_required ?? false,
                    'override_price' => $addon->pivot->override_price ?? null,
                ];
            }),
        ]);
    }
}





