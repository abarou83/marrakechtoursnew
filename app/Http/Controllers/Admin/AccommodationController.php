<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\TourPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccommodationController extends Controller
{
    /**
     * Display a listing of accommodations
     */
    public function index()
    {
        $accommodations = Accommodation::with('rooms')->orderBy('name')->paginate(20);
        return view('admin.accommodations.index', compact('accommodations'));
    }

    /**
     * Show the form for creating a new accommodation
     */
    public function create()
    {
        return view('admin.accommodations.create');
    }

    /**
     * Store a newly created accommodation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:accommodations,slug',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'stars' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.location' => 'nullable|string|max:255',
            'rooms' => 'nullable|array',
            'rooms.*.room_type' => 'required|in:single,double,twin,triple',
            'rooms.*.price_per_night' => 'required|numeric|min:0',
            'rooms.*.max_occupancy' => 'required|integer|min:1',
            'rooms.*.description' => 'nullable|string',
            'rooms.*.is_active' => 'boolean',
        ]);

        $accommodation = Accommodation::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'address' => $validated['address'] ?? null,
            'stars' => $validated['stars'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Save translations if provided
        if (!empty($validated['translations'])) {
            foreach ($validated['translations'] as $translationData) {
                if (!empty($translationData['locale'])) {
                    $accommodation->translations()->updateOrCreate(
                        ['locale' => $translationData['locale']],
                        [
                            'name' => $translationData['name'] ?? $accommodation->name,
                            'description' => $translationData['description'] ?? null,
                            'location' => $translationData['location'] ?? null,
                        ]
                    );
                }
            }
        }

        // Create rooms if provided
        if (!empty($validated['rooms'])) {
            foreach ($validated['rooms'] as $roomData) {
                $accommodation->rooms()->create([
                    'room_type' => $roomData['room_type'],
                    'price_per_night' => $roomData['price_per_night'],
                    'max_occupancy' => $roomData['max_occupancy'],
                    'description' => $roomData['description'] ?? null,
                    'is_active' => isset($roomData['is_active']),
                ]);
            }
        }

        return redirect()->route('admin.accommodations.index')
            ->with('success', 'Hébergement créé avec succès.');
    }

    /**
     * Display the specified accommodation
     */
    public function show(Accommodation $accommodation)
    {
        $accommodation->load(['rooms', 'tourPricings']);
        return view('admin.accommodations.show', compact('accommodation'));
    }

    /**
     * Show the form for editing an accommodation
     */
    public function edit(Accommodation $accommodation)
    {
        $accommodation->load(['rooms', 'translations']);
        return view('admin.accommodations.edit', compact('accommodation'));
    }

    /**
     * Update the specified accommodation
     */
    public function update(Request $request, Accommodation $accommodation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:accommodations,slug,' . $accommodation->id,
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'stars' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.location' => 'nullable|string|max:255',
            'rooms' => 'nullable|array',
            'rooms.*.id' => 'nullable|exists:accommodation_rooms,id',
            'rooms.*.room_type' => 'required|in:single,double,twin,triple',
            'rooms.*.price_per_night' => 'required|numeric|min:0',
            'rooms.*.max_occupancy' => 'required|integer|min:1',
            'rooms.*.description' => 'nullable|string',
            'rooms.*.is_active' => 'boolean',
        ]);

        $accommodation->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $accommodation->slug,
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'address' => $validated['address'] ?? null,
            'stars' => $validated['stars'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Save translations if provided
        if (!empty($validated['translations'])) {
            foreach ($validated['translations'] as $translationData) {
                if (!empty($translationData['locale'])) {
                    $accommodation->translations()->updateOrCreate(
                        ['locale' => $translationData['locale']],
                        [
                            'name' => $translationData['name'] ?? $accommodation->name,
                            'description' => $translationData['description'] ?? null,
                            'location' => $translationData['location'] ?? null,
                        ]
                    );
                }
            }
        }

        // Update or create rooms
        if (!empty($validated['rooms'])) {
            $existingRoomIds = [];
            
            foreach ($validated['rooms'] as $roomData) {
                if (!empty($roomData['id'])) {
                    // Update existing room
                    $room = AccommodationRoom::find($roomData['id']);
                    if ($room && $room->accommodation_id == $accommodation->id) {
                        $room->update([
                            'room_type' => $roomData['room_type'],
                            'price_per_night' => $roomData['price_per_night'],
                            'max_occupancy' => $roomData['max_occupancy'],
                            'description' => $roomData['description'] ?? null,
                            'is_active' => isset($roomData['is_active']),
                        ]);
                        $existingRoomIds[] = $room->id;
                    }
                } else {
                    // Create new room
                    $newRoom = $accommodation->rooms()->create([
                        'room_type' => $roomData['room_type'],
                        'price_per_night' => $roomData['price_per_night'],
                        'max_occupancy' => $roomData['max_occupancy'],
                        'description' => $roomData['description'] ?? null,
                        'is_active' => isset($roomData['is_active']),
                    ]);
                    $existingRoomIds[] = $newRoom->id;
                }
            }
            
            // Delete rooms that were removed
            $accommodation->rooms()->whereNotIn('id', $existingRoomIds)->delete();
        } else {
            // If no rooms provided, delete all existing rooms
            $accommodation->rooms()->delete();
        }

        return redirect()->route('admin.accommodations.index')
            ->with('success', 'Hébergement modifié avec succès.');
    }

    /**
     * Remove the specified accommodation
     */
    public function destroy(Accommodation $accommodation)
    {
        // Check if accommodation is used in any tour pricings
        $pricingCount = $accommodation->tourPricings()->count();
        if ($pricingCount > 0) {
            return back()->with('error', "Impossible de supprimer l'hébergement. Il est attaché à {$pricingCount} formule(s).");
        }

        $accommodation->delete();

        return redirect()->route('admin.accommodations.index')
            ->with('success', 'Hébergement supprimé avec succès.');
    }

    /**
     * Manage accommodations for a specific tour pricing
     */
    public function managePricingAccommodations(TourPricing $tourPricing)
    {
        $tourPricing->load(['accommodations.rooms', 'tour']);
        $allAccommodations = Accommodation::active()->orderBy('name')->get();
        
        return view('admin.tour-pricings.manage-accommodations', compact('tourPricing', 'allAccommodations'));
    }

    /**
     * Attach accommodation to tour pricing
     */
    public function attachToPricing(Request $request, TourPricing $tourPricing)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'is_optional' => 'boolean',
            'nights' => 'required|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $tourPricing->accommodations()->syncWithoutDetaching([
            $request->accommodation_id => [
                'is_optional' => $request->has('is_optional'),
                'nights' => $request->nights ?? 1,
                'display_order' => $request->display_order ?? 0,
            ]
        ]);

        return back()->with('success', 'Hébergement attaché à la formule avec succès.');
    }

    /**
     * Detach accommodation from tour pricing
     */
    public function detachFromPricing(TourPricing $tourPricing, Accommodation $accommodation)
    {
        $tourPricing->accommodations()->detach($accommodation->id);

        return back()->with('success', 'Hébergement détaché de la formule avec succès.');
    }

    /**
     * Update accommodation order or settings in pricing
     */
    public function updatePricingAccommodation(Request $request, TourPricing $tourPricing, Accommodation $accommodation)
    {
        $request->validate([
            'is_optional' => 'boolean',
            'nights' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $tourPricing->accommodations()->updateExistingPivot($accommodation->id, [
            'is_optional' => $request->has('is_optional'),
            'nights' => $request->nights ?? 1,
            'display_order' => $request->display_order ?? 0,
        ]);

        return back()->with('success', 'Paramètres de l\'hébergement mis à jour.');
    }
}