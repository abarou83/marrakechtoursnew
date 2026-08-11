<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'rate_to_base' => 1.000000,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'rate_to_base' => 1.080000, // exemple par rapport à EUR
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'MAD',
                'name' => 'Moroccan Dirham',
                'symbol' => 'MAD',
                'rate_to_base' => 11.000000, // exemple par rapport à EUR
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($data as $row) {
            Currency::updateOrCreate(['code' => $row['code']], $row);
        }
    }
}
