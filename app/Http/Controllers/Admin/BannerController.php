<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerTranslation;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::with(['translations', 'images'])->orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.banners.create', compact('availableLocales', 'locales'));
    }

    public function store(Request $request)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $data = $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|max:4096',
            'primary_image_index' => 'nullable|integer|min:0',
            'link_url' => 'nullable|url|max:255',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'nullable|string|max:255',
        ]);

        // Garder image_path pour compatibilité avec l'ancienne structure
        $firstImagePath = $request->file('images')[0]->store('banners', 'public');

        $banner = Banner::create([
            'image_path' => $firstImagePath,
            'link_url' => $data['link_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'order' => $data['order'] ?? 0,
        ]);

        // Enregistrer toutes les images
        $primaryIndex = $data['primary_image_index'] ?? 0;
        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('banners', 'public');
            Image::create([
                'imageable_type' => Banner::class,
                'imageable_id' => $banner->id,
                'path' => $path,
                'is_primary' => $index == $primaryIndex,
            ]);
        }

        foreach ($data['translations'] as $translation) {
            BannerTranslation::create([
                'banner_id' => $banner->id,
                'locale' => $translation['locale'],
                'title' => $translation['title'],
                'slug' => !empty($translation['slug']) ? $translation['slug'] : null,
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Bannière créée.');
    }

    public function edit(Banner $banner)
    {
        $banner->load(['translations', 'images']);
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.banners.edit', compact('banner', 'availableLocales', 'locales'));
    }

    public function update(Request $request, Banner $banner)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        
        $data = $request->validate([
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|max:4096',
            'primary_image_index' => 'nullable|integer|min:0',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|integer|exists:images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'nullable|integer|exists:images,id',
            'link_url' => 'nullable|url|max:255',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'nullable|string|max:255',
        ]);

        $update = [
            'link_url' => $data['link_url'] ?? null,
            'is_active' => $request->boolean('is_active', false),
            'order' => $data['order'] ?? 0,
        ];

        // Supprimer les images demandées
        if ($request->has('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $image = Image::find($imageId);
                if ($image && $image->imageable_id == $banner->id && $image->imageable_type == Banner::class) {
                    if (Storage::disk('public')->exists($image->path)) {
                        Storage::disk('public')->delete($image->path);
                    }
                    $image->delete();
                }
            }
        }

        // Ajouter de nouvelles images
        if ($request->hasFile('images')) {
            $existingImagesCount = $banner->images()->count();
            
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('banners', 'public');
                // Par défaut, la première nouvelle image n'est principale que s'il n'y a pas d'images existantes
                $isPrimary = false;
                
                Image::create([
                    'imageable_type' => Banner::class,
                    'imageable_id' => $banner->id,
                    'path' => $path,
                    'is_primary' => $isPrimary,
                ]);
            }
        }

        // Mettre à jour l'image principale si spécifiée (doit être fait après suppression et ajout)
        if ($request->has('primary_image_index') && $request->primary_image_index !== null) {
            $primaryIndex = (int)$request->input('primary_image_index');
            $banner->images()->update(['is_primary' => false]);
            $images = $banner->images()->orderBy('is_primary', 'desc')->orderBy('id')->get();
            if (isset($images[$primaryIndex])) {
                $images[$primaryIndex]->update(['is_primary' => true]);
            }
        }

        // Mettre à jour image_path pour compatibilité (première image principale ou première image)
        $primaryImage = $banner->primaryImage;
        if ($primaryImage) {
            $update['image_path'] = $primaryImage->path;
        } elseif ($banner->images()->count() > 0) {
            $update['image_path'] = $banner->images()->first()->path;
        }

        $banner->update($update);

        // Mettre à jour les traductions
        foreach ($data['translations'] as $translation) {
            BannerTranslation::updateOrCreate(
                [
                    'banner_id' => $banner->id,
                    'locale' => $translation['locale'],
                ],
                [
                    'title' => $translation['title'],
                    'slug' => !empty($translation['slug']) ? $translation['slug'] : null,
                ]
            );
        }

        return redirect()->route('admin.banners.index')->with('success', 'Bannière mise à jour.');
    }

    public function destroy(Banner $banner)
    {
        // Supprimer toutes les images
        foreach ($banner->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }
        $banner->images()->delete();
        
        // Supprimer l'ancienne image si elle existe
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        
        $banner->delete();
        return back()->with('success', 'Bannière supprimée.');
    }

    public function deleteImage(Image $image)
    {
        if ($image->imageable_type == Banner::class) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
            $image->delete();
            return back()->with('success', 'Image supprimée.');
        }
        return back()->with('error', 'Image non trouvée.');
    }
}
