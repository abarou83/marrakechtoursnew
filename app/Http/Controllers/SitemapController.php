<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemapService): Response
    {
        return response($sitemapService->toXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(SitemapService $sitemapService): Response
    {
        $sitemapUrl = $sitemapService->baseUrl() . '/sitemap.xml';

        $content = <<<TXT
# Robots.txt
User-agent: *
Allow: /

# Sitemap
Sitemap: {$sitemapUrl}

# Empêcher l'indexation des pages d'administration
Disallow: /admin/
Disallow: /login
Disallow: /register
Disallow: /password/
Disallow: /dashboard

# Empêcher l'indexation des fichiers système
Disallow: /storage/
Disallow: /_ignition/

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

TXT;

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
