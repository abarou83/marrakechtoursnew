<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\TourPricing;
use App\Models\TourPromotion;
use App\Models\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer un admin (si n'existe pas)
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        echo "✅ Admin: admin@test.com / password\n";

        // 2. Créer des catégories
        $categories = [
            ['name' => 'Aventure', 'slug' => 'aventure'],
            ['name' => 'Culture', 'slug' => 'culture'],
            ['name' => 'Gastronomie', 'slug' => 'gastronomie'],
            ['name' => 'Nature', 'slug' => 'nature'],
            ['name' => 'Ville', 'slug' => 'ville'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'meta_title' => $cat['name'] . ' - Tours et Activités',
                    'meta_description' => 'Découvrez nos tours et activités ' . strtolower($cat['name']),
                ]
            );
        }

        echo "✅ " . count($categories) . " catégories créées\n";

        // 3. Créer des tours
        $tours = [
            [
                'category' => 'ville',
                'title' => 'Tour de la Tour Eiffel',
                'description' => "Découvrez le monument le plus emblématique de Paris ! Notre visite guidée vous emmène au sommet de la Tour Eiffel avec un guide expert qui vous racontera son histoire fascinante.\n\nVous profiterez d'une vue panoramique à 360° sur tout Paris, incluant les Champs-Élysées, le Louvre, Notre-Dame et bien plus encore.\n\nLa visite comprend l'accès prioritaire (pas de file d'attente), un guide francophone passionné, et du temps libre pour prendre des photos inoubliables.",
                'location' => 'Paris, France',
                'duration' => '2 heures',
                'capacity' => 20,
                'status' => 'published',
                'pricings' => [
                    ['name' => 'Standard', 'price_min' => 49.99, 'min' => 1, 'max' => 1, 'default' => true],
                    ['name' => 'Duo', 'price_min' => 89.99, 'min' => 2, 'max' => 2, 'default' => false],
                    ['name' => 'Famille (4 pers)', 'price_min' => 159.99, 'min' => 4, 'max' => 4, 'default' => false],
                ],
                'promotion' => [
                    'name' => 'Early Bird',
                    'discount_type' => 'percentage',
                    'discount_value' => 20,
                    'badge_text' => '-20%',
                ],
            ],
            [
                'category' => 'culture',
                'title' => 'Visite du Louvre',
                'description' => "Explorez le plus grand musée d'art du monde avec notre guide expert ! Découvrez les chefs-d'œuvre incontournables : la Joconde, la Vénus de Milo, le Sacre de Napoléon et bien d'autres.\n\nNotre guide vous fera voyager à travers l'histoire de l'art, de l'Antiquité à la Renaissance, en passant par l'art islamique et les trésors égyptiens.\n\nBillets coupe-file inclus pour éviter les longues files d'attente. Casques audio fournis pour une expérience optimale.",
                'location' => 'Paris, France',
                'duration' => '3 heures',
                'capacity' => 15,
                'status' => 'published',
                'pricings' => [
                    ['name' => 'Adulte', 'price_min' => 69.99, 'min' => 1, 'max' => null, 'default' => true],
                    ['name' => 'Étudiant', 'price_min' => 49.99, 'min' => 1, 'max' => null, 'default' => false],
                    ['name' => 'Groupe (6+ pers)', 'price_min' => 59.99, 'min' => 6, 'max' => null, 'default' => false],
                ],
            ],
            [
                'category' => 'aventure',
                'title' => 'Randonnée Mont Blanc',
                'description' => "Vivez une aventure inoubliable au cœur des Alpes ! Notre randonnée guidée vous emmène sur les plus beaux sentiers du massif du Mont Blanc.\n\nVous découvrirez des panoramas à couper le souffle, des lacs d'altitude cristallins, et une faune alpine préservée. Notre guide de haute montagne diplômé vous accompagne en toute sécurité.\n\nNiveau intermédiaire requis. Équipement de sécurité fourni (baudrier, casque). Pique-nique inclus avec produits locaux.",
                'location' => 'Chamonix, France',
                'duration' => '1 journée',
                'capacity' => 12,
                'status' => 'published',
                'pricings' => [
                    ['name' => 'Solo', 'price_min' => 89.99, 'min' => 1, 'max' => 1, 'default' => true],
                    ['name' => 'Groupe privé', 'price_min' => 299.99, 'min' => 4, 'max' => 8, 'default' => false],
                ],
                'promotion' => [
                    'name' => 'Promo Hiver',
                    'discount_type' => 'fixed',
                    'discount_value' => 15,
                    'badge_text' => '-15€',
                ],
            ],
            [
                'category' => 'gastronomie',
                'title' => 'Dégustation de Vins à Bordeaux',
                'description' => "Découvrez les secrets des grands crus bordelais ! Notre tour œnologique vous emmène dans les plus prestigieux châteaux de la région.\n\nVous visiterez 3 domaines viticoles réputés, découvrirez leurs caves centenaires, et dégusterez 6 vins d'exception accompagnés de fromages locaux.\n\nNotre sommelier expert vous apprendra à reconnaître les arômes, évaluer la robe, et apprécier chaque vin. Transport en minibus confortable inclus.",
                'location' => 'Bordeaux, France',
                'duration' => '5 heures',
                'capacity' => 10,
                'status' => 'published',
                'pricings' => [
                    ['name' => 'Standard', 'price_min' => 129.99, 'min' => 1, 'max' => null, 'default' => true],
                    ['name' => 'VIP (déjeuner inclus)', 'price_min' => 199.99, 'min' => 1, 'max' => null, 'default' => false],
                ],
            ],
            [
                'category' => 'nature',
                'title' => 'Safari Photo en Camargue',
                'description' => "Partez à la découverte de la Camargue sauvage ! Notre safari photo vous permet d'observer flamants roses, chevaux blancs, taureaux noirs et plus de 400 espèces d'oiseaux.\n\nÀ bord d'un véhicule 4x4 tout terrain, notre guide naturaliste vous emmène dans les zones les plus préservées du parc naturel régional.\n\nConseils photo personnalisés, prêt de jumelles, pauses dans des points de vue spectaculaires. Rafraîchissements inclus.",
                'location' => 'Arles, France',
                'duration' => '4 heures',
                'capacity' => 8,
                'status' => 'published',
                'pricings' => [
                    ['name' => 'Adulte', 'price_min' => 79.99, 'min' => 1, 'max' => null, 'default' => true],
                    ['name' => 'Enfant (6-12 ans)', 'price_min' => 39.99, 'min' => 1, 'max' => null, 'default' => false],
                    ['name' => 'Famille (2 adultes + 2 enfants)', 'price_min' => 239.99, 'min' => 4, 'max' => 4, 'default' => false],
                ],
            ],
        ];

        foreach ($tours as $tourData) {
            // Créer le tour
            $tour = Tour::create([
                'category_id' => $categoryModels[$tourData['category']]->id,
                'title' => $tourData['title'],
                'slug' => Str::slug($tourData['title']),
                'description' => $tourData['description'],
                'location' => $tourData['location'],
                'duration' => $tourData['duration'],
                'price' => null, // Plus utilisé
                'capacity' => $tourData['capacity'],
                'status' => $tourData['status'],
                'meta_title' => $tourData['title'] . ' | Tourify',
                'meta_description' => substr($tourData['description'], 0, 160),
                'meta_keywords' => $tourData['title'] . ', ' . $tourData['location'],
            ]);

            // Créer les tarifs
            foreach ($tourData['pricings'] as $pricingData) {
                TourPricing::create([
                    'tour_id' => $tour->id,
                    'name' => $pricingData['name'],
                    'description' => 'Formule ' . $pricingData['name'],
                    'price_min' => $pricingData['price_min'],
                    'price_max' => $pricingData['price_min'],
                    'currency' => 'EUR',
                    'min_participants' => $pricingData['min'],
                    'max_participants' => $pricingData['max'],
                    'is_default' => $pricingData['default'],
                    'is_active' => true,
                    'order' => 0,
                ]);
            }

            // Créer la promotion si elle existe
            if (isset($tourData['promotion'])) {
                $promo = $tourData['promotion'];
                TourPromotion::create([
                    'tour_id' => $tour->id,
                    'name' => $promo['name'],
                    'description' => 'Promotion ' . $promo['name'],
                    'discount_type' => $promo['discount_type'],
                    'discount_value' => $promo['discount_value'],
                    'start_date' => now(),
                    'end_date' => now()->addMonths(2),
                    'usage_limit' => null,
                    'used_count' => 0,
                    'badge_text' => $promo['badge_text'],
                    'badge_color' => 'red',
                    'is_active' => true,
                ]);
            }

            // Créer des dates de tour
            for ($i = 0; $i < 10; $i++) {
                TourDate::create([
                    'tour_id' => $tour->id,
                    'start_at' => now()->addDays(3 + $i * 3)->setTime(10, 0),
                    'end_at' => now()->addDays(3 + $i * 3)->setTime(12, 0),
                    'capacity' => $tour->capacity,
                ]);
            }

            echo "✅ Tour créé: {$tour->title}\n";
        }

        echo "\n";
        echo "====================================\n";
        echo "✅ BASE DE DONNÉES REMPLIE !\n";
        echo "====================================\n";
        echo "\n";
        echo "📊 Résumé:\n";
        echo "- " . Category::count() . " catégories\n";
        echo "- " . Tour::count() . " tours\n";
        echo "- " . TourPricing::count() . " tarifs\n";
        echo "- " . TourPromotion::count() . " promotions\n";
        echo "- " . TourDate::count() . " dates\n";
        echo "\n";
        echo "🔐 Admin:\n";
        echo "Email: admin@test.com\n";
        echo "Mot de passe: password\n";
        echo "\n";
        echo "🌐 Accédez au site:\n";
        echo "Frontend: http://localhost/\n";
        echo "Admin: http://localhost/admin/dashboard\n";
        echo "\n";
    }
}

