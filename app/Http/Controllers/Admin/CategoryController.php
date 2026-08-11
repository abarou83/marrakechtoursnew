<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Get all categories with their parent and children, ordered hierarchically
        $categories = Category::with(['parent', 'children'])
            ->withCount('tours')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        
        // Also get all subcategories for count
        $allCategories = Category::withCount('tours')->get();
        
        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    public function create()
    {
        // Get all categories that can be parents (excluding potential circular references)
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'og_image' => 'nullable|url|max:255',
            'focus_keyword' => 'nullable|string|max:100',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(string $id)
    {
        $category = Category::withCount('tours')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(string $id)
    {
        $category = Category::with('children')->findOrFail($id);
        // Get all categories that can be parents (excluding the current category and its descendants to avoid circular references)
        $allCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        
        // Filter out categories that are descendants of the current category
        $parentCategories = $allCategories->filter(function($cat) use ($id) {
            return !$this->isDescendant($cat, $id);
        });
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Une catégorie ne peut pas être sa propre parente.');
                    }
                    // Prevent circular reference: check if the selected parent is a child of this category
                    $category = Category::find($id);
                    if ($category && $value) {
                        $parent = Category::find($value);
                        if ($parent && $this->isDescendant($parent, $id)) {
                            $fail('Impossible de définir cette catégorie comme parente (référence circulaire).');
                        }
                    }
                },
            ],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'og_image' => 'nullable|url|max:255',
            'focus_keyword' => 'nullable|string|max:100',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->tours()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer. Cette catégorie contient des tours.');
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer. Cette catégorie contient des sous-catégories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Check if a category is a descendant of another category
     */
    private function isDescendant($category, $ancestorId)
    {
        if ($category->parent_id == $ancestorId) {
            return true;
        }
        
        if ($category->parent_id === null) {
            return false;
        }
        
        $parent = Category::find($category->parent_id);
        if (!$parent) {
            return false;
        }
        
        return $this->isDescendant($parent, $ancestorId);
    }
}
