<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;
use App\Models\Addon;
use App\Models\Accommodation;
use App\Models\TourPricing;
use App\Models\TourTranslation;
use App\Models\CategoryTranslation;
use App\Models\AddonTranslation;
use App\Models\AccommodationTranslation;
use App\Models\TourPricingTranslation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * Gérer les traductions d'un tour
     */
    public function editTour(Tour $tour)
    {
        $tour->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        return view('admin.translations.tour', compact('tour', 'availableLocales'));
    }

    /**
     * Sauvegarder les traductions d'un tour
     */
    public function updateTour(Request $request, Tour $tour)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.itinerary' => 'nullable|string',
            'translations.*.location' => 'nullable|string|max:255',
            'translations.*.duration' => 'nullable|string|max:255',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string|max:255',
            'translations.*.focus_keyword' => 'nullable|string|max:100',
            'translations.*.canonical_url' => 'nullable|url|max:255',
            'translations.*.og_image' => 'nullable|url|max:255',
        ]);

        foreach ($validated['translations'] as $translation) {
            TourTranslation::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'locale' => $translation['locale'],
                ],
                $translation
            );
        }

        return redirect()->route('admin.tours.index')
            ->with('success', 'Traductions mises à jour avec succès.');
    }

    /**
     * Gérer les traductions d'une catégorie
     */
    public function editCategory(Category $category)
    {
        $category->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        return view('admin.translations.category', compact('category', 'availableLocales'));
    }

    /**
     * Sauvegarder les traductions d'une catégorie
     */
    public function updateCategory(Request $request, Category $category)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.name' => 'required|string|max:255',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string|max:255',
            'translations.*.focus_keyword' => 'nullable|string|max:100',
            'translations.*.canonical_url' => 'nullable|url|max:255',
            'translations.*.og_image' => 'nullable|url|max:255',
        ]);

        foreach ($validated['translations'] as $translation) {
            CategoryTranslation::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'locale' => $translation['locale'],
                ],
                $translation
            );
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Traductions mises à jour avec succès.');
    }

    /**
     * Gérer les traductions d'un addon
     */
    public function editAddon(Addon $addon)
    {
        $addon->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        return view('admin.translations.addon', compact('addon', 'availableLocales'));
    }

    /**
     * Sauvegarder les traductions d'un addon
     */
    public function updateAddon(Request $request, Addon $addon)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.name' => 'required|string|max:255',
        ]);

        foreach ($validated['translations'] as $translation) {
            AddonTranslation::updateOrCreate(
                [
                    'addon_id' => $addon->id,
                    'locale' => $translation['locale'],
                ],
                $translation
            );
        }

        return redirect()->route('admin.addons.index')
            ->with('success', 'Traductions mises à jour avec succès.');
    }

    /**
     * Gérer les traductions d'un hébergement
     */
    public function editAccommodation(Accommodation $accommodation)
    {
        $accommodation->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        return view('admin.translations.accommodation', compact('accommodation', 'availableLocales'));
    }

    /**
     * Sauvegarder les traductions d'un hébergement
     */
    public function updateAccommodation(Request $request, Accommodation $accommodation)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.location' => 'nullable|string|max:255',
        ]);

        foreach ($validated['translations'] as $translation) {
            AccommodationTranslation::updateOrCreate(
                [
                    'accommodation_id' => $accommodation->id,
                    'locale' => $translation['locale'],
                ],
                $translation
            );
        }

        return redirect()->route('admin.accommodations.index')
            ->with('success', 'Traductions mises à jour avec succès.');
    }

    /**
     * Gérer les traductions d'un pricing
     */
    public function editTourPricing(TourPricing $pricing)
    {
        $pricing->load(['translations', 'tour']);
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        return view('admin.translations.tour-pricing', compact('pricing', 'availableLocales'));
    }

    /**
     * Sauvegarder les traductions d'un pricing
     */
    public function updateTourPricing(Request $request, TourPricing $pricing)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
        ]);

        foreach ($validated['translations'] as $translation) {
            TourPricingTranslation::updateOrCreate(
                [
                    'tour_pricing_id' => $pricing->id,
                    'locale' => $translation['locale'],
                ],
                $translation
            );
        }

        return redirect()->route('admin.tour-pricings.index', $pricing->tour)
            ->with('success', 'Traductions du pricing mises à jour avec succès.');
    }
}

