<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\TourDate;
use Carbon\Carbon;

class TourDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les tours
        $tours = Tour::all();

        if ($tours->isEmpty()) {
            $this->command->warn('Aucun tour trouvé. Veuillez d\'abord créer des tours.');
            return;
        }

        // Heures de départ communes pour les tours
        $commonDepartureTimes = [
            '09:00',
            '10:00',
            '11:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
        ];

        // Heures de fin correspondantes (2 heures après le départ par défaut)
        $endTimes = [
            '09:00' => '11:00',
            '10:00' => '12:00',
            '11:00' => '13:00',
            '14:00' => '16:00',
            '15:00' => '17:00',
            '16:00' => '18:00',
            '17:00' => '19:00',
        ];

        $createdCount = 0;

        foreach ($tours as $tour) {
            // Créer des heures de départ pour les 30 prochains jours
            for ($day = 0; $day < 30; $day++) {
                $date = Carbon::today()->addDays($day);
                
                // Pour chaque jour, créer 3-5 heures de départ aléatoires
                $timesForDay = collect($commonDepartureTimes)->random(rand(3, 5));
                
                foreach ($timesForDay as $departureTime) {
                    // Vérifier si cette heure existe déjà pour cette date
                    $exists = TourDate::where('tour_id', $tour->id)
                        ->whereDate('start_at', $date)
                        ->whereTime('start_at', $departureTime)
                        ->exists();
                    
                    if (!$exists) {
                        $startAt = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $departureTime);
                        $endTime = $endTimes[$departureTime] ?? Carbon::parse($departureTime)->addHours(2)->format('H:i');
                        $endAt = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $endTime);
                        
                        TourDate::create([
                            'tour_id' => $tour->id,
                            'start_at' => $startAt,
                            'end_at' => $endAt,
                            'capacity' => $tour->capacity ?? 20, // Utiliser la capacité du tour ou 20 par défaut
                        ]);
                        
                        $createdCount++;
                    }
                }
            }
        }

        $this->command->info("✅ {$createdCount} heures de départ créées avec succès pour " . $tours->count() . " tour(s).");
    }
}



