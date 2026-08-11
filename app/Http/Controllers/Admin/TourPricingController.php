<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPricingRequest;
use App\Http\Requests\Admin\UpdateTourPricingRequest;
use App\Models\Accommodation;
use App\Models\Tour;
use App\Models\TourPricing;
use App\Models\TourGroupPrice;
use App\Models\TourPrivatePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TourPricingController extends Controller
{
    /**
     * Display a listing of pricings for a tour
     */
    public function index(Tour $tour)
    {
        $allPricings = $tour->pricings()
            ->with(['groupPrices', 'privatePrices'])
            ->orderBy('pricing_mode')
            ->orderBy('season')
            ->orderBy('id')
            ->get();

        // Separate pricings by mode
        $groupPricings = $allPricings->where('pricing_mode', 'group');
        $privatePricings = $allPricings->where('pricing_mode', 'private');

        return view('admin.tour-pricings.index', compact('tour', 'groupPricings', 'privatePricings'));
    }

    /**
     * Show the form for creating a new pricing
     */
    public function create(Tour $tour)
    {
        $tour->load('tourAddons.addon');
        $allAddons = \App\Models\Addon::active()->orderBy('name')->get();
        $allAccommodations = Accommodation::where('is_active', true)->orderBy('name')->get();
        return view('admin.tour-pricings.create', compact('tour', 'allAddons', 'allAccommodations'));
    }

    /**
     * Store a newly created pricing
     */
    public function store(StoreTourPricingRequest $request, Tour $tour)
    {
        DB::beginTransaction();
        try {
            $pricing = $tour->pricings()->create([
                'title' => $request->title,
                'pricing_mode' => $request->pricing_mode,
                'season' => $request->season,
                'is_active' => $request->has('is_active'),
            ]);

            // Create group prices if group mode
            if ($request->pricing_mode === 'group' && $request->has('group_prices')) {
                foreach ($request->group_prices as $groupPrice) {
                    $pricing->groupPrices()->create([
                        'category' => $groupPrice['category'],
                        'age_min' => $groupPrice['age_min'] ?? null,
                        'age_max' => $groupPrice['age_max'] ?? null,
                        'price' => $groupPrice['price'],
                    ]);
                }
            }

            // Create private prices if private mode
            if ($request->pricing_mode === 'private' && $request->has('private_prices')) {
                foreach ($request->private_prices as $privatePrice) {
                    $pricing->privatePrices()->create([
                        'min_people' => $privatePrice['min_people'],
                        'max_people' => $privatePrice['max_people'],
                        'price' => $privatePrice['price'],
                    ]);
                }
            }

            // Attach addons to pricing if provided
            $syncData = [];
            
            // Handle group addons
            if ($request->has('group_addons') && is_array($request->group_addons)) {
                foreach ($request->group_addons as $addonData) {
                    if (isset($addonData['addon_id']) && $addonData['addon_id']) {
                        $syncData[$addonData['addon_id']] = [
                            'is_required' => isset($addonData['is_required']) && $addonData['is_required'],
                            'is_included' => isset($addonData['is_included']) && $addonData['is_included'],
                            'override_price' => isset($addonData['override_price']) && $addonData['override_price'] !== '' ? $addonData['override_price'] : null,
                        ];
                    }
                }
            }
            
            // Handle private addons
            if ($request->has('private_addons') && is_array($request->private_addons)) {
                foreach ($request->private_addons as $addonData) {
                    if (isset($addonData['addon_id']) && $addonData['addon_id']) {
                        $syncData[$addonData['addon_id']] = [
                            'is_required' => isset($addonData['is_required']) && $addonData['is_required'],
                            'is_included' => isset($addonData['is_included']) && $addonData['is_included'],
                            'override_price' => isset($addonData['override_price']) && $addonData['override_price'] !== '' ? $addonData['override_price'] : null,
                        ];
                    }
                }
            }
            
            // Legacy support for addon_ids (backward compatibility)
            if ($request->has('addon_ids') && is_array($request->addon_ids)) {
                foreach ($request->addon_ids as $addonData) {
                    if (isset($addonData['addon_id']) && $addonData['addon_id']) {
                        $syncData[$addonData['addon_id']] = [
                            'is_required' => isset($addonData['is_required']) && $addonData['is_required'],
                            'is_included' => isset($addonData['is_included']) && $addonData['is_included'],
                            'override_price' => isset($addonData['override_price']) && $addonData['override_price'] !== '' ? $addonData['override_price'] : null,
                        ];
                    }
                }
            }
            
            if (!empty($syncData)) {
                $pricing->addons()->sync($syncData);
            }

            // Attach accommodations to pricing if provided
            $accommodationSyncData = [];
            
            // Handle accommodations
            if ($request->has('accommodations') && is_array($request->accommodations)) {
                foreach ($request->accommodations as $accommodationData) {
                    if (isset($accommodationData['accommodation_id']) && $accommodationData['accommodation_id']) {
                        $accommodationSyncData[$accommodationData['accommodation_id']] = [
                            'is_optional' => isset($accommodationData['is_optional']) && $accommodationData['is_optional'],
                            'nights' => isset($accommodationData['nights']) && $accommodationData['nights'] ? (int)$accommodationData['nights'] : 1,
                            'display_order' => isset($accommodationData['display_order']) ? (int)$accommodationData['display_order'] : 0,
                        ];
                    }
                }
            }
            
            if (!empty($accommodationSyncData)) {
                $pricing->accommodations()->sync($accommodationSyncData);
            }

            // Save translations
            if ($request->has('translations') && is_array($request->translations)) {
                foreach ($request->translations as $translationData) {
                    if (!empty($translationData['locale']) && !empty($translationData['title'])) {
                        $pricing->translations()->updateOrCreate(
                            ['locale' => $translationData['locale']],
                            ['title' => $translationData['title']]
                        );
                    }
                }
            }

            // Clear pricing cache for this tour
            $this->clearPricingCache($tour);

            DB::commit();

            return redirect()->to(route('admin.tours.edit', $tour) . '?tab=tarifs')
                ->with('success', 'Pricing created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating pricing: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a pricing
     * 
     * Loads the pricing with its prices and attached addons.
     * Also loads all available addons for selection.
     */
    public function edit(Tour $tour, TourPricing $pricing)
    {
        // Load pricing with all related data including addons, accommodations, and translations
        $pricing->load([
            'groupPrices', 
            'privatePrices', 
            'addons',
            'translations',
            'accommodations' => function($query) {
                $query->with(['activeRooms' => function($q) {
                    $q->where('is_active', true)->orderBy('room_type');
                }])->orderBy('pricing_accommodations.display_order');
            }
        ]);
        
        // Load all active addons for selection in the form
        $allAddons = \App\Models\Addon::active()->orderBy('name')->get();
        
        return view('admin.tour-pricings.edit', compact('tour', 'pricing', 'allAddons'));
    }

    /**
     * Update the specified pricing
     */
    public function update(UpdateTourPricingRequest $request, Tour $tour, TourPricing $pricing)
    {
        DB::beginTransaction();
        try {
            $pricing->update([
                'title' => $request->title,
                'pricing_mode' => $request->pricing_mode,
                'season' => $request->season,
                'is_active' => $request->has('is_active'),
            ]);

            // Delete existing prices
            $pricing->groupPrices()->delete();
            $pricing->privatePrices()->delete();

            // Create group prices if group mode
            if ($request->pricing_mode === 'group' && $request->has('group_prices')) {
                foreach ($request->group_prices as $groupPrice) {
                    $pricing->groupPrices()->create([
                        'category' => $groupPrice['category'],
                        'age_min' => $groupPrice['age_min'] ?? null,
                        'age_max' => $groupPrice['age_max'] ?? null,
                        'price' => $groupPrice['price'],
                    ]);
                }
            }

            // Create private prices if private mode
            if ($request->pricing_mode === 'private' && $request->has('private_prices')) {
                foreach ($request->private_prices as $privatePrice) {
                    $pricing->privatePrices()->create([
                        'min_people' => $privatePrice['min_people'],
                        'max_people' => $privatePrice['max_people'],
                        'price' => $privatePrice['price'],
                    ]);
                }
            }

            // Sync addons for pricing based on pricing mode
            // Each pricing mode has its own set of addons
            $syncData = [];
            
            // Handle addons based on pricing mode
            $addonKey = $request->pricing_mode === 'group' ? 'group_addons' : 'private_addons';
            
            if ($request->has($addonKey) && is_array($request->input($addonKey))) {
                foreach ($request->input($addonKey) as $addonData) {
                    if (isset($addonData['addon_id']) && $addonData['addon_id']) {
                        $syncData[$addonData['addon_id']] = [
                            'is_required' => isset($addonData['is_required']) && $addonData['is_required'],
                            'is_included' => isset($addonData['is_included']) && $addonData['is_included'],
                            'override_price' => isset($addonData['override_price']) && $addonData['override_price'] !== '' 
                                ? (float) $addonData['override_price'] 
                                : null,
                        ];
                    }
                }
            }
            
            // Legacy support for addon_ids (backward compatibility)
            if (empty($syncData) && $request->has('addon_ids') && is_array($request->addon_ids)) {
                foreach ($request->addon_ids as $addonData) {
                    if (isset($addonData['addon_id']) && $addonData['addon_id']) {
                        $syncData[$addonData['addon_id']] = [
                            'is_required' => isset($addonData['is_required']) && $addonData['is_required'],
                            'is_included' => isset($addonData['is_included']) && $addonData['is_included'],
                            'override_price' => isset($addonData['override_price']) && $addonData['override_price'] !== '' 
                                ? (float) $addonData['override_price'] 
                                : null,
                        ];
                    }
                }
            }
            
            // Sync addons (replaces existing attachments)
            $pricing->addons()->sync($syncData);

            // Save translations
            if ($request->has('translations') && is_array($request->translations)) {
                foreach ($request->translations as $translationData) {
                    if (!empty($translationData['locale']) && !empty($translationData['title'])) {
                        $pricing->translations()->updateOrCreate(
                            ['locale' => $translationData['locale']],
                            ['title' => $translationData['title']]
                        );
                    }
                }
            }

            // Clear pricing cache for this tour
            $this->clearPricingCache($tour);

            DB::commit();

            return redirect()->route('admin.tour-pricings.edit', [$tour, $pricing])
                ->with('success', 'Pricing updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating pricing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified pricing
     */
    public function destroy(Tour $tour, TourPricing $pricing)
    {
        DB::beginTransaction();
        try {
            $pricing->groupPrices()->delete();
            $pricing->privatePrices()->delete();
            $pricing->delete();

            // Clear pricing cache for this tour
            $this->clearPricingCache($tour);

            DB::commit();

            return redirect()->to(route('admin.tours.edit', $tour) . '?tab=tarifs')
                ->with('success', 'Pricing deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting pricing: ' . $e->getMessage());
        }
    }

    /**
     * Clear pricing cache for a tour
     * Clears all possible cache keys for all pricing modes and seasons
     */
    protected function clearPricingCache(Tour $tour)
    {
        $modes = ['group', 'private'];
        $seasons = ['low', 'normal', 'high', 'all'];
        
        foreach ($modes as $mode) {
            foreach ($seasons as $season) {
                $cacheKey = "tour_pricing_{$tour->id}_{$mode}_{$season}";
                Cache::forget($cacheKey);
            }
        }
    }
}
