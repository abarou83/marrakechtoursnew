<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Guide;
use App\Models\LandingPage;
use App\Models\Page;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class SitemapService
{
    protected array $locales = ['fr', 'en', 'es'];
    protected string $defaultLocale = 'fr';

    public function toXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;

        $xml .= $this->urlEntryWithHreflang('home', [], '1.0', 'daily');
        $xml .= $this->urlEntryWithHreflang('tours.index', [], '0.9', 'daily');

        if (Route::has('blog.index')) {
            $xml .= $this->urlEntryWithHreflang('blog.index', [], '0.8', 'daily');
        }

        if (Route::has('guides.index')) {
            $xml .= $this->urlEntryWithHreflang('guides.index', [], '0.8', 'weekly');
        }

        if (Route::has('contact')) {
            $xml .= $this->urlEntry($this->routeUrl('contact'), '0.6', 'monthly');
        }

        foreach (Category::all() as $category) {
            $xml .= $this->urlEntry(
                $this->routeUrl('category.show', $category->url_key ?? $category->slug),
                '0.8',
                'weekly',
                $category->updated_at
            );
        }

        foreach (Tour::where('is_active', true)->get() as $tour) {
            $xml .= $this->tourUrlEntry($tour);
        }

        if (Schema::hasTable('landing_pages')) {
            foreach (LandingPage::published()->indexable()->with('translations')->get() as $landingPage) {
                $xml .= $this->landingPageUrlEntry($landingPage);
            }
        }

        if (Schema::hasTable('pages')) {
            foreach (Page::active()->ordered()->get() as $page) {
                $xml .= $this->urlEntry(
                    $this->routeUrl('pages.show', $page->url_key ?? $page->slug),
                    '0.6',
                    'monthly',
                    $page->updated_at
                );
            }
        }

        if (Schema::hasTable('blog_posts') && Route::has('blog.show')) {
            foreach (BlogPost::published()->latestPublished()->with('translations')->get() as $post) {
                $xml .= $this->blogPostUrlEntry($post);
            }
        }

        if (Schema::hasTable('guides') && Route::has('guides.show')) {
            foreach (Guide::published()->with('translations')->orderBy('position')->get() as $guide) {
                $xml .= $this->guideUrlEntry($guide);
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    protected function tourUrlEntry(Tour $tour): string
    {
        $xml = '  <url>' . PHP_EOL;
        
        $defaultTranslation = $tour->translate($this->defaultLocale);
        $slug = $defaultTranslation?->slug ?? $tour->slug ?? $tour->url_key;
        
        $xml .= '    <loc>' . $this->escape($this->routeUrl('tours.show', $slug)) . '</loc>' . PHP_EOL;

        if ($tour->updated_at) {
            $xml .= '    <lastmod>' . $tour->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
        }

        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.9</priority>' . PHP_EOL;

        foreach ($this->locales as $locale) {
            $translation = $tour->translate($locale);
            $localeSlug = $translation?->slug ?? $slug;
            
            try {
                $url = $this->routeUrl('tours.show', ['locale' => $locale, 'slug' => $localeSlug]);
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$this->escape($url)}\" />" . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }

        $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$this->escape($this->routeUrl('tours.show', $slug))}\" />" . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    protected function landingPageUrlEntry(LandingPage $landingPage): string
    {
        $xml = '  <url>' . PHP_EOL;
        
        $defaultTranslation = $landingPage->translate($this->defaultLocale);
        $slug = $defaultTranslation?->slug;
        
        if (!$slug) {
            return '';
        }
        
        $xml .= '    <loc>' . $this->escape($this->routeUrl('landing.show', $slug)) . '</loc>' . PHP_EOL;

        if ($landingPage->updated_at) {
            $xml .= '    <lastmod>' . $landingPage->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
        }

        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.8</priority>' . PHP_EOL;

        foreach ($this->locales as $locale) {
            $translation = $landingPage->translate($locale);
            $localeSlug = $translation?->slug ?? $slug;
            
            try {
                $url = $this->routeUrl('landing.show', ['locale' => $locale, 'slug' => $localeSlug]);
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$this->escape($url)}\" />" . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }

        $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$this->escape($this->routeUrl('landing.show', $slug))}\" />" . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    protected function blogPostUrlEntry(BlogPost $post): string
    {
        $xml = '  <url>' . PHP_EOL;
        
        $defaultTranslation = $post->translate($this->defaultLocale);
        $slug = $defaultTranslation?->slug;
        
        if (!$slug) {
            return '';
        }
        
        $xml .= '    <loc>' . $this->escape($this->routeUrl('blog.show', $slug)) . '</loc>' . PHP_EOL;

        if ($post->updated_at) {
            $xml .= '    <lastmod>' . $post->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
        }

        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.7</priority>' . PHP_EOL;

        foreach ($this->locales as $locale) {
            $translation = $post->translate($locale);
            $localeSlug = $translation?->slug ?? $slug;
            
            try {
                $url = $this->routeUrl('blog.show', ['locale' => $locale, 'slug' => $localeSlug]);
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$this->escape($url)}\" />" . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }

        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    protected function guideUrlEntry(Guide $guide): string
    {
        $xml = '  <url>' . PHP_EOL;

        $defaultTranslation = $guide->translate($this->defaultLocale);
        $slug = $defaultTranslation?->slug;

        if (!$slug) {
            return '';
        }

        $xml .= '    <loc>' . $this->escape($this->routeUrl('guides.show', $slug)) . '</loc>' . PHP_EOL;

        if ($guide->updated_at) {
            $xml .= '    <lastmod>' . $guide->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
        }

        $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.75</priority>' . PHP_EOL;

        foreach ($this->locales as $locale) {
            $translation = $guide->translate($locale);
            $localeSlug = $translation?->slug ?? $slug;

            try {
                $url = $this->routeUrl('guides.show', ['locale' => $locale, 'slug' => $localeSlug]);
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$this->escape($url)}\" />" . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }

        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    protected function urlEntryWithHreflang(string $routeName, array $params, string $priority, string $changefreq): string
    {
        $xml = '  <url>' . PHP_EOL;
        
        $xml .= '    <loc>' . $this->escape($this->routeUrl($routeName, $params)) . '</loc>' . PHP_EOL;
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;

        foreach ($this->locales as $locale) {
            try {
                $url = $this->routeUrl($routeName, array_merge($params, ['locale' => $locale]));
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$this->escape($url)}\" />" . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }

        $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$this->escape($this->routeUrl($routeName, $params))}\" />" . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    public function baseUrl(): string
    {
        $configured = rtrim((string) config('app.url'), '/');

        if (!app()->runningInConsole() && request()->getHttpHost()) {
            $requestBase = rtrim(request()->getSchemeAndHttpHost(), '/');

            if ($this->isLocalhostUrl($configured) && !$this->isLocalhostUrl($requestBase)) {
                return $requestBase;
            }
        }

        return $configured;
    }

    private function isLocalhostUrl(string $url): bool
    {
        return (bool) preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/|$)#i', $url);
    }

    private function url(string $path): string
    {
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }

    private function routeUrl(string $name, mixed $parameters = []): string
    {
        return $this->baseUrl() . route($name, $parameters, absolute: false);
    }

    private function urlEntry(string $loc, string $priority, string $changefreq, ?Carbon $lastmod = null): string
    {
        $entry = '  <url>' . PHP_EOL;
        $entry .= '    <loc>' . $this->escape($loc) . '</loc>' . PHP_EOL;

        if ($lastmod) {
            $entry .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . PHP_EOL;
        }

        $entry .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $entry .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        $entry .= '  </url>' . PHP_EOL;

        return $entry;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
