<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\FAQTranslation;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Afficher la liste des FAQs
     */
    public function index()
    {
        $faqs = FAQ::ordered()->with('translations')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        return view('admin.faqs.create', compact('availableLocales'));
    }

    /**
     * Créer une nouvelle FAQ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'translations.*.locale' => 'required|in:' . implode(',', \App\Models\Language::active()->pluck('code')->toArray()),
            'translations.*.question' => 'required|string|max:255',
            'translations.*.answer' => 'required|string',
        ]);

        $faq = FAQ::create([
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($validated['translations'] as $translation) {
            FAQTranslation::create([
                'faq_id' => $faq->id,
                'locale' => $translation['locale'],
                'question' => $translation['question'],
                'answer' => $translation['answer'],
            ]);
        }

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ ajoutée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(FAQ $faq)
    {
        $faq->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        return view('admin.faqs.edit', compact('faq', 'availableLocales'));
    }

    /**
     * Mettre à jour une FAQ
     */
    public function update(Request $request, FAQ $faq)
    {
        $validated = $request->validate([
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'translations.*.locale' => 'required|in:' . implode(',', \App\Models\Language::active()->pluck('code')->toArray()),
            'translations.*.question' => 'required|string|max:255',
            'translations.*.answer' => 'required|string',
        ]);

        $faq->update([
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($validated['translations'] as $translation) {
            FAQTranslation::updateOrCreate(
                [
                    'faq_id' => $faq->id,
                    'locale' => $translation['locale'],
                ],
                [
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]
            );
        }

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ mise à jour avec succès.');
    }

    /**
     * Supprimer une FAQ
     */
    public function destroy(FAQ $faq)
    {
        $faq->delete();
        
        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ supprimée avec succès.');
    }
}
