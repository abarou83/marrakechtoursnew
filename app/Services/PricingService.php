<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourPricing;
use App\Models\Addon;
use App\Models\TourAddon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PricingService
{
    /**
     * Determine season based on date
     * 
     * @param Carbon|string $date
     * @return string (low|normal|high)
     */
    public function determineSeason($date): string
    {
        if ($date instanceof Carbon) {
            $month = $date->month;
        } else {
            $month = Carbon::parse($date)->month;
        }

        // Define seasons (can be customized)
        // High season: June, July, August, December
        // Low season: January, February, November
        // Normal: rest
        
        if (in_array($month, [6, 7, 8, 12])) {
            return 'high';
        } elseif (in_array($month, [1, 2, 11])) {
            return 'low';
        }
        
        return 'normal';
    }

    /**
     * Get pricing for a tour by mode and season
     * 
     * @param Tour $tour
     * @param string $pricingMode (group|private)
     * @param string|null $season (low|normal|high) - if null, will be determined from date
     * @param Carbon|string|null $date - used to determine season if not provided
     * @return TourPricing|null
     */
    public function getPricing(Tour $tour, string $pricingMode, ?string $season = null, $date = null): ?TourPricing
    {
        // Determine season if not provided
        if ($season === null && $date !== null) {
            $season = $this->determineSeason($date);
        } elseif ($season === null) {
            $season = 'normal'; // Default
        }

        $cacheKey = "tour_pricing_{$tour->id}_{$pricingMode}_{$season}";

        return Cache::remember($cacheKey, 3600, function () use ($tour, $pricingMode, $season) {
            // First, try to find a pricing for the specific season
            $pricing = TourPricing::where('tour_id', $tour->id)
                ->where('pricing_mode', $pricingMode)
                ->where('season', $season)
                ->where('is_active', true)
                ->with(['groupPrices', 'privatePrices'])
                ->first();
            
            // If not found, try to find a pricing for "all" seasons
            if (!$pricing) {
                $pricing = TourPricing::where('tour_id', $tour->id)
                    ->where('pricing_mode', $pricingMode)
                    ->where('season', 'all')
                    ->where('is_active', true)
                    ->with(['groupPrices', 'privatePrices'])
                    ->first();
            }
            
            // Verify that the pricing has prices configured
            if ($pricing) {
                if ($pricingMode === 'group' && $pricing->groupPrices->isEmpty()) {
                    // Pricing exists but has no group prices - return null to trigger error
                    return null;
                }
                if ($pricingMode === 'private' && $pricing->privatePrices->isEmpty()) {
                    // Pricing exists but has no private prices - return null to trigger error
                    return null;
                }
            }
            
            return $pricing;
        });
    }

    /**
     * Resolve the TourPricing for API/display: prefer explicit pricing_id, else mode+season.
     */
    public function resolvePricing(Tour $tour, string $pricingMode, ?int $pricingId = null, ?string $season = null, $date = null): ?TourPricing
    {
        if ($pricingId) {
            $pricing = TourPricing::where('id', $pricingId)
                ->where('tour_id', $tour->id)
                ->where('pricing_mode', $pricingMode)
                ->where('is_active', true)
                ->first();

            if ($pricing) {
                return $pricing;
            }
        }

        return $this->getPricing($tour, $pricingMode, $season, $date);
    }

    /**
     * Calculate group pricing
     * 
     * @param TourPricing $pricing
     * @param int $adults
     * @param int $children
     * @param int $infants
     * @return array
     * @throws \Exception
     */
    public function calculateGroupPrice(TourPricing $pricing, int $adults, int $children = 0, int $infants = 0): array
    {
        if ($pricing->pricing_mode !== 'group') {
            throw new \Exception('Pricing mode must be "group" for group pricing calculation');
        }

        // Ensure relationships are loaded
        if (!$pricing->relationLoaded('groupPrices')) {
            $pricing->load('groupPrices');
        }

        // Check if pricing has any group prices configured
        if ($pricing->groupPrices->isEmpty()) {
            throw new \Exception('Ce tarif n\'a aucun prix configuré. Veuillez ajouter au moins un prix adulte dans l\'admin: /admin/tours/' . $pricing->tour_id . '/pricings/' . $pricing->id . '/edit');
        }

        $adultPrice = $pricing->getAdultPrice();
        $childPrice = $pricing->getChildPrice();
        
        // Vérifier si la catégorie infant existe vraiment dans les groupPrices
        $infantPriceExists = $pricing->groupPrices->where('category', 'infant')->isNotEmpty();
        $infantPrice = $infantPriceExists ? $pricing->getInfantPrice() : null;

        if ($adultPrice == 0 && $adults > 0) {
            throw new \Exception('Le tarif adulte n\'est pas configuré pour ce pricing. Veuillez configurer au moins un tarif adulte dans l\'admin: /admin/tours/' . $pricing->tour_id . '/pricings/' . $pricing->id . '/edit');
        }

        $adultsTotal = $adults * $adultPrice;
        $childrenTotal = $children * $childPrice;
        
        // Ne calculer le prix des bébés que si la catégorie infant existe et a un prix > 0
        $infantsTotal = 0;
        if ($infants > 0 && $infantPriceExists && $infantPrice !== null && $infantPrice > 0) {
        $infantsTotal = $infants * $infantPrice;
        }

        $basePrice = $adultsTotal + $childrenTotal + $infantsTotal;
        
        // Calculate discount percentage for children (if child price is less than adult price)
        $childDiscountPercentage = null;
        if ($adultPrice > 0 && $childPrice < $adultPrice) {
            $childDiscountPercentage = round((($adultPrice - $childPrice) / $adultPrice) * 100);
        }

        return [
            'base_price' => $basePrice,
            'breakdown' => [
                'adults' => [
                    'quantity' => $adults,
                    'unit_price' => $adultPrice,
                    'total' => $adultsTotal,
                ],
                'children' => [
                    'quantity' => $children,
                    'unit_price' => $childPrice,
                    'total' => $childrenTotal,
                    'discount_percentage' => $childDiscountPercentage,
                ],
                'infants' => [
                    'quantity' => $infants,
                    'unit_price' => ($infantPriceExists && $infantPrice !== null && $infantPrice > 0) ? $infantPrice : 0,
                    'total' => $infantsTotal,
                ],
            ],
        ];
    }

    /**
     * Calculate private pricing
     * 
     * @param TourPricing $pricing
     * @param int $totalPeople
     * @return array
     * @throws \Exception
     */
    public function calculatePrivatePrice(TourPricing $pricing, int $totalPeople): array
    {
        if ($pricing->pricing_mode !== 'private') {
            throw new \Exception('Pricing mode must be "private" for private pricing calculation');
        }

        // Ensure relationships are loaded
        if (!$pricing->relationLoaded('privatePrices')) {
            $pricing->load('privatePrices');
        }

        // Check if pricing has any private prices configured
        if ($pricing->privatePrices->isEmpty()) {
            throw new \Exception('Ce tarif privé n\'a aucun prix configuré. Veuillez ajouter au moins un tarif par nombre de personnes dans l\'admin: /admin/tours/' . $pricing->tour_id . '/pricings/' . $pricing->id . '/edit');
        }

        $price = $pricing->getPrivatePriceForPeople($totalPeople);

        if ($price === null) {
            throw new \Exception("Aucun tarif trouvé pour {$totalPeople} personne(s). Veuillez configurer un tarif pour ce nombre de participants dans l'admin: /admin/tours/{$pricing->tour_id}/pricings/{$pricing->id}/edit");
        }

        return [
            'base_price' => $price,
            'breakdown' => [
                'people' => $totalPeople,
                'unit_price' => $price,
                'total' => $price,
            ],
        ];
    }

    /**
     * Calculate addons pricing based on TourPricing
     * 
     * @param TourPricing $pricing The pricing for which addons are attached
     * @param array $selectedAddons Array of ['addon_id' => quantity]
     * @param int $totalPeople Total people for per_person pricing
     * @return array
     * @throws \Exception If invalid addons are selected
     */
    public function calculateAddonsPriceForPricing(TourPricing $pricing, array $selectedAddons, int $totalPeople = 1): array
    {
        $addonsTotal = 0;
        $addonsDetails = [];

        // Get valid addon IDs for this pricing
        $validAddonIds = $pricing->addons()->pluck('addon_id')->toArray();

        foreach ($selectedAddons as $addonId => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            // SECURITY: Strict validation - addon MUST belong to this pricing
            // This prevents frontend manipulation and ensures data integrity
            if (!in_array($addonId, $validAddonIds)) {
                \Log::warning('Invalid addon attempt', [
                    'pricing_id' => $pricing->id,
                    'addon_id' => $addonId,
                    'valid_addon_ids' => $validAddonIds
                ]);
                throw new \Exception("Addon ID {$addonId} is not valid for this pricing. Security validation failed. Only addons attached to this TourPricing are allowed.");
            }

            // Get addon from pricing relationship with priceTiers loaded
            $addon = $pricing->addons()->with('priceTiers')->where('addon_id', $addonId)->first();

            if (!$addon || !$addon->is_active) {
                continue;
            }

            // Get pivot data (override_price, is_required)
            $pivot = $addon->pivot;
            
            if (!$pivot) {
                continue; // Skip if no pivot data
            }

            // Calculate price based on pricing type
            // Note: priceTiers will be loaded in the per_person case if needed
            $addonTotal = 0;
            $unitPrice = $pivot->override_price ?? $addon->base_price;
            
            switch ($addon->pricing_type) {
                case 'per_person':
                    // Check if addon has price tiers (e.g., guide with tiers: 1-5 = 20€, 6-8 = 60€)
                    // The price tiers are already loaded via ->with('priceTiers') above
                    $priceTiers = $addon->priceTiers ?? collect();
                    
                    if ($priceTiers->isNotEmpty()) {
                        // Find matching tier for the number of people
                        // Sort tiers by min_people to ensure we check in order
                        $sortedTiers = $priceTiers->sortBy('min_people');
                        
                        $tier = $sortedTiers->first(function ($tier) use ($totalPeople) {
                            return $totalPeople >= (int)$tier->min_people && $totalPeople <= (int)$tier->max_people;
                        });

                        if ($tier) {
                            // Use tier price (fixed price for the range, not per person)
                            // Example: if 1-5 people = 60€, use 60€ for 1, 2, 3, 4, or 5 people
                            // Example: if 6-10 people = 70€, use 70€ for 6, 7, 8, 9, or 10 people
                            // Override price takes precedence if provided
                            $tierPrice = (float) $tier->price;
                            $addonTotal = $pivot->override_price ?? $tierPrice;
                            $unitPrice = $addonTotal; // For display purposes
                        } else {
                            // No matching tier found, use override or base price × total people
                            \Log::warning('No matching tier found for addon', [
                                'addon_id' => $addon->id,
                                'addon_name' => $addon->name,
                                'total_people' => $totalPeople,
                                'available_tiers' => $sortedTiers->map(function($t) {
                                    return "{$t->min_people}-{$t->max_people} = {$t->price}";
                                })->toArray()
                            ]);
                            $unitPrice = $pivot->override_price ?? $addon->base_price;
                            $addonTotal = $unitPrice * $totalPeople;
                        }
                    } else {
                        // No tiers configured: use override price if available, otherwise base price × total people
                        // In private mode: $totalPeople = number of participants (adults)
                        // In group mode: $totalPeople = adults + children + infants
                        $unitPrice = $pivot->override_price ?? $addon->base_price;
                    $addonTotal = $unitPrice * $totalPeople;
                    }
                    break;
                case 'per_group':
                    // For per_group: fixed price per group (quantity is number of addons)
                    $unitPrice = $pivot->override_price ?? $addon->base_price;
                    $addonTotal = $unitPrice * $quantity;
                    break;
                case 'free':
                    $addonTotal = 0;
                    $unitPrice = 0;
                    break;
            }

            $addonsTotal += $addonTotal;

            // Get is_included from pivot
            $isIncluded = (bool) ($pivot->is_included ?? false);
            
            $addonTranslation = $addon->translate();
            $addonsDetails[] = [
                'addon_id' => $addon->id,
                'addon_name' => $addonTranslation ? $addonTranslation->name : $addon->name,
                'pricing_type' => $addon->pricing_type,
                'quantity' => $addon->pricing_type === 'per_person' ? $totalPeople : $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $addonTotal,
                'is_required' => (bool) $pivot->is_required,
                'is_included' => $isIncluded,
            ];
        }

        return [
            'addons_total' => $addonsTotal,
            'addons' => $addonsDetails,
        ];
    }

    /**
     * Calculate addons pricing (legacy method - uses TourAddon)
     * 
     * @param Tour $tour
     * @param array $selectedAddons Array of ['addon_id' => quantity]
     * @param int $totalPeople Total people for per_person pricing
     * @return array
     * @deprecated Use calculateAddonsPriceForPricing instead
     */
    public function calculateAddonsPrice(Tour $tour, array $selectedAddons, int $totalPeople = 1): array
    {
        $addonsTotal = 0;
        $addonsDetails = [];

        foreach ($selectedAddons as $addonId => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            $tourAddon = TourAddon::where('tour_id', $tour->id)
                ->where('addon_id', $addonId)
                ->first();

            if (!$tourAddon) {
                continue;
            }

            $addon = $tourAddon->addon;

            if (!$addon || !$addon->is_active) {
                continue;
            }

            // Use override price if available, otherwise use base price
            $unitPrice = $tourAddon->override_price ?? $addon->base_price;

            // Calculate price based on pricing type
            $addonTotal = 0;
            switch ($addon->pricing_type) {
                case 'per_person':
                    // For per_person: price per person × total people
                    // $quantity is the number of times this addon is selected (usually 1)
                    $addonTotal = $unitPrice * $totalPeople;
                    break;
                case 'per_group':
                    // For per_group: fixed price per group (quantity is number of addons)
                    $addonTotal = $unitPrice * $quantity;
                    break;
                case 'free':
                    $addonTotal = 0;
                    break;
            }

            $addonsTotal += $addonTotal;

            $addonTrans = $addon->translate();
            $addonsDetails[] = [
                'addon_id' => $addon->id,
                'addon_name' => $addonTrans ? $addonTrans->name : $addon->name,
                'pricing_type' => $addon->pricing_type,
                'quantity' => $addon->pricing_type === 'per_person' ? $totalPeople : $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $addonTotal,
                'is_required' => $tourAddon->is_required,
            ];
        }

        return [
            'addons_total' => $addonsTotal,
            'addons' => $addonsDetails,
        ];
    }

    /**
     * Calculate complete price for a booking
     * 
     * @param Tour $tour
     * @param string $pricingMode (group|private)
     * @param Carbon|string $date
     * @param int $adults
     * @param int $children
     * @param int $infants
     * @param array $selectedAddons Array of ['addon_id' => quantity]
     * @return array
     * @throws \Exception
     */
    public function calculatePrice(
        Tour $tour,
        string $pricingMode,
        $date,
        int $adults,
        int $children = 0,
        int $infants = 0,
        array $selectedAddons = [],
        ?int $pricingId = null,
        ?array $accommodationData = null
    ): array {
        // Get pricing - use specific pricing_id if provided, otherwise find by mode and season
        if ($pricingId) {
            $pricing = TourPricing::where('id', $pricingId)
                ->where('tour_id', $tour->id)
                ->where('pricing_mode', $pricingMode)
                ->where('is_active', true)
                ->with(['groupPrices', 'privatePrices', 'addons'])
                ->first();
            
            if (!$pricing) {
                throw new \Exception("Le tarif sélectionné n'existe pas ou n'est pas actif.");
            }
        } else {
            $season = $this->determineSeason($date);
            $pricing = $this->getPricing($tour, $pricingMode, $season, $date);
        }

        if (!$pricing) {
            $seasonName = ucfirst($season);
            
            // Check if tour has any pricing at all
            $hasAnyPricing = $tour->pricings()->where('is_active', true)->exists();
            $hasModePricing = $tour->pricings()
                ->where('pricing_mode', $pricingMode)
                ->where('is_active', true)
                ->exists();
            $hasSeasonPricing = $tour->pricings()
                ->where('pricing_mode', $pricingMode)
                ->where('season', $season)
                ->where('is_active', true)
                ->exists();
            
            $hasAllSeasonPricing = $tour->pricings()
                ->where('pricing_mode', $pricingMode)
                ->where('season', 'all')
                ->where('is_active', true)
                ->exists();
            
            if (!$hasAnyPricing) {
                throw new \Exception("Ce tour n'a aucun tarif configuré. Veuillez créer un tarif dans l'admin: /admin/tours/{$tour->id}/pricings");
            } elseif (!$hasModePricing) {
                throw new \Exception("Ce tour n'a pas de tarif configuré pour le mode '{$pricingMode}'. Veuillez créer un tarif en mode '{$pricingMode}' dans l'admin: /admin/tours/{$tour->id}/pricings");
            } elseif (!$hasSeasonPricing && !$hasAllSeasonPricing) {
                throw new \Exception("Ce tour n'a pas de tarif configuré pour la saison '{$seasonName}' en mode '{$pricingMode}'. Veuillez créer un tarif pour cette saison ou un tarif 'All Seasons' dans l'admin: /admin/tours/{$tour->id}/pricings");
            } else {
                throw new \Exception("Aucun tarif actif trouvé pour ce tour en mode {$pricingMode} pour la saison {$seasonName}. Veuillez vérifier que le tarif est bien activé dans l'admin.");
            }
        }

        // Calculate base price
        if ($pricingMode === 'group') {
            $baseCalculation = $this->calculateGroupPrice($pricing, $adults, $children, $infants);
            $totalPeople = $adults + $children + $infants;
        } else {
            // For private mode, adults contains the total people count
            $totalPeople = $adults;
            $baseCalculation = $this->calculatePrivatePrice($pricing, $totalPeople);
        }

        $basePrice = $baseCalculation['base_price'];

        // Calculate addons using TourPricing relationship (new method)
        $addonsCalculation = $this->calculateAddonsPriceForPricing($pricing, $selectedAddons, $totalPeople);

        // Calculate accommodation price if provided
        $accommodationCalculation = $this->calculateAccommodationPrice($pricing, $accommodationData);

        // Calculate total
        $totalPrice = $basePrice + $addonsCalculation['addons_total'] + $accommodationCalculation['total'];

        return [
            'tour_id' => $tour->id,
            'tour_title' => $tour->title,
            'pricing_mode' => $pricingMode,
            'pricing_id' => $pricing->id,
            'season' => $pricing->season,
            'date' => $date instanceof Carbon ? $date->format('Y-m-d') : $date,
            'base_price' => round($basePrice, 2),
            'base_breakdown' => $baseCalculation['breakdown'] ?? null,
            'addons' => $addonsCalculation['addons'],
            'addons_total' => round($addonsCalculation['addons_total'], 2),
            'accommodation_rooms' => $accommodationCalculation['details'] ?? [],
            'accommodation_total' => round($accommodationCalculation['total'], 2),
            'total_price' => round($totalPrice, 2),
            'currency' => 'EUR', // Can be made dynamic
        ];
    }

    /**
     * Calculate accommodation price for a pricing (supports multiple rooms)
     * 
     * @param TourPricing $pricing
     * @param array|null $accommodationData ['rooms' => [...], 'nights' => int]
     * @return array
     */
    public function calculateAccommodationPrice(TourPricing $pricing, ?array $accommodationData): array
    {
        if (!$accommodationData || empty($accommodationData['rooms']) || !is_array($accommodationData['rooms'])) {
            return [
                'total' => 0,
                'details' => [],
            ];
        }

        // Default nights (can be overridden per room)
        $defaultNights = $accommodationData['nights'] ?? 1;
        $totalPrice = 0;
        $roomsDetails = [];

        // Get room type names mapping
        $roomTypeNames = [
            'single' => 'Chambre Simple',
            'double' => 'Chambre Double',
            'twin' => 'Chambre Twin',
            'triple' => 'Chambre Triple',
        ];

        foreach ($accommodationData['rooms'] as $roomData) {
            if (empty($roomData['accommodation_id']) || empty($roomData['accommodation_room_id'])) {
                continue;
            }

            // Verify accommodation belongs to this pricing
            $accommodation = $pricing->accommodations()
                ->where('accommodations.id', $roomData['accommodation_id'])
                ->first();

            if (!$accommodation) {
                \Log::warning('Accommodation not found for pricing', [
                    'pricing_id' => $pricing->id,
                    'accommodation_id' => $roomData['accommodation_id'],
                ]);
                continue;
            }

            // Get the room
            $room = \App\Models\AccommodationRoom::where('id', $roomData['accommodation_room_id'])
                ->where('accommodation_id', $roomData['accommodation_id'])
                ->where('is_active', true)
                ->first();

            if (!$room) {
                \Log::warning('Room not found or inactive', [
                    'accommodation_id' => $roomData['accommodation_id'],
                    'room_id' => $roomData['accommodation_room_id'],
                ]);
                continue;
            }

            // Verify room type matches
            if ($room->room_type !== $roomData['room_type']) {
                \Log::warning('Room type mismatch', [
                    'expected' => $roomData['room_type'],
                    'actual' => $room->room_type,
                ]);
                continue;
            }

            // Use nights from roomData if provided, otherwise use the pivot value or default
            $nights = $roomData['nights'] ?? ($accommodation->pivot->nights ?? $defaultNights);
            
            $quantity = $roomData['quantity'] ?? 1;
            $roomPricePerNight = $room->price_per_night;
            $roomTotalPrice = $roomPricePerNight * $quantity * $nights;
            $totalPrice += $roomTotalPrice;

            $roomTypeName = $roomTypeNames[$room->room_type] ?? ucfirst($room->room_type);

            $roomsDetails[] = [
                'accommodation_id' => $accommodation->id,
                'accommodation_name' => $accommodation->name,
                'room_id' => $room->id,
                'room_type' => $room->room_type,
                'room_type_name' => $roomTypeName,
                'quantity' => $quantity,
                'price_per_night' => round($roomPricePerNight, 2),
                'nights' => $nights,
                'subtotal' => round($roomTotalPrice, 2),
                'room_notes' => $roomData['room_notes'] ?? null,
            ];
        }

        return [
            'total' => round($totalPrice, 2),
            'details' => $roomsDetails,
        ];
    }

    /**
     * Get available addons for a tour
     * 
     * @param Tour $tour
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableAddons(Tour $tour)
    {
        return $tour->activeAddons()->get();
    }

    /**
     * Validate pricing configuration for a tour
     * 
     * @param Tour $tour
     * @param string $pricingMode
     * @return array
     */
    public function validatePricingConfiguration(Tour $tour, string $pricingMode): array
    {
        $errors = [];
        $pricings = TourPricing::where('tour_id', $tour->id)
            ->where('pricing_mode', $pricingMode)
            ->where('is_active', true)
            ->get();

        if ($pricings->isEmpty()) {
            $errors[] = "No active {$pricingMode} pricing configured";
            return $errors;
        }

        foreach ($pricings as $pricing) {
            if ($pricingMode === 'group') {
                $adultPrice = $pricing->getAdultPrice();
                if ($adultPrice == 0) {
                    $errors[] = "Group pricing for season {$pricing->season} missing adult price";
                }
            } else {
                $privatePrices = $pricing->privatePrices;
                if ($privatePrices->isEmpty()) {
                    $errors[] = "Private pricing for season {$pricing->season} has no price tiers";
                }
            }
        }

        return $errors;
    }
}



