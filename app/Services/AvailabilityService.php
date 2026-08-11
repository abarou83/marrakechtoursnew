<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AvailabilityService
{
    /**
     * Check if a tour is available for a given date and number of people
     */
    public function isAvailable(Tour $tour, Carbon $date, int $spotsNeeded): bool
    {
        $availability = $this->getAvailability($tour, $date);

        if (!$availability || !$availability->is_available) {
            return false;
        }

        return $availability->spots_available >= $spotsNeeded;
    }

    /**
     * Get availability for a tour on a specific date
     */
    public function getAvailability(Tour $tour, Carbon $date): ?TourAvailability
    {
        $cacheKey = "availability:{$tour->id}:{$date->format('Y-m-d')}";

        return Cache::tags(['availabilities', "tour:{$tour->id}"])
            ->remember($cacheKey, 300, function () use ($tour, $date) {
                return TourAvailability::where('tour_id', $tour->id)
                    ->whereDate('date', $date)
                    ->first();
            });
    }

    /**
     * Get availabilities for a tour within a date range
     */
    public function getAvailabilitiesInRange(Tour $tour, Carbon $startDate, Carbon $endDate): array
    {
        $availabilities = TourAvailability::where('tour_id', $tour->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $result = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $availability = $availabilities->firstWhere('date', $dateStr);

            $result[$dateStr] = [
                'date' => $dateStr,
                'is_available' => $availability ? $availability->is_available : false,
                'spots_total' => $availability ? $availability->spots_total : 0,
                'spots_available' => $availability ? $availability->spots_available : 0,
                'price_override' => $availability ? $availability->price_override : null,
            ];

            $current->addDay();
        }

        return $result;
    }

    /**
     * Reserve spots with pessimistic locking (anti double-booking)
     * CRITICAL: Always use this within a transaction
     */
    public function reserveSpots(Tour $tour, Carbon $date, int $spots): TourAvailability
    {
        return DB::transaction(function () use ($tour, $date, $spots) {
            $availability = TourAvailability::where('tour_id', $tour->id)
                ->whereDate('date', $date)
                ->lockForUpdate()
                ->first();

            if (!$availability) {
                throw new \Exception(__('Aucune disponibilité trouvée pour cette date.'));
            }

            if (!$availability->is_available) {
                throw new \Exception(__('Ce tour n\'est pas disponible à cette date.'));
            }

            if ($availability->spots_available < $spots) {
                throw new \Exception(
                    __('Seulement :available place(s) disponible(s), vous avez demandé :requested.', [
                        'available' => $availability->spots_available,
                        'requested' => $spots,
                    ])
                );
            }

            $availability->spots_available -= $spots;
            $availability->save();

            $this->invalidateCache($tour->id, $date);

            Log::info('Spots reserved', [
                'tour_id' => $tour->id,
                'date' => $date->format('Y-m-d'),
                'spots_reserved' => $spots,
                'spots_remaining' => $availability->spots_available,
            ]);

            return $availability;
        }, 5);
    }

    /**
     * Release spots (for cancellation/refund)
     */
    public function releaseSpots(Tour $tour, Carbon $date, int $spots): TourAvailability
    {
        return DB::transaction(function () use ($tour, $date, $spots) {
            $availability = TourAvailability::where('tour_id', $tour->id)
                ->whereDate('date', $date)
                ->lockForUpdate()
                ->first();

            if (!$availability) {
                throw new \Exception(__('Aucune disponibilité trouvée pour cette date.'));
            }

            $newSpots = min(
                $availability->spots_available + $spots,
                $availability->spots_total
            );

            $availability->spots_available = $newSpots;
            $availability->save();

            $this->invalidateCache($tour->id, $date);

            Log::info('Spots released', [
                'tour_id' => $tour->id,
                'date' => $date->format('Y-m-d'),
                'spots_released' => $spots,
                'spots_available' => $availability->spots_available,
            ]);

            return $availability;
        }, 5);
    }

    /**
     * Create or update availability for a tour date
     */
    public function setAvailability(
        Tour $tour,
        Carbon $date,
        int $spotsTotal,
        bool $isAvailable = true,
        ?float $priceOverride = null
    ): TourAvailability {
        $availability = TourAvailability::updateOrCreate(
            [
                'tour_id' => $tour->id,
                'date' => $date->format('Y-m-d'),
            ],
            [
                'spots_total' => $spotsTotal,
                'spots_available' => $spotsTotal,
                'is_available' => $isAvailable,
                'price_override' => $priceOverride,
            ]
        );

        $this->invalidateCache($tour->id, $date);

        return $availability;
    }

    /**
     * Generate availabilities for a tour over a date range
     */
    public function generateAvailabilities(
        Tour $tour,
        Carbon $startDate,
        Carbon $endDate,
        int $defaultSpots,
        array $excludedWeekdays = [],
        array $excludedDates = []
    ): int {
        $created = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');

            if (in_array($current->dayOfWeek, $excludedWeekdays)) {
                $current->addDay();
                continue;
            }

            if (in_array($dateStr, $excludedDates)) {
                $current->addDay();
                continue;
            }

            $exists = TourAvailability::where('tour_id', $tour->id)
                ->whereDate('date', $current)
                ->exists();

            if (!$exists) {
                TourAvailability::create([
                    'tour_id' => $tour->id,
                    'date' => $dateStr,
                    'spots_total' => $defaultSpots,
                    'spots_available' => $defaultSpots,
                    'is_available' => true,
                ]);
                $created++;
            }

            $current->addDay();
        }

        Cache::tags(["tour:{$tour->id}"])->flush();

        return $created;
    }

    /**
     * Close expired availabilities (past dates)
     */
    public function closeExpiredAvailabilities(): int
    {
        $affected = TourAvailability::where('date', '<', now()->startOfDay())
            ->where('is_available', true)
            ->update(['is_available' => false]);

        if ($affected > 0) {
            Cache::tags(['availabilities'])->flush();
        }

        return $affected;
    }

    /**
     * Check booking deadline for a tour date
     */
    public function isWithinBookingDeadline(Tour $tour, Carbon $date): bool
    {
        $deadlineHours = $tour->booking_deadline_hours ?? 24;
        $deadline = $date->copy()->subHours($deadlineHours);

        return now()->lt($deadline);
    }

    /**
     * Invalidate cache for a specific tour/date
     */
    protected function invalidateCache(int $tourId, Carbon $date): void
    {
        $cacheKey = "availability:{$tourId}:{$date->format('Y-m-d')}";
        Cache::tags(['availabilities', "tour:{$tourId}"])->forget($cacheKey);
    }
}
