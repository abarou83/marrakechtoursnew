<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tour;
use App\Services\TourImageService;
use Illuminate\Console\Command;

class SyncTourImages extends Command
{
    protected $signature = 'tours:sync-images
                            {--folder= : Dossier source (défaut : storage/app/tour-images)}
                            {--pexels : Télécharger depuis Pexels pour les tours sans image}
                            {--pixabay : Télécharger depuis Pixabay (PIXABAY_API_KEY)}
                            {--limit= : Nombre max de tours à traiter via API}';

    protected $description = 'Associe des images aux tours (dossier local et/ou Pixabay)';

    public function handle(TourImageService $tourImageService): int
    {
        $folder = $this->option('folder') ?: storage_path('app/tour-images');
        $usePixabay = (bool) $this->option('pixabay');
        $usePexels = (bool) $this->option('pexels');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        if (! $usePexels && ! $usePixabay && config('services.pexels.key')) {
            $usePexels = Tour::whereDoesntHave('images')->exists();
        }

        $withoutImages = Tour::whereDoesntHave('images')->count();
        $this->info("Tours sans image : {$withoutImages}");

        if (is_dir($folder)) {
            $this->info("Import depuis : {$folder}");
            try {
                $folderStats = $tourImageService->importFromDirectory($folder, true);
                $this->line(sprintf(
                    'Dossier : %d fichier(s), %d correspondance(s) slug, %d image(s) assignée(s).',
                    $folderStats['files'],
                    $folderStats['matched'],
                    $folderStats['assigned']
                ));
            } catch (\Throwable $e) {
                $this->error('Import dossier : '.$e->getMessage());
            }
        } else {
            $this->warn("Dossier absent (créez-le et y déposez vos photos) : {$folder}");
        }

        $remaining = Tour::whereDoesntHave('images')->count();

        if ($usePexels && $remaining > 0) {
            if (! config('services.pexels.key')) {
                $this->error('PEXELS_API_KEY manquant dans .env');

                return self::FAILURE;
            }

            $this->info('Téléchargement Pexels…');
            $pexelsStats = $tourImageService->fillMissingFromPexels($limit);
            $this->line(sprintf(
                'Pexels : %d image(s) ajoutée(s), %d échec(s).',
                $pexelsStats['attached'],
                $pexelsStats['failed']
            ));
            $remaining = Tour::whereDoesntHave('images')->count();
        }

        if ($usePixabay && $remaining > 0) {
            if (! config('services.pixabay.key')) {
                $this->error('PIXABAY_API_KEY manquant dans .env');

                return self::FAILURE;
            }

            if ($remaining === 0) {
                $this->info('Tous les tours ont déjà une image.');

                return self::SUCCESS;
            }

            $this->info('Téléchargement Pixabay…');
            $pixabayStats = $tourImageService->fillMissingFromPixabay($limit);
            $this->line(sprintf(
                'Pixabay : %d image(s) ajoutée(s), %d échec(s).',
                $pixabayStats['attached'],
                $pixabayStats['failed']
            ));
        } elseif ($remaining > 0 && ! $usePexels) {
            $this->warn("Il reste {$remaining} tour(s) sans image. Déposez des fichiers dans {$folder} ou lancez avec --pexels.");
        }

        $stillMissing = Tour::whereDoesntHave('images')->count();
        if ($stillMissing > 0) {
            $this->warn("Encore {$stillMissing} tour(s) sans image.");
        } else {
            $this->info('Tous les tours ont au moins une image.');
        }

        return self::SUCCESS;
    }
}
