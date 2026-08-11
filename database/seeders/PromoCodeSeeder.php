<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'min_amount' => 50,
                'max_uses' => 1000,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'tour_ids' => null,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER2026',
                'type' => 'percent',
                'value' => 15,
                'min_amount' => 100,
                'max_uses' => 500,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'tour_ids' => null,
                'is_active' => true,
            ],
            [
                'code' => 'DESERT20',
                'type' => 'fixed',
                'value' => 20,
                'min_amount' => 80,
                'max_uses' => 200,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'tour_ids' => null,
                'is_active' => true,
            ],
            [
                'code' => 'GROUP50',
                'type' => 'fixed',
                'value' => 50,
                'min_amount' => 300,
                'max_uses' => 100,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'tour_ids' => null,
                'is_active' => true,
            ],
            [
                'code' => 'EARLYBIRD',
                'type' => 'percent',
                'value' => 20,
                'min_amount' => 150,
                'max_uses' => 50,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addWeeks(2),
                'tour_ids' => null,
                'is_active' => true,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::updateOrCreate(
                ['code' => $promoCode['code']],
                $promoCode
            );
        }

        $this->command->info('Promo codes seeded successfully!');
    }
}
