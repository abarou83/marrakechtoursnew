<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Afficher la liste des langues
     */
    public function index()
    {
        $languages = Language::orderBy('order')->get();
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Créer une nouvelle langue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = false;

        Language::create($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue ajoutée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Mettre à jour une langue
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $language->update($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue mise à jour avec succès.');
    }

    /**
     * Activer/Désactiver une langue
     */
    public function toggleActive(Language $language)
    {
        // Empêcher de désactiver la langue par défaut
        if ($language->is_default && $language->is_active) {
            return back()->with('error', 'Impossible de désactiver la langue par défaut.');
        }

        $language->update(['is_active' => !$language->is_active]);

        $status = $language->is_active ? 'activée' : 'désactivée';
        return back()->with('success', "Langue {$status} avec succès.");
    }

    /**
     * Définir comme langue par défaut
     */
    public function setDefault(Language $language)
    {
        DB::beginTransaction();
        try {
            // Retirer le statut par défaut de toutes les langues
            Language::where('is_default', true)->update(['is_default' => false]);

            // Définir cette langue comme par défaut et l'activer
            $language->update([
                'is_default' => true,
                'is_active' => true,
            ]);

            DB::commit();

            return back()->with('success', 'Langue par défaut définie avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Supprimer une langue
     */
    public function destroy(Language $language)
    {
        // Empêcher de supprimer la langue par défaut
        if ($language->is_default) {
            return back()->with('error', 'Impossible de supprimer la langue par défaut.');
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue supprimée avec succès.');
    }
}




