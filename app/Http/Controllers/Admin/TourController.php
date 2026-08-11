<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;
use App\Models\Image;
use App\Models\TourDate;
use App\Models\TourTranslation;
use App\Models\Language;
use App\Models\Addon;
use App\Services\TourJsonImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    public function importExample(TourJsonImportService $importService)
    {
        $filePath = base_path('database/data/example-tour-pack.multilingual.json');

        if (!is_file($filePath)) {
            return back()->withErrors([
                'import' => 'Fichier exemple introuvable: database/data/example-tour-pack.multilingual.json',
            ]);
        }

        try {
            $jsonContent = file_get_contents($filePath);
            $payload = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            $result = $importService->import($payload);

            return back()->with('success', "Import exemple terminé. {$result['tours']} tour(s), {$result['categories']} catégorie(s), {$result['addons']} add-on(s), {$result['accommodations']} hébergement(s).");
        } catch (\Throwable $e) {
            \Log::error('Tour import example error: ' . $e->getMessage());
            return back()->withErrors([
                'import' => "Erreur lors de l'import exemple: {$e->getMessage()}",
            ]);
        }
    }

    public function importJson(Request $request, TourJsonImportService $importService)
    {
        $validated = $request->validate([
            'json_file' => 'required|file|mimes:json,txt|max:4096',
        ]);

        try {
            $jsonContent = file_get_contents($validated['json_file']->getRealPath());
            $payload = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            $result = $importService->import($payload);

            return back()->with('success', "Import JSON terminé. {$result['tours']} tour(s), {$result['categories']} catégorie(s), {$result['addons']} add-on(s), {$result['accommodations']} hébergement(s).");
        } catch (\Throwable $e) {
            \Log::error('Tour import json error: ' . $e->getMessage());
            return back()->withErrors([
                'import' => "Erreur lors de l'import JSON: {$e->getMessage()}",
            ]);
        }
    }

    public function index()
    {
        $tours = Tour::with(['categories', 'category', 'images'])->latest()->paginate(15);
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        $categories = Category::all();
        $availableLocales = Language::active()->pluck('code')->toArray();
        return view('admin.tours.create', compact('categories', 'availableLocales'));
    }

    public function store(Request $request)
    {
        $activeLocales = Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'category_id' => 'nullable|exists:categories,id', // Keep for backward compatibility
            'title' => 'required|string|max:255', // Pour le slug
            'slug' => 'nullable|string|max:255|unique:tours',
            'type' => 'required|in:daytrip,activity,excursion,circuit',
            'is_active' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'canonical_url' => 'nullable|url|max:255',
            'og_image' => 'nullable|url|max:255',
            'status' => 'required|in:draft,published',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.itinerary' => 'nullable|string',
            'translations.*.location' => 'required|string|max:255',
            'translations.*.duration' => 'required|string|max:255',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string|max:255',
            'translations.*.focus_keyword' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Créer le tour avec les données de base (sans les traductions et catégories)
            $tourData = $validated;
            unset($tourData['translations'], $tourData['category_ids']);
            // Keep category_id for backward compatibility (first category)
            if (isset($validated['category_ids'][0])) {
                $tourData['category_id'] = $validated['category_ids'][0];
            }
            // Handle is_active checkbox
            $tourData['is_active'] = $request->has('is_active');
            $tour = Tour::create($tourData);

            // Attach categories (many-to-many)
            if (isset($validated['category_ids'])) {
                $tour->categories()->sync($validated['category_ids']);
            }

            // Créer les traductions
            if (isset($validated['translations'])) {
                foreach ($validated['translations'] as $translationData) {
                    TourTranslation::create([
                        'tour_id' => $tour->id,
                        'locale' => $translationData['locale'],
                        'title' => $translationData['title'],
                        'description' => $translationData['description'],
                        'itinerary' => $translationData['itinerary'] ?? null,
                        'location' => $translationData['location'],
                        'duration' => $translationData['duration'],
                        'meta_title' => $translationData['meta_title'] ?? null,
                        'meta_description' => $translationData['meta_description'] ?? null,
                        'meta_keywords' => $translationData['meta_keywords'] ?? null,
                        'focus_keyword' => $translationData['focus_keyword'] ?? null,
                    ]);
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                $isFirstImage = true;
                foreach ($request->file('images') as $file) {
                    $path = $file->store('tours', 'public');
                    
                    Image::create([
                        'imageable_type' => Tour::class,
                        'imageable_id' => $tour->id,
                        'path' => $path,
                        'alt' => $tour->title,
                        'is_primary' => $isFirstImage, // Première image = principale
                    ]);
                    
                    $isFirstImage = false;
                }
            }

            DB::commit();

            return redirect()->route('admin.tours.index')
                ->with('success', 'Tour créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Tour creation error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création du tour: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $tour = Tour::with(['categories', 'category', 'images', 'tourDates', 'bookings'])->findOrFail($id);
        return view('admin.tours.show', compact('tour'));
    }

    public function edit(string $id)
    {
        $tour = Tour::with(['images', 'translations', 'categories'])->findOrFail($id);
        $categories = Category::all();
        $availableLocales = Language::active()->pluck('code')->toArray();
        $translations = $tour->translations->keyBy('locale');
        $selectedCategoryIds = $tour->categories->pluck('id')->toArray();

        // Données pour les onglets (dates, tarifs, add-ons, promotions)
        $tourDates = $tour->tourDates()->latest()->paginate(20, ['*'], 'dates_page');
        $allPricings = $tour->pricings()->with(['groupPrices', 'privatePrices'])->orderBy('pricing_mode')->orderBy('season')->orderBy('id')->get();
        $groupPricings = $allPricings->where('pricing_mode', 'group');
        $privatePricings = $allPricings->where('pricing_mode', 'private');
        $tour->load('tourAddons.addon');
        $allAddons = Addon::active()->orderBy('name')->get();
        $promotions = $tour->promotions()->latest()->get();

        return view('admin.tours.edit', compact(
            'tour', 'categories', 'availableLocales', 'translations', 'selectedCategoryIds',
            'tourDates', 'groupPricings', 'privatePricings', 'allAddons', 'promotions'
        ));
    }

    public function update(Request $request, string $id)
    {
        $tour = Tour::findOrFail($id);
        
        $activeLocales = Language::active()->pluck('code')->toArray();
        
        $validated = $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'category_id' => 'nullable|exists:categories,id', // Keep for backward compatibility
            'title' => 'required|string|max:255', // Pour le slug
            'slug' => 'nullable|string|max:255|unique:tours,slug,' . $id,
            'type' => 'required|in:daytrip,activity,excursion,circuit',
            'is_active' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'canonical_url' => 'nullable|url|max:255',
            'og_image' => 'nullable|url|max:255',
            'status' => 'required|in:draft,published',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.itinerary' => 'nullable|string',
            'translations.*.location' => 'required|string|max:255',
            'translations.*.duration' => 'required|string|max:255',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string|max:255',
            'translations.*.focus_keyword' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour le tour avec les données de base (sans les traductions et catégories)
            $tourData = $validated;
            unset($tourData['translations'], $tourData['category_ids']);
            // Keep category_id for backward compatibility (first category)
            if (isset($validated['category_ids'][0])) {
                $tourData['category_id'] = $validated['category_ids'][0];
            }
            // Handle is_active checkbox
            $tourData['is_active'] = $request->has('is_active');
            $tour->update($tourData);

            // Sync categories (many-to-many)
            if (isset($validated['category_ids'])) {
                $tour->categories()->sync($validated['category_ids']);
            }

            // Mettre à jour ou créer les traductions
            if (isset($validated['translations'])) {
                foreach ($validated['translations'] as $translationData) {
                    TourTranslation::updateOrCreate(
                        [
                            'tour_id' => $tour->id,
                            'locale' => $translationData['locale'],
                        ],
                        [
                            'title' => $translationData['title'],
                            'description' => $translationData['description'],
                            'itinerary' => $translationData['itinerary'] ?? null,
                            'location' => $translationData['location'],
                            'duration' => $translationData['duration'],
                            'meta_title' => $translationData['meta_title'] ?? null,
                            'meta_description' => $translationData['meta_description'] ?? null,
                            'meta_keywords' => $translationData['meta_keywords'] ?? null,
                            'focus_keyword' => $translationData['focus_keyword'] ?? null,
                        ]
                    );
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $hasPrimaryImage = $tour->images()->where('is_primary', true)->exists();
                
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('tours', 'public');
                    
                    Image::create([
                        'imageable_type' => Tour::class,
                        'imageable_id' => $tour->id,
                        'path' => $path,
                        'alt' => $tour->title,
                        'is_primary' => !$hasPrimaryImage && $index === 0,
                    ]);
                }
            }

            DB::commit();

            $message = 'Tour mis à jour avec succès.';
            if ($request->hasFile('images')) {
                $count = count($request->file('images'));
                $message .= " {$count} image(s) ajoutée(s).";
            }

            return redirect()->route('admin.tours.edit', $tour)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Tour update error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $tour = Tour::with('images')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete images from storage
            foreach ($tour->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            $tour->delete();

            DB::commit();

            return redirect()->route('admin.tours.index')
                ->with('success', 'Tour supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    // Delete single image
    public function deleteImage(Image $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image supprimée avec succès.');
    }

    // Set primary image
    public function setPrimaryImage(Image $image)
    {
        DB::beginTransaction();
        try {
            // Reset all images of this tour
            Image::where('imageable_type', Tour::class)
                ->where('imageable_id', $image->imageable_id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            DB::commit();

            return back()->with('success', 'Image principale définie avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }
}
