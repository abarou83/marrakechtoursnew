<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un client de démonstration
        Client::firstOrCreate(
            ['email' => 'client@demo.com'],
            [
                'name' => 'Client Démo',
                'email' => 'client@demo.com',
                'password' => Hash::make('password'),
                'phone' => '+33 6 12 34 56 78',
                'address' => '123 Rue de la Démo',
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '75001',
                'email_verified_at' => now(),
            ]
        );

        // Créer quelques clients supplémentaires pour les tests
        Client::firstOrCreate(
            ['email' => 'jean.dupont@example.com'],
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'password' => Hash::make('password'),
                'phone' => '+33 6 98 76 54 32',
                'address' => '45 Avenue des Champs',
                'city' => 'Lyon',
                'country' => 'France',
                'postal_code' => '69001',
                'email_verified_at' => now(),
            ]
        );

        Client::firstOrCreate(
            ['email' => 'marie.martin@example.com'],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+33 6 11 22 33 44',
                'address' => '78 Boulevard de la République',
                'city' => 'Marseille',
                'country' => 'France',
                'postal_code' => '13001',
                'email_verified_at' => now(),
            ]
        );
    }
}
