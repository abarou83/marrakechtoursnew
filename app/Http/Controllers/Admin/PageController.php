<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Afficher la liste des pages
     */
    public function index()
    {
        $pages = Page::ordered()->with('translations')->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.pages.create', compact('availableLocales', 'locales'));
    }

    /**
     * Créer une nouvelle page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'translations.*.locale' => 'required|in:' . implode(',', \App\Models\Language::active()->pluck('code')->toArray()),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
            'translations.*.meta_keywords' => 'nullable|string|max:500',
        ]);

        $page = Page::create([
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($validated['translations'] as $translation) {
            PageTranslation::create([
                'page_id' => $page->id,
                'locale' => $translation['locale'],
                'slug' => Str::slug($translation['slug']),
                'title' => $translation['title'],
                'content' => $translation['content'],
                'meta_title' => $translation['meta_title'] ?? null,
                'meta_description' => $translation['meta_description'] ?? null,
                'meta_keywords' => $translation['meta_keywords'] ?? null,
            ]);
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page ajoutée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Page $page)
    {
        $page->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.pages.edit', compact('page', 'availableLocales', 'locales'));
    }

    /**
     * Mettre à jour une page
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'translations.*.locale' => 'required|in:' . implode(',', \App\Models\Language::active()->pluck('code')->toArray()),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
            'translations.*.meta_keywords' => 'nullable|string|max:500',
        ]);

        $page->update([
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($validated['translations'] as $translation) {
            PageTranslation::updateOrCreate(
                [
                    'page_id' => $page->id,
                    'locale' => $translation['locale'],
                ],
                [
                    'slug' => Str::slug($translation['slug']),
                    'title' => $translation['title'],
                    'content' => $translation['content'],
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'meta_keywords' => $translation['meta_keywords'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page mise à jour avec succès.');
    }

    /**
     * Supprimer une page
     */
    public function destroy(Page $page)
    {
        $page->delete();
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Page supprimée avec succès.');
    }
}

