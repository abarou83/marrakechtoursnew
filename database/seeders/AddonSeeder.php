<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates demo add-ons for tours with different pricing types
     */
    public function run(): void
    {
        $addons = [
            // Per Person addons
            [
                'name' => 'Lunch',
                'slug' => 'lunch',
                'pricing_type' => 'per_person',
                'base_price' => 15.00,
                'is_active' => true,
            ],
            [
                'name' => 'Dinner',
                'slug' => 'dinner',
                'pricing_type' => 'per_person',
                'base_price' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Breakfast',
                'slug' => 'breakfast',
                'pricing_type' => 'per_person',
                'base_price' => 10.00,
                'is_active' => true,
            ],
            [
                'name' => 'Travel Insurance',
                'slug' => 'travel-insurance',
                'pricing_type' => 'per_person',
                'base_price' => 8.00,
                'is_active' => true,
            ],
            [
                'name' => 'Audio Guide',
                'slug' => 'audio-guide',
                'pricing_type' => 'per_person',
                'base_price' => 5.00,
                'is_active' => true,
            ],
            
            // Per Group addons
            [
                'name' => 'Private Guide',
                'slug' => 'private-guide',
                'pricing_type' => 'per_group',
                'base_price' => 120.00,
                'is_active' => true,
            ],
            [
                'name' => 'Camel Ride',
                'slug' => 'camel-ride',
                'pricing_type' => 'per_group',
                'base_price' => 80.00,
                'is_active' => true,
            ],
            [
                'name' => 'Quad Bike',
                'slug' => 'quad-bike',
                'pricing_type' => 'per_group',
                'base_price' => 150.00,
                'is_active' => true,
            ],
            [
                'name' => 'Hot Air Balloon',
                'slug' => 'hot-air-balloon',
                'pricing_type' => 'per_group',
                'base_price' => 200.00,
                'is_active' => true,
            ],
            [
                'name' => 'Private Transport',
                'slug' => 'private-transport',
                'pricing_type' => 'per_group',
                'base_price' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Photography Session',
                'slug' => 'photography-session',
                'pricing_type' => 'per_group',
                'base_price' => 75.00,
                'is_active' => true,
            ],
            
            // Free addons
            [
                'name' => 'Bottled Water',
                'slug' => 'bottled-water',
                'pricing_type' => 'free',
                'base_price' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'City Map',
                'slug' => 'city-map',
                'pricing_type' => 'free',
                'base_price' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sunscreen',
                'slug' => 'sunscreen',
                'pricing_type' => 'free',
                'base_price' => 0.00,
                'is_active' => true,
            ],
        ];

        foreach ($addons as $addonData) {
            Addon::updateOrCreate(
                ['slug' => $addonData['slug']],
                $addonData
            );
        }

        $this->command->info('✅ Created ' . count($addons) . ' demo add-ons');
    }
}
