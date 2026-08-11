<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Afficher une page par son slug
     */
    public function show($slugOrId)
    {
        $locale = app()->getLocale();
        
        // Support both slug and numeric ID
        if (is_numeric($slugOrId)) {
            $page = Page::where('is_active', true)->findOrFail($slugOrId);
            $translation = $page->translate($locale);
            if (!$translation) {
                abort(404, 'Page non trouvée');
            }
        } else {
            // Chercher la page par slug dans les traductions
            $translation = PageTranslation::where('slug', $slugOrId)
                ->where('locale', $locale)
                ->with('page')
                ->first();
            
            // Si pas trouvée dans la locale actuelle, chercher dans la locale par défaut
            if (!$translation) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $translation = PageTranslation::where('slug', $slugOrId)
                    ->where('locale', $defaultLocale)
                    ->with('page')
                    ->first();
            }
            
            if (!$translation || !$translation->page || !$translation->page->is_active) {
                abort(404, 'Page non trouvée');
            }
            
            $page = $translation->page;
        }
        $page->load('translations');
        
        return view('frontend.pages.show', compact('page', 'translation'));
    }
}


