<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\Category;
use App\Models\BlogPost;
use App\Models\LandingPage;
use App\Models\Page;
use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CacheWarmup extends Command
{
    protected $signature = 'cache:warmup 
                            {--tours : Warm up tour caches}
                            {--pages : Warm up page caches}
                            {--all : Warm up all caches}
                            {--url= : Base URL for HTTP warmup}';

    protected $description = 'Warm up application caches for better performance';

    protected array $locales = ['fr', 'en', 'es'];

    public function handle(CacheService $cacheService): int
    {
        $this->info('Starting cache warmup...');

        $warmAll = $this->option('all');

        if ($warmAll || $this->option('tours')) {
            $this->warmTours();
        }

        if ($warmAll || $this->option('pages')) {
            $this->warmPages();
        }

        if ($warmAll) {
            $this->warmCategories();
            $this->warmBlogPosts();
            $this->warmLandingPages();
            $this->warmSettings();
        }

        if ($this->option('url')) {
            $this->warmUrls($this->option('url'));
        }

        $this->info('Cache warmup completed!');

        return Command::SUCCESS;
    }

    protected function warmTours(): void
    {
        $this->info('Warming tour caches...');

        $tours = Tour::where('is_active', true)
            ->with(['translations', 'pricings.groupPrices', 'categories'])
            ->get();

        $bar = $this->output->createProgressBar($tours->count() * count($this->locales));

        foreach ($this->locales as $locale) {
            app()->setLocale($locale);

            foreach ($tours as $tour) {
                $cacheKey = "tour_{$tour->id}_{$locale}";
                Cache::put($cacheKey, $this->serializeModel($tour), 3600);
                $bar->advance();
            }

            $listKey = "tours_list_{$locale}_featured";
            Cache::put($listKey, $tours->where('is_featured', true)->map(fn ($t) => $this->serializeModel($t))->values()->all(), 3600);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Warmed {$tours->count()} tours in " . count($this->locales) . " locales");
    }

    protected function warmCategories(): void
    {
        $this->info('Warming category caches...');

        $categories = Category::where('is_active', true)
            ->with(['translations', 'tours' => fn($q) => $q->where('is_active', true)])
            ->get();

        foreach ($this->locales as $locale) {
            app()->setLocale($locale);

            foreach ($categories as $category) {
                $cacheKey = "category_{$category->id}_{$locale}";
                Cache::put($cacheKey, $category->toArray(), 3600);
            }

            Cache::put("categories_all_{$locale}", $categories->toArray(), 3600);
        }

        $this->info("Warmed {$categories->count()} categories");
    }

    protected function warmPages(): void
    {
        $this->info('Warming page caches...');

        $pages = Page::where('is_active', true)->with('translations')->get();

        foreach ($this->locales as $locale) {
            foreach ($pages as $page) {
                $translation = $page->translate($locale);
                if ($translation) {
                    $cacheKey = "page_{$translation->slug}_{$locale}";
                    Cache::put($cacheKey, $page->toArray(), 3600);
                }
            }
        }

        $this->info("Warmed {$pages->count()} pages");
    }

    protected function warmBlogPosts(): void
    {
        $this->info('Warming blog caches...');

        $posts = BlogPost::published()
            ->with(['translations', 'categories'])
            ->latest('published_at')
            ->take(50)
            ->get();

        foreach ($this->locales as $locale) {
            Cache::put("blog_recent_{$locale}", $posts->toArray(), 1800);
        }

        $this->info("Warmed {$posts->count()} blog posts");
    }

    protected function warmLandingPages(): void
    {
        $this->info('Warming landing page caches...');

        $pages = LandingPage::where('is_published', true)
            ->with(['translations', 'destination', 'category'])
            ->get();

        foreach ($this->locales as $locale) {
            foreach ($pages as $page) {
                $translation = $page->translate($locale);
                if ($translation) {
                    $cacheKey = "landing_{$translation->slug}_{$locale}";
                    Cache::put($cacheKey, $page->toArray(), 3600);
                }
            }
        }

        $this->info("Warmed {$pages->count()} landing pages");
    }

    protected function warmSettings(): void
    {
        $this->info('Warming settings caches...');

        if (!class_exists(\App\Models\SiteSetting::class)) {
            $this->warn('SiteSetting model not found, skipping.');
            return;
        }

        $settings = \App\Models\SiteSetting::all()->groupBy('group');

        foreach ($settings as $group => $items) {
            Cache::put("settings_{$group}", $items->pluck('value', 'key')->toArray(), 86400);
        }

        $this->info('Settings cached');
    }

    protected function warmUrls(string $baseUrl): void
    {
        $this->info('Warming URLs via HTTP requests...');

        $urls = [
            '/',
            '/tours',
            '/blog',
            '/contact',
        ];

        $tours = Tour::where('is_active', true)->take(10)->get();
        foreach ($tours as $tour) {
            $urls[] = "/tours/{$tour->slug}";
        }

        $bar = $this->output->createProgressBar(count($urls));

        foreach ($urls as $path) {
            try {
                Http::timeout(10)->get(rtrim($baseUrl, '/') . $path);
            } catch (\Exception $e) {
                $this->warn("Failed to warm: {$path}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('URL warmup completed');
    }

    protected function serializeModel($model): array
    {
        try {
            return json_decode(json_encode($model->toArray()), true) ?? [];
        } catch (\Throwable $e) {
            return [
                'id' => $model->id,
                'slug' => $model->slug ?? null,
            ];
        }
    }
}
