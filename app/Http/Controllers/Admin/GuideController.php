<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideTranslation;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public function index()
    {
        $guides = Guide::with('translations')
            ->orderBy('position')
            ->orderByDesc('published_at')
            ->paginate(20);

        return view('admin.guides.index', compact('guides'));
    }

    public function create()
    {
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        $tours = Tour::active()->with('translations')->orderBy('title')->get();
        $categories = $this->categories();

        return view('admin.guides.create', compact('locales', 'tours', 'categories'));
    }

    public function store(Request $request)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:50'],
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'reading_time' => 'nullable|integer|min:1|max:120',
            'position' => 'nullable|integer|min:0',
            'featured_image' => 'nullable|image|max:4096',
            'tour_ids' => 'nullable|array',
            'tour_ids.*' => 'exists:tours,id',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.excerpt' => 'nullable|string|max:500',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        $this->validateUniqueSlugs($request);

        $featuredImage = $request->hasFile('featured_image')
            ? $request->file('featured_image')->store('guides', 'public')
            : null;

        $guide = Guide::create([
            'category' => $validated['category'],
            'is_published' => $request->boolean('is_published'),
            'published_at' => $validated['published_at'] ?? now(),
            'author_id' => auth('admin')->id(),
            'featured_image' => $featuredImage,
            'reading_time' => $validated['reading_time'] ?? 5,
            'position' => $validated['position'] ?? 0,
        ]);

        $this->syncTranslations($guide, $validated['translations']);
        $this->syncTours($guide, $request->input('tour_ids', []));

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide créé avec succès.');
    }

    public function edit(Guide $guide)
    {
        $guide->load(['translations', 'tours']);
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        $tours = Tour::active()->with('translations')->orderBy('title')->get();
        $categories = $this->categories();

        return view('admin.guides.edit', compact('guide', 'locales', 'tours', 'categories'));
    }

    public function update(Request $request, Guide $guide)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:50'],
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'reading_time' => 'nullable|integer|min:1|max:120',
            'position' => 'nullable|integer|min:0',
            'featured_image' => 'nullable|image|max:4096',
            'remove_featured_image' => 'boolean',
            'tour_ids' => 'nullable|array',
            'tour_ids.*' => 'exists:tours,id',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.excerpt' => 'nullable|string|max:500',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        $this->validateUniqueSlugs($request, $guide->id);

        $featuredImage = $guide->featured_image;

        if ($request->boolean('remove_featured_image') && $featuredImage) {
            Storage::disk('public')->delete($featuredImage);
            $featuredImage = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($featuredImage) {
                Storage::disk('public')->delete($featuredImage);
            }
            $featuredImage = $request->file('featured_image')->store('guides', 'public');
        }

        $guide->update([
            'category' => $validated['category'],
            'is_published' => $request->boolean('is_published'),
            'published_at' => $validated['published_at'] ?? $guide->published_at,
            'featured_image' => $featuredImage,
            'reading_time' => $validated['reading_time'] ?? $guide->reading_time,
            'position' => $validated['position'] ?? $guide->position,
        ]);

        $this->syncTranslations($guide, $validated['translations']);
        $this->syncTours($guide, $request->input('tour_ids', []));

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide mis à jour.');
    }

    public function destroy(Guide $guide)
    {
        if ($guide->featured_image) {
            Storage::disk('public')->delete($guide->featured_image);
        }

        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide supprimé.');
    }

    protected function syncTranslations(Guide $guide, array $translations): void
    {
        $guide->translations()->delete();

        foreach ($translations as $data) {
            $guide->translations()->create([
                'locale' => $data['locale'],
                'slug' => Str::slug($data['slug']),
                'title' => $data['title'],
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content'],
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
            ]);
        }
    }

    protected function syncTours(Guide $guide, array $tourIds): void
    {
        $sync = [];
        foreach (array_values($tourIds) as $index => $tourId) {
            $sync[$tourId] = ['position' => $index];
        }
        $guide->tours()->sync($sync);
    }

    protected function validateUniqueSlugs(Request $request, ?int $excludeGuideId = null): void
    {
        foreach ($request->input('translations', []) as $index => $data) {
            $slug = Str::slug($data['slug'] ?? '');
            $locale = $data['locale'] ?? '';

            $exists = GuideTranslation::where('locale', $locale)
                ->where('slug', $slug)
                ->when($excludeGuideId, fn ($q) => $q->where('guide_id', '!=', $excludeGuideId))
                ->exists();

            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "translations.{$index}.slug" => "Le slug « {$slug} » existe déjà pour la locale {$locale}.",
                ]);
            }
        }
    }

    protected function categories(): array
    {
        return [
            'marrakech' => 'Marrakech',
            'desert' => 'Désert',
            'culture' => 'Culture',
            'food' => 'Gastronomie',
            'transport' => 'Transport',
            'tips' => 'Conseils pratiques',
            'general' => 'Général',
        ];
    }
}
