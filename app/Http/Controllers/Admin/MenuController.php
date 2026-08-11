<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Models\MenuTranslation;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::withCount('allItems as all_items_count')->orderBy('position')->get();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('translations')->get();
        $pages = \App\Models\Page::with('translations')->active()->ordered()->get();
        $tours = Tour::where('status', 'published')->with('translations')->orderBy('title')->get();
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.menus.create', compact('categories', 'pages', 'tours', 'availableLocales', 'locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $validated = $this->validateMenuRequest($request, $activeLocales);

        DB::beginTransaction();
        try {
            $menu = Menu::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? null,
                'location' => $validated['location'],
                'is_active' => $request->has('is_active'),
                'position' => $validated['position'] ?? 0,
            ]);

            $this->syncMenuTranslations($menu, $validated['menu_translations'] ?? []);

            if (isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $itemData) {
                    $this->persistMenuItem($menu, $itemData, $index, $activeLocales);
                }
            }

            DB::commit();

            return redirect()->route('admin.menus.edit', $menu)
                ->with('success', 'Menu créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menu = Menu::with(['allItems.category', 'allItems.page', 'allItems.tour'])->findOrFail($id);
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $menu = Menu::with(['allItems.category', 'allItems.page', 'allItems.tour', 'translations', 'allItems.translations'])->findOrFail($id);
        $categories = Category::with('translations')->get();
        $pages = \App\Models\Page::with('translations')->active()->ordered()->get();
        $tours = Tour::where('status', 'published')->with('translations')->orderBy('title')->get();
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        return view('admin.menus.edit', compact('menu', 'categories', 'pages', 'tours', 'availableLocales', 'locales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $menu = Menu::findOrFail($id);
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $validated = $this->validateMenuRequest($request, $activeLocales, $menu->id);

        DB::beginTransaction();
        try {
            $menu->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? $menu->slug,
                'location' => $validated['location'],
                'is_active' => $request->has('is_active'),
                'position' => $validated['position'] ?? $menu->position,
            ]);

            $this->syncMenuTranslations($menu, $validated['menu_translations'] ?? []);

            $existingItemIds = $menu->allItems->pluck('id')->toArray();
            $updatedItemIds = [];

            if (isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $itemData) {
                    $existingItem = null;
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        $existingItem = MenuItem::find($itemData['id']);
                    }

                    $menuItem = $this->persistMenuItem($menu, $itemData, $index, $activeLocales, $existingItem);
                    $updatedItemIds[] = $menuItem->id;
                }
            }

            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                MenuItem::whereIn('id', $itemsToDelete)->delete();
            }

            DB::commit();

            return redirect()->route('admin.menus.edit', $menu)
                ->with('success', 'Menu mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu supprimé avec succès.');
    }

    /**
     * Toggle menu active status
     */
    public function toggleActive(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_active' => !$menu->is_active]);

        return back()->with('success', 'Statut du menu mis à jour.');
    }

    private function syncMenuTranslations(Menu $menu, array $translations): void
    {
        foreach ($translations as $translation) {
            if (empty($translation['locale'])) {
                continue;
            }

            if (!empty($translation['name'])) {
                MenuTranslation::updateOrCreate(
                    [
                        'menu_id' => $menu->id,
                        'locale' => $translation['locale'],
                    ],
                    [
                        'name' => $translation['name'],
                    ]
                );
            } else {
                MenuTranslation::where('menu_id', $menu->id)
                    ->where('locale', $translation['locale'])
                    ->delete();
            }
        }
    }

    private function validateMenuRequest(Request $request, array $activeLocales, ?int $menuId = null): array
    {
        $slugRule = $menuId
            ? 'nullable|string|max:255|unique:menus,slug,' . $menuId
            : 'nullable|string|max:255|unique:menus';

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'location' => 'required|in:header,footer,footer_bottom',
            'is_active' => 'boolean',
            'position' => 'nullable|integer|min:0',
            'menu_translations' => 'nullable|array',
            'menu_translations.*.locale' => 'required_with:menu_translations|in:' . implode(',', $activeLocales),
            'menu_translations.*.name' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:menu_items,id',
            'items.*.link_type' => 'required_with:items|in:custom,internal,external,category,page,tour',
            'items.*.link_url' => 'nullable|string|max:500',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.page_id' => 'nullable|exists:pages,id',
            'items.*.tour_id' => 'nullable|exists:tours,id',
            'items.*.order' => 'nullable|integer|min:0',
            'items.*.is_active' => 'boolean',
            'items.*.icon' => 'nullable|string|max:100',
            'items.*.translations' => 'nullable|array',
            'items.*.translations.*.locale' => 'nullable|in:' . implode(',', $activeLocales),
            'items.*.translations.*.label' => 'nullable|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request, $activeLocales) {
            $items = $request->input('items', []);
            if (!is_array($items)) {
                return;
            }

            foreach ($items as $index => $item) {
                $linkType = $item['link_type'] ?? '';
                $itemNumber = $index + 1;

                if (in_array($linkType, MenuItem::customLinkTypes(), true)) {
                    if (empty(trim($item['link_url'] ?? ''))) {
                        $validator->errors()->add("items.$index.link_url", "L'item #$itemNumber : l'URL est requise.");
                    }

                    foreach ($activeLocales as $locale) {
                        $translation = collect($item['translations'] ?? [])->firstWhere('locale', $locale);
                        $label = trim($translation['label'] ?? '');
                        if ($label === '') {
                            $validator->errors()->add(
                                "items.$index.translations",
                                "L'item #$itemNumber : le label en " . strtoupper($locale) . " est requis pour un lien personnalisé."
                            );
                        }
                    }
                }

                if ($linkType === 'category' && empty($item['category_id'])) {
                    $validator->errors()->add("items.$index.category_id", "L'item #$itemNumber : veuillez sélectionner une catégorie.");
                }

                if ($linkType === 'page' && empty($item['page_id'])) {
                    $validator->errors()->add("items.$index.page_id", "L'item #$itemNumber : veuillez sélectionner une page.");
                }

                if ($linkType === 'tour' && empty($item['tour_id'])) {
                    $validator->errors()->add("items.$index.tour_id", "L'item #$itemNumber : veuillez sélectionner un tour.");
                }
            }
        });

        return $validator->validate();
    }

    private function persistMenuItem(Menu $menu, array $itemData, int $index, array $activeLocales, ?MenuItem $existingItem = null): MenuItem
    {
        $linkType = $itemData['link_type'];
        $isCustom = in_array($linkType, MenuItem::customLinkTypes(), true);

        $attributes = [
            'menu_id' => $menu->id,
            'link_type' => $linkType,
            'link_url' => $isCustom ? ($itemData['link_url'] ?? null) : null,
            'category_id' => $linkType === 'category' ? ($itemData['category_id'] ?? null) : null,
            'page_id' => $linkType === 'page' ? ($itemData['page_id'] ?? null) : null,
            'tour_id' => $linkType === 'tour' ? ($itemData['tour_id'] ?? null) : null,
            'order' => $itemData['order'] ?? $index,
            'is_active' => isset($itemData['is_active']),
            'icon' => $itemData['icon'] ?? null,
            'label' => 'Item',
        ];

        if ($existingItem) {
            $existingItem->update($attributes);
            $menuItem = $existingItem;
        } else {
            $menuItem = MenuItem::create($attributes);
        }

        $this->syncMenuItemTranslations($menuItem, $itemData, $activeLocales);

        return $menuItem;
    }

    private function syncMenuItemTranslations(MenuItem $menuItem, array $itemData, array $activeLocales): void
    {
        $menuItem->translations()->delete();

        if ($menuItem->usesEntityLabel()) {
            $menuItem->load(['category.translations', 'page.translations', 'tour.translations']);
            $defaultLabel = 'Item';

            foreach ($activeLocales as $locale) {
                $label = $this->resolveEntityLabel($menuItem, $locale);
                if ($label) {
                    MenuItemTranslation::create([
                        'menu_item_id' => $menuItem->id,
                        'locale' => $locale,
                        'label' => $label,
                    ]);
                    $defaultLabel = $defaultLabel === 'Item' ? $label : $defaultLabel;
                }
            }

            $menuItem->update(['label' => $defaultLabel]);
            return;
        }

        $defaultLabel = 'Item';
        foreach ($itemData['translations'] ?? [] as $translation) {
            if (empty($translation['locale']) || trim($translation['label'] ?? '') === '') {
                continue;
            }

            MenuItemTranslation::create([
                'menu_item_id' => $menuItem->id,
                'locale' => $translation['locale'],
                'label' => $translation['label'],
            ]);
            $defaultLabel = $translation['label'];
        }

        $menuItem->update(['label' => $defaultLabel]);
    }

    private function resolveEntityLabel(MenuItem $menuItem, string $locale): ?string
    {
        switch ($menuItem->link_type) {
            case 'category':
                if (!$menuItem->category) {
                    return null;
                }
                $translation = $menuItem->category->translate($locale);
                return $translation?->name ?? $menuItem->category->name;
            case 'page':
                if (!$menuItem->page) {
                    return null;
                }
                $translation = $menuItem->page->translate($locale);
                return $translation?->title ?? $menuItem->page->title ?? null;
            case 'tour':
                if (!$menuItem->tour) {
                    return null;
                }
                $translation = $menuItem->tour->translate($locale);
                return $translation?->title ?? $menuItem->tour->title ?? null;
        }

        return null;
    }
}
