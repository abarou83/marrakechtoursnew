<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tour;
use App\Models\TourDate;
use Carbon\Carbon;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $category1 = Category::create([
            'name' => 'Nature & Aventure',
            'slug' => 'nature-aventure',
            'meta_title' => 'Tours de Nature et Aventure',
            'meta_description' => 'Découvrez des expériences en pleine nature et des aventures exceptionnelles',
        ]);

        $category2 = Category::create([
            'name' => 'Culture & Histoire',
            'slug' => 'culture-histoire',
            'meta_title' => 'Tours Culturels et Historiques',
            'meta_description' => 'Explorez le patrimoine et l\'histoire de différentes régions',
        ]);

        $category3 = Category::create([
            'name' => 'Bien-être & Relaxation',
            'slug' => 'bien-etre-relaxation',
            'meta_title' => 'Tours de Bien-être',
            'meta_description' => 'Profitez de moments de détente et de relaxation',
        ]);

        // Tour 1
        $tour1 = Tour::create([
            'category_id' => $category1->id,
            'title' => 'Safari dans le Parc National',
            'slug' => 'safari-parc-national',
            'description' => 'Une aventure inoubliable à la découverte de la faune sauvage. Explorez les vastes étendues du parc national et observez les animaux dans leur habitat naturel. Guide expérimenté et véhicule tout-terrain inclus.',
            'location' => 'Parc National de Serengeti',
            'duration' => '3 jours / 2 nuits',
            'price' => 450.00,
            'capacity' => 20,
            'meta_title' => 'Safari dans le Parc National - Aventure Africaine',
            'meta_description' => 'Découvrez la faune sauvage africaine lors d\'un safari inoubliable',
            'status' => 'published',
        ]);

        // Dates for Tour 1
        TourDate::create(['tour_id' => $tour1->id, 'start_at' => Carbon::now()->addDays(7), 'end_at' => Carbon::now()->addDays(9), 'capacity' => 20]);
        TourDate::create(['tour_id' => $tour1->id, 'start_at' => Carbon::now()->addDays(15), 'end_at' => Carbon::now()->addDays(17), 'capacity' => 20]);
        TourDate::create(['tour_id' => $tour1->id, 'start_at' => Carbon::now()->addDays(22), 'end_at' => Carbon::now()->addDays(24), 'capacity' => 20]);

        // Tour 2
        $tour2 = Tour::create([
            'category_id' => $category1->id,
            'title' => 'Randonnée Montagne avec guide',
            'slug' => 'randonnee-montagne-guide',
            'description' => 'Escaladez les plus hauts sommets avec nos guides de montagne professionnels. Matériel fourni, nuits en refuge de montagne, vues panoramiques à couper le souffle.',
            'location' => 'Montagnes des Alpes',
            'duration' => '5 jours / 4 nuits',
            'price' => 750.00,
            'capacity' => 15,
            'meta_title' => 'Randonnée en Montagne - Aventure Alpine',
            'meta_description' => 'Explorez les plus beaux sommets avec des guides expérimentés',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour2->id, 'start_at' => Carbon::now()->addDays(10), 'end_at' => Carbon::now()->addDays(14), 'capacity' => 15]);
        TourDate::create(['tour_id' => $tour2->id, 'start_at' => Carbon::now()->addDays(25), 'end_at' => Carbon::now()->addDays(29), 'capacity' => 15]);

        // Tour 3
        $tour3 = Tour::create([
            'category_id' => $category1->id,
            'title' => 'Plongée sous-marine en eaux tropicales',
            'slug' => 'plongee-sous-marine-tropiques',
            'description' => 'Explorez les récifs coralliens colorés et la vie marine exotique. Certificat PADI inclus pour les débutants. Équipement professionnel fourni.',
            'location' => 'Maldives',
            'duration' => '4 jours / 3 nuits',
            'price' => 650.00,
            'capacity' => 12,
            'meta_title' => 'Plongée sous-marine - Aventure Marine',
            'meta_description' => 'Découvrez les merveilles sous-marines des Maldives',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour3->id, 'start_at' => Carbon::now()->addDays(14), 'end_at' => Carbon::now()->addDays(17), 'capacity' => 12]);
        TourDate::create(['tour_id' => $tour3->id, 'start_at' => Carbon::now()->addDays(28), 'end_at' => Carbon::now()->addDays(31), 'capacity' => 12]);

        // Tour 4
        $tour4 = Tour::create([
            'category_id' => $category2->id,
            'title' => 'Visite guidée des monuments historiques',
            'slug' => 'visite-monuments-historiques',
            'description' => 'Découvrez les sites historiques emblématiques avec des guides experts. Accès coupe-file, déjeuner gastronomique inclus. Parfait pour les passionnés d\'histoire.',
            'location' => 'Paris, France',
            'duration' => '1 jour',
            'price' => 95.00,
            'capacity' => 25,
            'meta_title' => 'Visite Paris - Patrimoine Historique',
            'meta_description' => 'Explorez le patrimoine historique de Paris avec des guides experts',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour4->id, 'start_at' => Carbon::now()->addDays(3), 'end_at' => Carbon::now()->addDays(3)->addHours(8), 'capacity' => 25]);
        TourDate::create(['tour_id' => $tour4->id, 'start_at' => Carbon::now()->addDays(7), 'end_at' => Carbon::now()->addDays(7)->addHours(8), 'capacity' => 25]);
        TourDate::create(['tour_id' => $tour4->id, 'start_at' => Carbon::now()->addDays(10), 'end_at' => Carbon::now()->addDays(10)->addHours(8), 'capacity' => 25]);

        // Tour 5
        $tour5 = Tour::create([
            'category_id' => $category2->id,
            'title' => 'Circuit culturel en Asie',
            'slug' => 'circuit-culturel-asie',
            'description' => 'Voyagez à travers les temples anciens, les marchés animés et découvrez les traditions millénaires. Hébergement 4 étoiles, tous les repas inclus.',
            'location' => 'Japon, Thaïlande, Cambodge',
            'duration' => '10 jours / 9 nuits',
            'price' => 1850.00,
            'capacity' => 18,
            'meta_title' => 'Circuit Asie - Culture Orientale',
            'meta_description' => 'Immersion totale dans la culture asiatique',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour5->id, 'start_at' => Carbon::now()->addDays(30), 'end_at' => Carbon::now()->addDays(39), 'capacity' => 18]);
        TourDate::create(['tour_id' => $tour5->id, 'start_at' => Carbon::now()->addDays(60), 'end_at' => Carbon::now()->addDays(69), 'capacity' => 18]);

        // Tour 6
        $tour6 = Tour::create([
            'category_id' => $category2->id,
            'title' => 'Musées et Art Modern',
            'slug' => 'musees-art-moderne',
            'description' => 'Explorez les plus grands musées d\'art du monde. Entrées VIP, visites privées, repas dans les restaurants des musées.',
            'location' => 'Londres, Paris, Madrid',
            'duration' => '6 jours / 5 nuits',
            'price' => 1250.00,
            'capacity' => 16,
            'meta_title' => 'Tour Musées - Art et Culture Européenne',
            'meta_description' => 'Découvrez l\'art à travers les grands musées européens',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour6->id, 'start_at' => Carbon::now()->addDays(20), 'end_at' => Carbon::now()->addDays(25), 'capacity' => 16]);

        // Tour 7
        $tour7 = Tour::create([
            'category_id' => $category3->id,
            'title' => 'Retraite Spa et Bien-être',
            'slug' => 'retraite-spa-bien-etre',
            'description' => 'Détendez-vous dans un spa de luxe avec massages, soins du visage, jacuzzi et saunas. Petit-déjeuner bio, cours de yoga matinal inclus.',
            'location' => 'Station thermale des Alpes',
            'duration' => '3 jours / 2 nuits',
            'price' => 420.00,
            'capacity' => 30,
            'meta_title' => 'Retraite Spa - Détente et Bien-être',
            'meta_description' => 'Offrez-vous une pause détente dans un spa de luxe',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour7->id, 'start_at' => Carbon::now()->addDays(5), 'end_at' => Carbon::now()->addDays(7), 'capacity' => 30]);
        TourDate::create(['tour_id' => $tour7->id, 'start_at' => Carbon::now()->addDays(12), 'end_at' => Carbon::now()->addDays(14), 'capacity' => 30]);
        TourDate::create(['tour_id' => $tour7->id, 'start_at' => Carbon::now()->addDays(19), 'end_at' => Carbon::now()->addDays(21), 'capacity' => 30]);

        // Tour 8
        $tour8 = Tour::create([
            'category_id' => $category3->id,
            'title' => 'Yoga et Méditation en Montagne',
            'slug' => 'yoga-meditation-montagne',
            'description' => 'Retraite spirituelle en montagne avec cours de yoga quotidien, méditation, alimentation saine et bains de forêt. Hébergement écologique.',
            'location' => 'Ashram des Himalayas',
            'duration' => '7 jours / 6 nuits',
            'price' => 580.00,
            'capacity' => 20,
            'meta_title' => 'Yoga en Montagne - Retraite Spirituelle',
            'meta_description' => 'Retrouvez votre paix intérieure en montagne',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour8->id, 'start_at' => Carbon::now()->addDays(18), 'end_at' => Carbon::now()->addDays(24), 'capacity' => 20]);
        TourDate::create(['tour_id' => $tour8->id, 'start_at' => Carbon::now()->addDays(45), 'end_at' => Carbon::now()->addDays(51), 'capacity' => 20]);

        // Tour 9
        $tour9 = Tour::create([
            'category_id' => $category3->id,
            'title' => 'Randonnée Thalasso',
            'slug' => 'randonnee-thalasso',
            'description' => 'Combine randonnée douce au bord de mer et thalassothérapie. Soins marins, séances de kinésithérapie aquatique, repas santé.',
            'location' => 'Côte Atlantique',
            'duration' => '5 jours / 4 nuits',
            'price' => 490.00,
            'capacity' => 22,
            'meta_title' => 'Thalasso Marche - Bien-être Maritime',
            'meta_description' => 'Médecine naturelle de la mer et randonnée côtière',
            'status' => 'published',
        ]);

        TourDate::create(['tour_id' => $tour9->id, 'start_at' => Carbon::now()->addDays(8), 'end_at' => Carbon::now()->addDays(12), 'capacity' => 22]);
        TourDate::create(['tour_id' => $tour9->id, 'start_at' => Carbon::now()->addDays(22), 'end_at' => Carbon::now()->addDays(26), 'capacity' => 22]);

        $this->command->info('✅ ' . Tour::count() . ' tours créés avec succès!');
        $this->command->info('✅ ' . TourDate::count() . ' dates de tour créées!');
    }
}
