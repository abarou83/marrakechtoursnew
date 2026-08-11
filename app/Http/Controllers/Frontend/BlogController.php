<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogCategory;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $query = BlogPost::published()
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale')]),
                'media',
                'categories.translations',
            ])
            ->latestPublished();

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function($q) use ($search, $locale) {
                $q->where('locale', $locale)
                  ->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%"));
            });
        }

        $posts = $query->paginate(9)->withQueryString();

        $categories = Cache::remember('blog_categories_' . $locale, 3600, function () use ($locale) {
            return BlogCategory::withCount(['posts' => fn($q) => $q->published()])
                ->having('posts_count', '>', 0)
                ->with(['translations' => fn($q) => $q->where('locale', $locale)])
                ->orderBy('name')
                ->get();
        });

        $popularTags = Cache::remember('blog_popular_tags', 3600, function () {
            return BlogPost::published()
                ->whereNotNull('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take(15)
                ->keys();
        });

        $seo = $this->seoService->generateMetaTags([
            'title' => __('Blog - Conseils voyages au Maroc | MarrakechTours'),
            'description' => __('Découvrez nos articles, conseils et guides pour préparer votre voyage au Maroc. Destinations, culture, gastronomie et bons plans.'),
            'type' => 'website',
        ]);

        $hreflang = $this->seoService->generateHreflangTags('blog.index', []);

        return view('frontend.blog.index', compact(
            'posts',
            'categories',
            'popularTags',
            'seo',
            'hreflang'
        ));
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $translation = BlogPostTranslation::where('slug', $slug)
            ->where('locale', $locale)
            ->with('blogPost')
            ->first();

        if (!$translation) {
            $translation = BlogPostTranslation::where('slug', $slug)
                ->where('locale', config('app.fallback_locale'))
                ->with('blogPost')
                ->first();
        }

        if (!$translation || !$translation->blogPost || !$translation->blogPost->is_active) {
            abort(404);
        }

        $post = $translation->blogPost;
        $post->load(['media', 'categories.translations', 'author']);

        if ($post->published_at && $post->published_at->isFuture()) {
            abort(404);
        }

        $post->increment('views_count');

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where(function($query) use ($post) {
                if ($post->categories->isNotEmpty()) {
                    $query->whereHas('categories', fn($q) => 
                        $q->whereIn('blog_categories.id', $post->categories->pluck('id'))
                    );
                }
                if ($post->tags) {
                    foreach ($post->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale')])])
            ->latestPublished()
            ->take(3)
            ->get();

        $recentPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale')])])
            ->latestPublished()
            ->take(5)
            ->get();

        $seo = $this->seoService->generateMetaTags([
            'title' => $translation->meta_title ?? $translation->title,
            'description' => $translation->meta_description ?? $this->seoService->truncateDescription(strip_tags($translation->excerpt ?? $translation->content)),
            'image' => $post->getFirstMediaUrl('featured', 'og') ?: $post->getFirstMediaUrl('featured'),
            'type' => 'article',
        ]);

        $hreflang = $this->seoService->generateHreflangTags('blog.show', ['slug' => $slug]);

        $jsonLd = $this->generateArticleJsonLd($post, $translation);

        $breadcrumbs = [
            ['title' => __('Accueil'), 'url' => route('home')],
            ['title' => __('Blog'), 'url' => route('blog.index')],
        ];

        if ($post->categories->first()) {
            $category = $post->categories->first();
            $breadcrumbs[] = [
                'title' => $category->translate()?->name ?? $category->name,
                'url' => route('blog.index', ['category' => $category->slug]),
            ];
        }

        $breadcrumbs[] = ['title' => $translation->title, 'url' => null];

        return view('frontend.blog.show', compact(
            'post',
            'translation',
            'relatedPosts',
            'recentPosts',
            'seo',
            'hreflang',
            'jsonLd',
            'breadcrumbs'
        ));
    }

    public function category(string $slug)
    {
        $locale = app()->getLocale();

        $category = BlogCategory::where('slug', $slug)
            ->with('translations')
            ->firstOrFail();

        $posts = BlogPost::published()
            ->whereHas('categories', fn($q) => $q->where('blog_categories.id', $category->id))
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.fallback_locale')]),
                'media',
            ])
            ->latestPublished()
            ->paginate(9);

        $categoryTranslation = $category->translate($locale);

        $seo = $this->seoService->generateMetaTags([
            'title' => $categoryTranslation?->meta_title ?? __(':category - Blog | MarrakechTours', ['category' => $categoryTranslation?->name ?? $category->name]),
            'description' => $categoryTranslation?->meta_description ?? __('Articles sur :category. Conseils, guides et inspiration pour votre voyage au Maroc.', ['category' => $categoryTranslation?->name ?? $category->name]),
        ]);

        return view('frontend.blog.category', compact('category', 'categoryTranslation', 'posts', 'seo'));
    }

    protected function generateArticleJsonLd(BlogPost $post, BlogPostTranslation $translation): array
    {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $translation->title,
            'description' => $this->seoService->truncateDescription(strip_tags($translation->excerpt ?? $translation->content)),
            'url' => url()->current(),
            'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'MarrakechTours',
                'url' => config('app.url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'MarrakechTours',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];

        if ($post->author) {
            $jsonLd['author'] = [
                '@type' => 'Person',
                'name' => $post->author->name,
            ];
        }

        if ($post->getFirstMediaUrl('featured')) {
            $jsonLd['image'] = [
                '@type' => 'ImageObject',
                'url' => $post->getFirstMediaUrl('featured'),
            ];
        }

        return $jsonLd;
    }
}
