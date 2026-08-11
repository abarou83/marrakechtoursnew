<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    protected const DEFAULT_TTL = 3600;
    protected const SHORT_TTL = 300;
    protected const LONG_TTL = 86400;

    protected array $tags = [
        'tours' => ['tours', 'catalog'],
        'categories' => ['categories', 'catalog'],
        'pages' => ['pages', 'content'],
        'blog' => ['blog', 'content'],
        'settings' => ['settings', 'config'],
        'landing' => ['landing', 'seo'],
        'pricing' => ['pricing', 'catalog'],
    ];

    public function remember(string $key, mixed $callback, ?int $ttl = null, array $tags = []): mixed
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;

        if ($this->supportsTagging() && !empty($tags)) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    public function rememberForever(string $key, mixed $callback, array $tags = []): mixed
    {
        if ($this->supportsTagging() && !empty($tags)) {
            return Cache::tags($tags)->rememberForever($key, $callback);
        }

        return Cache::rememberForever($key, $callback);
    }

    public function forget(string $key, array $tags = []): bool
    {
        if ($this->supportsTagging() && !empty($tags)) {
            return Cache::tags($tags)->forget($key);
        }

        return Cache::forget($key);
    }

    public function flush(array $tags = []): bool
    {
        if ($this->supportsTagging() && !empty($tags)) {
            Cache::tags($tags)->flush();
            return true;
        }

        if (empty($tags)) {
            Cache::flush();
            return true;
        }

        return false;
    }

    public function flushTours(): void
    {
        $this->flush($this->tags['tours']);
        Log::info('Tours cache flushed');
    }

    public function flushCategories(): void
    {
        $this->flush($this->tags['categories']);
        Log::info('Categories cache flushed');
    }

    public function flushPages(): void
    {
        $this->flush($this->tags['pages']);
        Log::info('Pages cache flushed');
    }

    public function flushBlog(): void
    {
        $this->flush($this->tags['blog']);
        Log::info('Blog cache flushed');
    }

    public function flushSettings(): void
    {
        $this->flush($this->tags['settings']);
        Log::info('Settings cache flushed');
    }

    public function flushAll(): void
    {
        foreach ($this->tags as $tagGroup) {
            $this->flush($tagGroup);
        }
        Log::info('All tagged caches flushed');
    }

    public function tourKey(int $tourId, string $locale): string
    {
        return "tour_{$tourId}_{$locale}";
    }

    public function toursListKey(string $locale, array $filters = []): string
    {
        $filterHash = md5(json_encode($filters));
        return "tours_list_{$locale}_{$filterHash}";
    }

    public function categoryKey(int $categoryId, string $locale): string
    {
        return "category_{$categoryId}_{$locale}";
    }

    public function pageKey(string $slug, string $locale): string
    {
        return "page_{$slug}_{$locale}";
    }

    public function settingsKey(string $group = 'general'): string
    {
        return "settings_{$group}";
    }

    public function landingPageKey(string $slug, string $locale): string
    {
        return "landing_{$slug}_{$locale}";
    }

    public function homePageKey(string $locale): string
    {
        return "home_{$locale}";
    }

    protected function supportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'dynamodb']);
    }

    public function warmUp(): void
    {
        Log::info('Starting cache warm-up');

        $locales = ['fr', 'en', 'es'];

        foreach ($locales as $locale) {
            app()->setLocale($locale);
        }

        Log::info('Cache warm-up completed');
    }
}
