<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportProductionSitemap extends Command
{
    protected $signature = 'sitemap:export {--url=https://tripfrommarrakech.com : URL de base APP_URL}';

    protected $description = 'Exporte un sitemap.xml statique avec les bonnes URLs (upload FTP sur public/sitemap.xml)';

    public function handle(SitemapService $sitemapService): int
    {
        $url = rtrim($this->option('url'), '/');
        config(['app.url' => $url]);

        $xml = $sitemapService->toXml();
        $path = database_path('sitemap-tripfrommarrakech.xml');

        File::put($path, $xml);

        $this->info("Sitemap exporté : {$path}");
        $this->line("URL de base : {$url}");
        $this->newLine();
        $this->comment('Sur le serveur : uploadez ce fichier vers public/sitemap.xml (remplace l\'ancien).');
        $this->comment('Ou supprimez public/sitemap.xml et déployez la route dynamique + .htaccess.');

        return Command::SUCCESS;
    }
}
