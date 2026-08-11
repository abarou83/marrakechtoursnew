<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Tour;
use App\Models\TourAddon;
use App\Models\TourAvailability;
use App\Models\TourDate;
use App\Models\TourGroupPrice;
use App\Models\TourPricing;
use App\Models\TourPrivatePrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Rend tous les tours réservables : tarifs (group/private × saisons), add-ons, disponibilités.
 * Idempotent : peut être relancé sans dupliquer les formules.
 */
class TourBookingSetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AddonSeeder::class);

        $tours = Tour::query()->orderBy('id')->get();

        if ($tours->isEmpty()) {
            $this->command?->warn('Aucun tour — exécutez TourSeeder d\'abord.');

            return;
        }

        $addonCatalog = Addon::query()->where('is_active', true)->get()->keyBy('slug');

        $defaultAddonSlugs = [
            'lunch',
            'breakfast',
            'travel-insurance',
            'private-transport',
            'bottled-water',
            'audio-guide',
            'photography-session',
        ];

        $pricingCount = 0;
        $addonLinkCount = 0;

        foreach ($tours as $tour) {
            $this->prepareTourRecord($tour);

            $baseAdult = (float) ($tour->price ?? 0);
            if ($baseAdult <= 0) {
                $baseAdult = 99.0;
            }

            $seasonMultipliers = [
                'low' => 0.90,
                'normal' => 1.00,
                'high' => 1.15,
            ];

            foreach ($seasonMultipliers as $season => $multiplier) {
                $adult = round($baseAdult * $multiplier, 2);
                $child = round($adult * 0.55, 2);
                $infant = 0.0;

                $groupPricing = TourPricing::query()->updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'pricing_mode' => 'group',
                        'season' => $season,
                    ],
                    [
                        'title' => 'Groupe — '.ucfirst($season),
                        'is_active' => true,
                    ]
                );
                $pricingCount++;

                $this->upsertGroupPrices($groupPricing->id, $adult, $child, $infant);
                $addonLinkCount += $this->syncPricingAddons($groupPricing, $addonCatalog, $defaultAddonSlugs);

                $privatePricing = TourPricing::query()->updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'pricing_mode' => 'private',
                        'season' => $season,
                    ],
                    [
                        'title' => 'Privé — '.ucfirst($season),
                        'is_active' => true,
                    ]
                );
                $pricingCount++;

                $this->upsertPrivatePrices($privatePricing->id, $adult);
                $addonLinkCount += $this->syncPricingAddons($privatePricing, $addonCatalog, $defaultAddonSlugs);
            }

            $this->syncTourAddons($tour, $addonCatalog, $defaultAddonSlugs);
            $this->seedAvailability($tour);
            $this->ensureUpcomingTourDates($tour);
        }

        Cache::flush();

        $this->command?->info('✅ Tarifs & réservation : '.$tours->count().' tour(s) configurés.');
        $this->command?->info('   Formules (créées/mises à jour) : ~'.$pricingCount);
        $this->command?->info('   Liens pricing_addons : '.$addonLinkCount);
    }

    private function prepareTourRecord(Tour $tour): void
    {
        $type = $tour->type;
        if (! $type) {
            $duration = strtolower((string) $tour->duration);
            if (str_contains($duration, '1 jour') || str_contains($duration, '1 jour')) {
                $type = 'daytrip';
            } elseif (preg_match('/(\d+)\s*jours/', $duration, $m) && (int) $m[1] >= 7) {
                $type = 'circuit';
            } elseif (str_contains($duration, 'jour')) {
                $type = 'excursion';
            } else {
                $type = 'activity';
            }
        }

        $tour->update([
            'is_active' => true,
            'status' => $tour->status ?: 'published',
            'type' => $type,
            'capacity' => $tour->capacity ?: 20,
        ]);
    }

    private function upsertGroupPrices(int $pricingId, float $adult, float $child, float $infant): void
    {
        $rows = [
            ['category' => 'adult', 'age_min' => 12, 'age_max' => null, 'price' => $adult],
            ['category' => 'child', 'age_min' => 3, 'age_max' => 11, 'price' => $child],
            ['category' => 'infant', 'age_min' => 0, 'age_max' => 2, 'price' => $infant],
        ];

        foreach ($rows as $row) {
            TourGroupPrice::query()->updateOrCreate(
                [
                    'tour_pricing_id' => $pricingId,
                    'category' => $row['category'],
                ],
                [
                    'age_min' => $row['age_min'],
                    'age_max' => $row['age_max'],
                    'price' => $row['price'],
                ]
            );
        }
    }

    private function upsertPrivatePrices(int $pricingId, float $adultBase): void
    {
        $tiers = [
            ['min_people' => 1, 'max_people' => 3, 'multiplier' => 2.2],
            ['min_people' => 4, 'max_people' => 7, 'multiplier' => 3.6],
            ['min_people' => 8, 'max_people' => 12, 'multiplier' => 5.5],
        ];

        foreach ($tiers as $tier) {
            TourPrivatePrice::query()->updateOrCreate(
                [
                    'tour_pricing_id' => $pricingId,
                    'min_people' => $tier['min_people'],
                    'max_people' => $tier['max_people'],
                ],
                [
                    'price' => round($adultBase * $tier['multiplier'], 2),
                ]
            );
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Addon>  $addonCatalog
     * @param  list<string>  $slugs
     */
    private function syncPricingAddons(TourPricing $pricing, $addonCatalog, array $slugs): int
    {
        $linked = 0;

        foreach ($slugs as $slug) {
            $addon = $addonCatalog->get($slug);
            if (! $addon) {
                continue;
            }

            $isIncluded = $slug === 'bottled-water';
            $isRequired = $slug === 'lunch' && $pricing->pricing_mode === 'group' && $pricing->season === 'normal';

            DB::table('pricing_addons')->updateOrInsert(
                [
                    'tour_pricing_id' => $pricing->id,
                    'addon_id' => $addon->id,
                ],
                [
                    'is_required' => $isRequired,
                    'is_included' => $isIncluded,
                    'override_price' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $linked++;
        }

        return $linked;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Addon>  $addonCatalog
     * @param  list<string>  $slugs
     */
    private function syncTourAddons(Tour $tour, $addonCatalog, array $slugs): void
    {
        foreach (array_slice($slugs, 0, 5) as $slug) {
            $addon = $addonCatalog->get($slug);
            if (! $addon) {
                continue;
            }

            TourAddon::query()->updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'addon_id' => $addon->id,
                ],
                [
                    'is_required' => false,
                    'override_price' => null,
                ]
            );
        }
    }

    private function seedAvailability(Tour $tour): void
    {
        $start = Carbon::today();
        $end = Carbon::today()->addMonths(6);
        $spotsTotal = (int) ($tour->capacity ?: 20);

        $current = $start->copy();

        while ($current->lte($end)) {
            TourAvailability::query()->updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'date' => $current->format('Y-m-d'),
                ],
                [
                    'spots_total' => $spotsTotal,
                    'spots_available' => $spotsTotal,
                    'is_available' => true,
                    'price_override' => null,
                ]
            );
            $current->addDay();
        }
    }

    private function ensureUpcomingTourDates(Tour $tour): void
    {
        $times = ['09:00', '10:00', '14:00'];
        $capacity = (int) ($tour->capacity ?: 20);

        for ($day = 1; $day <= 45; $day++) {
            $date = Carbon::today()->addDays($day);
            $departure = $times[$day % count($times)];

            $exists = TourDate::query()
                ->where('tour_id', $tour->id)
                ->whereDate('start_at', $date)
                ->exists();

            if ($exists) {
                continue;
            }

            $startAt = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d').' '.$departure);
            $endAt = $startAt->copy()->addHours(2);

            TourDate::query()->create([
                'tour_id' => $tour->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'capacity' => $capacity,
            ]);
        }
    }
}
