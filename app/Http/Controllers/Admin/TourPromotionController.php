<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourPromotion;
use Illuminate\Http\Request;

class TourPromotionController extends Controller
{
    public function index(Tour $tour)
    {
        $promotions = $tour->promotions()->latest()->get();
        return view('admin.tour-promotions.index', compact('tour', 'promotions'));
    }

    public function create(Tour $tour)
    {
        return view('admin.tour-promotions.create', compact('tour'));
    }

    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $tour->promotions()->create($validated);

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=promotions')
            ->with('success', 'Promotion créée avec succès.');
    }

    public function edit(Tour $tour, TourPromotion $promotion)
    {
        return view('admin.tour-promotions.edit', compact('tour', 'promotion'));
    }

    public function update(Request $request, Tour $tour, TourPromotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $promotion->update($validated);

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=promotions')
            ->with('success', 'Promotion mise à jour avec succès.');
    }

    public function destroy(Tour $tour, TourPromotion $promotion)
    {
        $promotion->delete();

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=promotions')
            ->with('success', 'Promotion supprimée avec succès.');
    }
}




