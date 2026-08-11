<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les tours
        $tours = Tour::all();
        
        // Récupérer tous les utilisateurs (ou créer un utilisateur de test si nécessaire)
        $users = User::all();
        
        if ($tours->isEmpty()) {
            $this->command->warn('Aucun tour trouvé. Créez d\'abord des tours avant de créer des avis.');
            return;
        }
        
        if ($users->isEmpty()) {
            // Créer un utilisateur de test pour les avis
            $user = User::create([
                'name' => 'Visiteur Test',
                'email' => 'visiteur@test.com',
                'password' => bcrypt('password'),
            ]);
            $users = collect([$user]);
        }

        // Avis de démonstration
        $demoReviews = [
            [
                'rating' => 5,
                'comment' => 'Fantastique ! Le coupe-file nous a fait gagner un temps précieux. Le guide était passionné et a su rendre l\'histoire de l\'art accessible et amusante. Voir la Joconde sans la foule était un rêve !',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => 'Un moyen parfait de voir le Louvre. Le groupe était de taille idéale. La sélection des œuvres était pertinente pour une première visite. Je le referais sans hésiter.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => 'Très bonne expérience ! Le guide était compétent et répondait à toutes nos questions. Seul bémol : un peu court pour voir toutes les œuvres majeures.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => 'Expérience exceptionnelle ! Le billet coupe-file vaut vraiment le coup. Nous avons passé un moment inoubliable. Je recommande vivement cette visite.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => 'Superbe visite guidée ! Le guide connaissait parfaitement l\'histoire de chaque œuvre. Nous avons appris beaucoup de choses intéressantes.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => 'Incroyable ! Une visite qui dépasse toutes mes attentes. Le guide était excellent et l\'organisation parfaite. À refaire absolument !',
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'comment' => 'Visite correcte mais un peu décevante. Le guide parlait trop vite et il était difficile de tout suivre. Le musée était très bondé ce jour-là.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => 'Très belle expérience ! Les écouteurs permettaient d\'entendre parfaitement le guide même dans les salles bondées. Je recommande.',
                'is_approved' => true,
            ],
        ];

        // Pour chaque tour, créer quelques avis aléatoires
        foreach ($tours as $tour) {
            // Sélectionner un nombre aléatoire d'avis à créer (entre 3 et 6)
            $numberOfReviews = rand(3, min(6, count($demoReviews)));
            
            // Mélanger les avis et en prendre un nombre aléatoire
            $selectedReviews = collect($demoReviews)->shuffle()->take($numberOfReviews);
            
            foreach ($selectedReviews as $reviewData) {
                // Sélectionner un utilisateur aléatoire
                $user = $users->random();
                
                // Créer un date aléatoire dans les 6 derniers mois
                $createdAt = now()->subDays(rand(0, 180));
                
                Review::create([
                    'tour_id' => $tour->id,
                    'user_id' => $user->id,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_approved' => $reviewData['is_approved'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('Avis de démonstration créés avec succès !');
    }
}
