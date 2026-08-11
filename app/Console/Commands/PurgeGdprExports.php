<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeGdprExports extends Command
{
    protected $signature = 'gdpr:purge-exports';

    protected $description = 'Supprime les exports de données RGPD expirés';

    public function handle(): int
    {
        $retentionHours = config('gdpr.export.file_retention_hours', 24);
        $cutoff = now()->subHours($retentionHours)->getTimestamp();
        $deleted = 0;

        if (!Storage::exists('exports')) {
            $this->info('Aucun dossier exports trouvé.');
            return Command::SUCCESS;
        }

        foreach (Storage::files('exports') as $file) {
            if (Storage::lastModified($file) < $cutoff) {
                Storage::delete($file);
                $deleted++;
            }
        }

        $this->info("{$deleted} export(s) supprimé(s).");

        return Command::SUCCESS;
    }
}
