<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @deprecated Utiliser TourBookingSetupSeeder ou `php artisan tours:setup-booking`
 */
class PricingSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TourBookingSetupSeeder::class);
    }
}
