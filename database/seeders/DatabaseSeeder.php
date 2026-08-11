<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            TourSeeder::class,
            AddonSeeder::class,
            TourBookingSetupSeeder::class,
            TourDateSeeder::class,
            TourAvailabilitySeeder::class,
            PromoCodeSeeder::class,
            PageSeeder::class,
            FAQSeeder::class,
            DestinationSeeder::class,
            ClientSeeder::class,
            ReviewSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->command->info('Database seeded successfully!');
    }
}
