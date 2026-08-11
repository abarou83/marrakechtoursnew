<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CacheHeaders
{
    protected array $cacheableRoutes = [
        'home' => 300,
        'tours.index' => 300,
        'tours.show' => 600,
        'blog.index' => 300,
        'blog.show' => 600,
        'landing.*' => 600,
        'pages.show' => 600,
        'category.show' => 300,
    ];

    protected array $noCacheRoutes = [
        'dashboard.*',
        'admin.*',
        'cart.*',
        'booking.*',
        'api.*',
    ];

    public function handle(Request $request, Closure $next, ?string $maxAge = null): SymfonyResponse
    {
        $response = $next($request);

        if (!$response instanceof Response && !$response instanceof SymfonyResponse) {
            return $response;
        }

        if ($request->isMethod('GET') && !$this->isNoCacheRoute($request)) {
            $ttl = $maxAge ? (int) $maxAge : $this->getCacheTtl($request);

            if ($ttl > 0 && !auth()->check() && !auth('client')->check()) {
                $response->headers->set('Cache-Control', "public, max-age={$ttl}, s-maxage={$ttl}");
                $response->headers->set('Vary', 'Accept-Encoding, Accept-Language');

                $etag = md5($response->getContent() . $request->fullUrl());
                $response->setEtag($etag);

                if ($request->headers->get('If-None-Match') === "\"{$etag}\"") {
                    $response->setStatusCode(304);
                    $response->setContent('');
                }
            } else {
                $this->setNoCacheHeaders($response);
            }
        } else {
            $this->setNoCacheHeaders($response);
        }

        return $response;
    }

    protected function getCacheTtl(Request $request): int
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 0;
        }

        foreach ($this->cacheableRoutes as $pattern => $ttl) {
            if ($this->matchRoute($routeName, $pattern)) {
                return $ttl;
            }
        }

        return 0;
    }

    protected function isNoCacheRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return true;
        }

        foreach ($this->noCacheRoutes as $pattern) {
            if ($this->matchRoute($routeName, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function matchRoute(string $routeName, string $pattern): bool
    {
        if (str_ends_with($pattern, '*')) {
            $prefix = rtrim($pattern, '*');
            return str_starts_with($routeName, $prefix);
        }

        return $routeName === $pattern;
    }

    protected function setNoCacheHeaders(SymfonyResponse $response): void
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
    }
}
