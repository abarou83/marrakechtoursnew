<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAddonRequest;
use App\Http\Requests\Admin\UpdateAddonRequest;
use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * Display a listing of addons
     */
    public function index()
    {
        $addons = Addon::orderBy('name')->paginate(20);
        return view('admin.addons.index', compact('addons'));
    }

    /**
     * Display the specified addon
     */
    public function show(Addon $addon)
    {
        $addon->load('tours');
        return view('admin.addons.show', compact('addon'));
    }

    /**
     * Show the form for creating a new addon
     */
    public function create()
    {
        return view('admin.addons.create');
    }

    /**
     * Store a newly created addon
     */
    public function store(StoreAddonRequest $request)
    {
        $addon = Addon::create([
            'name' => $request->name,
            'slug' => $request->slug ?? null,
            'pricing_type' => $request->pricing_type,
            'base_price' => $request->pricing_type === 'free' ? 0 : $request->base_price,
            'is_active' => $request->has('is_active'),
        ]);

        // Save translations if provided
        if ($request->has('translations') && is_array($request->translations)) {
            foreach ($request->translations as $translationData) {
                if (!empty($translationData['locale']) && !empty($translationData['name'])) {
                    $addon->translations()->updateOrCreate(
                        ['locale' => $translationData['locale']],
                        ['name' => $translationData['name']]
                    );
                }
            }
        }

        // Save price tiers if provided (for per_person addons with tiered pricing)
        if ($request->has('price_tiers') && is_array($request->price_tiers)) {
            foreach ($request->price_tiers as $tier) {
                if (!empty($tier['min_people']) && !empty($tier['max_people']) && !empty($tier['price'])) {
                    $addon->priceTiers()->create([
                        'min_people' => $tier['min_people'],
                        'max_people' => $tier['max_people'],
                        'price' => $tier['price'],
                    ]);
                }
            }
        }

        // If redirect URL is provided and tour_id is present, attach addon to tour
        if ($request->has('redirect') && $request->has('tour_id')) {
            $tour = Tour::find($request->tour_id);
            if ($tour) {
                $tour->tourAddons()->updateOrCreate(
                    ['addon_id' => $addon->id],
                    [
                        'is_required' => $request->has('is_required'),
                        'override_price' => $request->override_price,
                    ]
                );
            }
            return redirect($request->redirect)->with('success', 'Addon created and attached to tour successfully.');
        }

        // If only redirect URL is provided, redirect there
        if ($request->has('redirect')) {
            return redirect($request->redirect)->with('success', 'Addon created successfully.');
        }

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon created successfully.');
    }

    /**
     * Show the form for editing an addon
     */
    public function edit(Addon $addon)
    {
        $addon->load('priceTiers', 'translations');
        return view('admin.addons.edit', compact('addon'));
    }

    /**
     * Update the specified addon
     */
    public function update(UpdateAddonRequest $request, Addon $addon)
    {
        $addon->update([
            'name' => $request->name,
            'slug' => $request->slug ?? $addon->slug,
            'pricing_type' => $request->pricing_type,
            'base_price' => $request->pricing_type === 'free' ? 0 : $request->base_price,
            'is_active' => $request->has('is_active'),
        ]);

        // Delete existing price tiers
        $addon->priceTiers()->delete();

        // Save new price tiers if provided (for per_person addons with tiered pricing)
        if ($request->has('price_tiers') && is_array($request->price_tiers)) {
            foreach ($request->price_tiers as $tier) {
                if (!empty($tier['min_people']) && !empty($tier['max_people']) && !empty($tier['price'])) {
                    $addon->priceTiers()->create([
                        'min_people' => $tier['min_people'],
                        'max_people' => $tier['max_people'],
                        'price' => $tier['price'],
                    ]);
                }
            }
        }

        // Save translations if provided
        if ($request->has('translations') && is_array($request->translations)) {
            foreach ($request->translations as $translationData) {
                if (!empty($translationData['locale']) && !empty($translationData['name'])) {
                    $addon->translations()->updateOrCreate(
                        ['locale' => $translationData['locale']],
                        ['name' => $translationData['name']]
                    );
                }
            }
        }

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon updated successfully.');
    }

    /**
     * Remove the specified addon
     */
    public function destroy(Addon $addon)
    {
        // Check if addon is used in any tours
        $tourCount = $addon->tours()->count();
        if ($tourCount > 0) {
            return back()->with('error', "Cannot delete addon. It is attached to {$tourCount} tour(s).");
        }

        $addon->delete();

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon deleted successfully.');
    }

    /**
     * Manage addons for a specific tour
     */
    public function manageTourAddons(Tour $tour)
    {
        $tour->load('tourAddons.addon');
        $allAddons = Addon::active()->orderBy('name')->get();
        
        return view('admin.tours.manage-addons', compact('tour', 'allAddons'));
    }

    /**
     * Attach addon to tour
     */
    public function attachToTour(Request $request, Tour $tour)
    {
        $request->validate([
            'addon_id' => 'required|exists:addons,id',
            'is_required' => 'boolean',
            'override_price' => 'nullable|numeric|min:0',
        ]);

        $tour->tourAddons()->updateOrCreate(
            ['addon_id' => $request->addon_id],
            [
                'is_required' => $request->has('is_required'),
                'override_price' => $request->override_price,
            ]
        );

        // Redirect to custom URL if provided, otherwise redirect to tour edit with addons tab
        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Addon attached to tour successfully.');
        }

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=addons')
            ->with('success', 'Addon attached to tour successfully.');
    }

    /**
     * Detach addon from tour
     */
    public function detachFromTour(Tour $tour, Addon $addon)
    {
        $tour->tourAddons()->where('addon_id', $addon->id)->delete();

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=addons')
            ->with('success', 'Addon detached from tour successfully.');
    }
}



