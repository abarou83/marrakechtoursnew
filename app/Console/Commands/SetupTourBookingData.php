<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupTourBookingData extends Command
{
    protected $signature = 'tours:setup-booking';

    protected $description = 'Configure tarifs, add-ons et disponibilités pour tous les tours (tests réservation)';

    public function handle(): int
    {
        $this->info('Configuration des tours pour la réservation…');

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\TourBookingSetupSeeder',
            '--force' => true,
        ]);

        $this->info('Terminé. Vous pouvez tester /tours/{slug}/booking');

        return self::SUCCESS;
    }
}
