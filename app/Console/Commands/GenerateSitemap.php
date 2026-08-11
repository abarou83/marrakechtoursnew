<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Affiche l\'URL du sitemap dynamique (basé sur APP_URL)';

    public function handle(SitemapService $sitemapService): int
    {
        $this->info('Le sitemap est servi dynamiquement par Laravel.');
        $this->line('URL de base (APP_URL) : ' . $sitemapService->baseUrl());
        $this->line('Sitemap : ' . $sitemapService->baseUrl() . '/sitemap.xml');
        $this->line('Robots  : ' . $sitemapService->baseUrl() . '/robots.txt');
        $this->newLine();
        $this->comment('Vérifiez que APP_URL dans .env correspond à votre domaine en production.');

        return Command::SUCCESS;
    }
}
