<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Tour;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingPageController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function show(Request $request, string $slug)
    {
        $cacheKey = "landing_page_{$slug}_" . app()->getLocale();

        $landingPage = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return LandingPage::published()
                ->whereHas('translations', fn($q) => $q->where('slug', $slug))
                ->with(['translations', 'destination.translations', 'category.translations'])
                ->first();
        });

        if (!$landingPage) {
            abort(404);
        }

        $landingPage->increment('views_count');

        $translation = $landingPage->translate();

        $tours = $landingPage->getToursQuery()
            ->with(['translations', 'media', 'pricings.groupPrices', 'categories.translations'])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->orderByDesc('avg_rating')
            ->paginate(12);

        $seo = $this->seoService->generateMetaTags([
            'title' => $translation?->meta_title ?? $translation?->title,
            'description' => $translation?->meta_description ?? $translation?->intro,
            'canonical' => url()->current(),
            'type' => 'website',
            'image' => $translation?->og_image ?? $tours->first()?->getFirstMediaUrl('images', 'og'),
        ]);

        $hreflang = $this->seoService->generateHreflangTags(
            'landing.show',
            ['slug' => $slug]
        );

        $breadcrumbs = $this->buildBreadcrumbs($landingPage, $translation);

        $jsonLd = $this->generateLandingPageJsonLd($landingPage, $translation, $tours);

        $relatedPages = $this->getRelatedLandingPages($landingPage);

        $faqs = $translation?->faqs ?? [];

        return view('frontend.landing.show', compact(
            'landingPage',
            'translation',
            'tours',
            'seo',
            'hreflang',
            'breadcrumbs',
            'jsonLd',
            'relatedPages',
            'faqs'
        ));
    }

    public function destination(Request $request, string $destinationSlug)
    {
        $landingPage = LandingPage::published()
            ->ofType('destination')
            ->whereHas('destination.translations', fn($q) => $q->where('slug', $destinationSlug))
            ->with(['translations', 'destination.translations'])
            ->first();

        if (!$landingPage) {
            abort(404);
        }

        return $this->show($request, $landingPage->translate()?->slug ?? $destinationSlug);
    }

    public function category(Request $request, string $categorySlug)
    {
        $landingPage = LandingPage::published()
            ->ofType('category')
            ->whereHas('category.translations', fn($q) => $q->where('slug', $categorySlug))
            ->with(['translations', 'category.translations'])
            ->first();

        if (!$landingPage) {
            abort(404);
        }

        return $this->show($request, $landingPage->translate()?->slug ?? $categorySlug);
    }

    protected function buildBreadcrumbs(LandingPage $landingPage, $translation): array
    {
        $breadcrumbs = [
            ['title' => __('Accueil'), 'url' => route('home')],
        ];

        if ($landingPage->type === 'destination' && $landingPage->destination) {
            $breadcrumbs[] = [
                'title' => __('Destinations'),
                'url' => route('tours.index'),
            ];
            $breadcrumbs[] = [
                'title' => $landingPage->destination->translate()?->name ?? $landingPage->destination->name,
                'url' => null,
            ];
        } elseif ($landingPage->type === 'category' && $landingPage->category) {
            $breadcrumbs[] = [
                'title' => __('Activités'),
                'url' => route('tours.index'),
            ];
            $breadcrumbs[] = [
                'title' => $landingPage->category->translate()?->name ?? $landingPage->category->name,
                'url' => null,
            ];
        } else {
            $breadcrumbs[] = [
                'title' => $translation?->title ?? 'Page',
                'url' => null,
            ];
        }

        return $breadcrumbs;
    }

    protected function generateLandingPageJsonLd(LandingPage $landingPage, $translation, $tours): array
    {
        $jsonLd = [];

        $jsonLd[] = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $translation?->title,
            'description' => $translation?->meta_description ?? $translation?->intro,
            'url' => url()->current(),
            'numberOfItems' => $tours->total(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $tours->take(10)->map(function ($tour, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('tours.show', $tour->slug),
                        'name' => $tour->translate()?->title ?? $tour->title,
                    ];
                })->toArray(),
            ],
        ];

        $jsonLd[] = $this->seoService->generateBreadcrumbJsonLd(
            $this->buildBreadcrumbs($landingPage, $translation)
        );

        if (!empty($translation?->faqs)) {
            $jsonLd[] = $this->seoService->generateFaqJsonLd($translation->faqs);
        }

        return $jsonLd;
    }

    protected function getRelatedLandingPages(LandingPage $landingPage): \Illuminate\Support\Collection
    {
        return LandingPage::published()
            ->where('id', '!=', $landingPage->id)
            ->where(function ($query) use ($landingPage) {
                if ($landingPage->destination_id) {
                    $query->where('destination_id', $landingPage->destination_id);
                }
                if ($landingPage->category_id) {
                    $query->orWhere('category_id', $landingPage->category_id);
                }
            })
            ->where('tours_count', '>=', 3)
            ->with('translations')
            ->limit(4)
            ->get();
    }
}
