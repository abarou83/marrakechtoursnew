<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureBlock;
use App\Models\FeatureBlockTranslation;
use App\Models\FeatureBlocksSectionTranslation;
use App\Models\FeatureBlocksSectionSetting;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeatureBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $featureBlocks = FeatureBlock::ordered()->with('translations')->get();
        $availableLocales = Language::active()->pluck('code')->toArray();
        $sectionTranslations = FeatureBlocksSectionTranslation::whereIn('locale', $availableLocales)
            ->get()
            ->keyBy('locale');
        $sectionSettings = FeatureBlocksSectionSetting::getSettings();
        
        return view('admin.feature-blocks.index', compact('featureBlocks', 'availableLocales', 'sectionTranslations', 'sectionSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableLocales = Language::active()->pluck('code')->toArray();
        return view('admin.feature-blocks.create', compact('availableLocales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $activeLocales = Language::active()->pluck('code')->toArray();
        
        $data = $request->validate([
            'icon' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:svg,png,jpg,jpeg', 'max:2048'],
            'icon_background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon_background_color_enabled' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'translations.*.locale' => ['required', 'in:' . implode(',', $activeLocales)],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ]);

        $data['order'] = $data['order'] ?? (FeatureBlock::max('order') ?? 0) + 1;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['icon_background_color_enabled'] = $request->boolean('icon_background_color_enabled', false);
        
        // Handle icon background color - set to null if disabled
        if (!$data['icon_background_color_enabled']) {
            $data['icon_background_color'] = null;
        } elseif ($request->has('icon_background_color') && !empty($request->icon_background_color)) {
            $data['icon_background_color'] = $request->icon_background_color;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('feature-blocks', 'public');
        }
        
        // Remove translations and image from main data
        $translations = $data['translations'] ?? [];
        unset($data['translations'], $data['image']);

        $featureBlock = FeatureBlock::create($data);
        
        // Create translations
        foreach ($translations as $translation) {
            FeatureBlockTranslation::create([
                'feature_block_id' => $featureBlock->id,
                'locale' => $translation['locale'],
                'title' => $translation['title'],
                'description' => $translation['description'],
            ]);
        }

        return redirect()->route('admin.feature-blocks.index')->with('success', 'Bloc créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeatureBlock $featureBlock)
    {
        $featureBlock->load('translations');
        $availableLocales = Language::active()->pluck('code')->toArray();
        return view('admin.feature-blocks.edit', compact('featureBlock', 'availableLocales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeatureBlock $featureBlock)
    {
        $activeLocales = Language::active()->pluck('code')->toArray();
        
        $data = $request->validate([
            'icon' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:svg,png,jpg,jpeg', 'max:2048'],
            'icon_background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon_background_color_enabled' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'translations.*.locale' => ['required', 'in:' . implode(',', $activeLocales)],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['icon_background_color_enabled'] = $request->boolean('icon_background_color_enabled', false);
        
        // Handle icon background color - set to null if disabled
        if (!$data['icon_background_color_enabled']) {
            $data['icon_background_color'] = null;
        } elseif ($request->has('icon_background_color') && !empty($request->icon_background_color)) {
            $data['icon_background_color'] = $request->icon_background_color;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($featureBlock->image_path && Storage::disk('public')->exists($featureBlock->image_path)) {
                Storage::disk('public')->delete($featureBlock->image_path);
            }
            $data['image_path'] = $request->file('image')->store('feature-blocks', 'public');
        }
        
        // Sync color inputs (use text input if provided, otherwise use color input)
        if ($request->has('container_background_color_text') && !empty($request->container_background_color_text)) {
            $data['container_background_color'] = $request->container_background_color_text;
        } elseif ($request->has('container_background_color') && !empty($request->container_background_color)) {
            $data['container_background_color'] = $request->container_background_color;
        }
        
        // Remove translations and image from main data
        $translations = $data['translations'] ?? [];
        unset($data['translations'], $data['image']);

        $featureBlock->update($data);
        
        // Update or create translations
        foreach ($translations as $translation) {
            FeatureBlockTranslation::updateOrCreate(
                [
                    'feature_block_id' => $featureBlock->id,
                    'locale' => $translation['locale'],
                ],
                [
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                ]
            );
        }

        return redirect()->route('admin.feature-blocks.index')->with('success', 'Bloc mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeatureBlock $featureBlock)
    {
        // Delete image if exists
        if ($featureBlock->image_path && Storage::disk('public')->exists($featureBlock->image_path)) {
            Storage::disk('public')->delete($featureBlock->image_path);
        }
        
        $featureBlock->delete();
        return back()->with('success', 'Bloc supprimé avec succès.');
    }

    /**
     * Update section settings (title, description, badge)
     */
    public function updateSectionSettings(Request $request)
    {
        $activeLocales = Language::active()->pluck('code')->toArray();
        
        $data = $request->validate([
            'translations.*.locale' => ['required', 'in:' . implode(',', $activeLocales)],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'container_background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        foreach ($data['translations'] ?? [] as $translation) {
            FeatureBlocksSectionTranslation::updateOrCreate(
                ['locale' => $translation['locale']],
                [
                    'title' => $translation['title'] ?? '',
                    'description' => $translation['description'] ?? '',
                ]
            );
        }

        // Update section settings (container background color)
        $settings = FeatureBlocksSectionSetting::getSettings();
        if (isset($data['container_background_color'])) {
            $settings->update([
                'container_background_color' => $data['container_background_color'],
            ]);
        }

        return back()->with('success', 'Paramètres de la section mis à jour.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(FeatureBlock $featureBlock)
    {
        $featureBlock->update(['is_active' => !$featureBlock->is_active]);
        return back()->with('success', 'Statut du bloc mis à jour.');
    }
}
