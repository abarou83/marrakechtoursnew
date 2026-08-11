<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideTranslation;
use App\Services\SeoService;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $query = Guide::published()
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale', 'fr')]),
            ])
            ->orderBy('position')
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search, $locale) {
                $q->where('locale', $locale)
                    ->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%"));
            });
        }

        $guides = $query->paginate(12)->withQueryString();

        $categories = Guide::published()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        $seo = $this->seoService->generateMetaTags([
            'title' => __('Guides voyage Maroc | MarrakechTours'),
            'description' => __('Guides pratiques pour préparer votre séjour au Maroc : Marrakech, désert, culture, gastronomie et conseils locaux.'),
            'type' => 'website',
        ]);

        $hreflang = $this->seoService->generateHreflangTags('guides.index', []);

        return view('frontend.guides.index', compact('guides', 'categories', 'seo', 'hreflang'));
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $translation = GuideTranslation::where('slug', $slug)
            ->where('locale', $locale)
            ->with('guide.tours.translations')
            ->first();

        if (!$translation) {
            $translation = GuideTranslation::where('slug', $slug)
                ->where('locale', config('app.fallback_locale', 'fr'))
                ->with('guide.tours.translations')
                ->first();
        }

        if (!$translation || !$translation->guide || !$translation->guide->is_published) {
            abort(404);
        }

        $guide = $translation->guide;

        if ($guide->published_at && $guide->published_at->isFuture()) {
            abort(404);
        }

        $guide->incrementViews();

        $relatedGuides = Guide::published()
            ->where('id', '!=', $guide->id)
            ->where('category', $guide->category)
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale', 'fr')])])
            ->orderBy('position')
            ->take(3)
            ->get();

        $seo = $this->seoService->generateMetaTags([
            'title' => $translation->meta_title ?? $translation->title,
            'description' => $translation->meta_description ?? $this->seoService->truncateDescription(strip_tags($translation->excerpt ?? $translation->content)),
            'image' => $guide->featured_image ? asset('storage/' . $guide->featured_image) : null,
            'type' => 'article',
        ]);

        $hreflang = $this->seoService->generateHreflangTags('guides.show', ['slug' => $slug]);

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $translation->title,
            'description' => $this->seoService->truncateDescription(strip_tags($translation->excerpt ?? $translation->content)),
            'url' => url()->current(),
            'datePublished' => $guide->published_at?->toIso8601String() ?? $guide->created_at->toIso8601String(),
            'dateModified' => $guide->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'MarrakechTours',
            ],
        ];

        $breadcrumbs = [
            ['title' => __('Accueil'), 'url' => route('home')],
            ['title' => __('Guides'), 'url' => route('guides.index')],
            ['title' => $translation->title, 'url' => null],
        ];

        return view('frontend.guides.show', compact(
            'guide',
            'translation',
            'relatedGuides',
            'seo',
            'hreflang',
            'jsonLd',
            'breadcrumbs'
        ));
    }
}
