<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourAvailability;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TourAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::where('is_active', true)->get();

        if ($tours->isEmpty()) {
            $this->command->warn('No active tours found. Skipping availability seeding.');
            return;
        }

        $startDate = Carbon::today();
        $endDate = Carbon::today()->addMonths(6);

        $closedWeekdays = [];

        foreach ($tours as $tour) {
            $this->command->info("Creating availabilities for tour: {$tour->title}");

            $current = $startDate->copy();
            $created = 0;

            while ($current->lte($endDate)) {
                if (in_array($current->dayOfWeek, $closedWeekdays)) {
                    $current->addDay();
                    continue;
                }

                $spotsTotal = $tour->capacity ?? rand(12, 20);

                $isAvailable = true;
                if ($current->isPast()) {
                    $isAvailable = false;
                }

                $spotsAvailable = $isAvailable ? $spotsTotal : 0;
                if ($isAvailable && rand(1, 10) <= 3) {
                    $spotsAvailable = rand(0, (int) ($spotsTotal * 0.3));
                    if ($spotsAvailable === 0) {
                        $isAvailable = false;
                    }
                }

                $priceOverride = null;
                if (in_array($current->month, [7, 8, 12])) {
                    $priceOverride = null;
                }

                TourAvailability::updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'date' => $current->format('Y-m-d'),
                    ],
                    [
                        'spots_total' => $spotsTotal,
                        'spots_available' => $spotsAvailable,
                        'is_available' => $isAvailable,
                        'price_override' => $priceOverride,
                    ]
                );

                $created++;
                $current->addDay();
            }

            $this->command->info("  Created {$created} availability entries.");
        }

        $this->command->info('Tour availabilities seeded successfully!');
    }
}
