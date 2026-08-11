<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\AccommodationTranslation;
use App\Models\Addon;
use App\Models\AddonTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Image;
use App\Models\PricingAccommodation;
use App\Models\PricingAddon;
use App\Models\Tour;
use App\Models\TourAddon;
use App\Models\TourDate;
use App\Models\TourGroupPrice;
use App\Models\TourPricing;
use App\Models\TourPricingTranslation;
use App\Models\TourPrivatePrice;
use App\Models\TourPromotion;
use App\Models\TourTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourJsonImportService
{
    public function import(array $payload): array
    {
        if (!isset($payload['tours']) || !is_array($payload['tours'])) {
            throw new \InvalidArgumentException('Le JSON doit contenir une clé "tours".');
        }

        $stats = [
            'categories' => 0,
            'addons' => 0,
            'accommodations' => 0,
            'tours' => 0,
        ];

        DB::transaction(function () use ($payload, &$stats) {
            $stats['categories'] = $this->importCategories($payload['categories_reference'] ?? []);
            $stats['addons'] = $this->importAddons($payload['addons_catalog'] ?? []);
            $stats['accommodations'] = $this->importAccommodations($payload['accommodations_catalog'] ?? []);
            $stats['tours'] = $this->importTours($payload['tours']);
        });

        return $stats;
    }

    private function importCategories(array $categories): int
    {
        $count = 0;

        foreach ($categories as $categoryData) {
            $slug = $categoryData['slug'] ?? null;
            if (!$slug) {
                continue;
            }

            $defaultName = $categoryData['translations']['fr']['name'] ?? $slug;

            $category = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $defaultName]
            );

            foreach (($categoryData['translations'] ?? []) as $locale => $translation) {
                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translation['name'] ?? $defaultName,
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    private function importAddons(array $addons): int
    {
        $count = 0;

        foreach ($addons as $addonData) {
            $slug = $addonData['slug'] ?? null;
            if (!$slug) {
                continue;
            }

            $defaultName = $addonData['name_default'] ?? Str::headline($slug);

            $addon = Addon::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $defaultName,
                    'pricing_type' => $addonData['pricing_type'] ?? 'per_person',
                    'base_price' => $addonData['base_price'] ?? 0,
                    'is_active' => (bool)($addonData['is_active'] ?? true),
                ]
            );

            foreach (($addonData['translations'] ?? []) as $locale => $translation) {
                AddonTranslation::updateOrCreate(
                    [
                        'addon_id' => $addon->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translation['name'] ?? $defaultName,
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    private function importAccommodations(array $accommodations): int
    {
        $count = 0;

        foreach ($accommodations as $accommodationData) {
            $slug = $accommodationData['slug'] ?? null;
            if (!$slug) {
                continue;
            }

            $defaultName = $accommodationData['name_default'] ?? Str::headline($slug);

            $accommodation = Accommodation::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $defaultName,
                    'description' => $accommodationData['description_default'] ?? null,
                    'location' => $accommodationData['location_default'] ?? null,
                    'address' => $accommodationData['address_default'] ?? null,
                    'stars' => $accommodationData['stars'] ?? null,
                    'is_active' => (bool)($accommodationData['is_active'] ?? true),
                ]
            );

            foreach (($accommodationData['translations'] ?? []) as $locale => $translation) {
                AccommodationTranslation::updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translation['name'] ?? $defaultName,
                        'description' => $translation['description'] ?? null,
                        'location' => $translation['location'] ?? null,
                    ]
                );
            }

            foreach (($accommodationData['rooms'] ?? []) as $roomData) {
                $roomType = $roomData['room_type'] ?? null;
                if (!$roomType) {
                    continue;
                }

                AccommodationRoom::updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'room_type' => $roomType,
                    ],
                    [
                        'price_per_night' => $roomData['price_per_night'] ?? 0,
                        'max_occupancy' => $roomData['max_occupancy'] ?? 1,
                        'description' => $roomData['description'] ?? null,
                        'is_active' => (bool)($roomData['is_active'] ?? true),
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    private function importTours(array $tours): int
    {
        $count = 0;

        foreach ($tours as $tourEntry) {
            $tourData = $tourEntry['tour'] ?? null;
            if (!$tourData || !is_array($tourData)) {
                continue;
            }

            $slug = $tourData['slug'] ?? null;
            if (!$slug) {
                continue;
            }

            $tour = Tour::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $tourData['title'] ?? Str::headline($slug),
                    'type' => $tourData['type'] ?? 'activity',
                    'description' => $tourData['description'] ?? null,
                    'location' => $tourData['location'] ?? null,
                    'duration' => $tourData['duration'] ?? null,
                    'price' => $tourData['price'] ?? 0,
                    'capacity' => $tourData['capacity'] ?? 1,
                    'meta_title' => $tourData['meta_title'] ?? null,
                    'meta_description' => $tourData['meta_description'] ?? null,
                    'meta_keywords' => $tourData['meta_keywords'] ?? null,
                    'canonical_url' => $tourData['canonical_url'] ?? null,
                    'og_image' => $tourData['og_image'] ?? null,
                    'focus_keyword' => $tourData['focus_keyword'] ?? null,
                    'status' => $tourData['status'] ?? 'draft',
                    'is_active' => (bool)($tourData['is_active'] ?? true),
                ]
            );

            $this->syncTourCategories($tour, $tourData['category_slugs'] ?? []);
            $this->syncTourTranslations($tour, $tourData['translations'] ?? []);
            $this->syncTourImages($tour, $tourData['images'] ?? []);
            $this->syncTourDates($tour, $tourData['tour_dates'] ?? []);
            $this->syncTourAddons($tour, $tourData['tour_addons'] ?? []);
            $this->syncTourPricings($tour, $tourData['pricings'] ?? []);
            $this->syncTourPromotions($tour, $tourData['promotions'] ?? []);

            $count++;
        }

        return $count;
    }

    private function syncTourCategories(Tour $tour, array $categorySlugs): void
    {
        $categoryIds = Category::query()
            ->whereIn('slug', $categorySlugs)
            ->pluck('id')
            ->toArray();

        if (empty($categoryIds)) {
            return;
        }

        $tour->categories()->sync($categoryIds);
        $tour->update(['category_id' => $categoryIds[0]]);
    }

    private function syncTourTranslations(Tour $tour, array $translations): void
    {
        foreach ($translations as $locale => $translation) {
            TourTranslation::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'] ?? $tour->title,
                    'description' => $translation['description'] ?? ($tour->description ?? ''),
                    'itinerary' => $translation['itinerary'] ?? null,
                    'location' => $translation['location'] ?? ($tour->location ?? ''),
                    'duration' => $translation['duration'] ?? ($tour->duration ?? ''),
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'meta_keywords' => $translation['meta_keywords'] ?? null,
                    'focus_keyword' => $translation['focus_keyword'] ?? null,
                    'canonical_url' => $translation['canonical_url'] ?? null,
                    'og_image' => $translation['og_image'] ?? null,
                ]
            );
        }
    }

    private function syncTourImages(Tour $tour, array $images): void
    {
        if (empty($images)) {
            return;
        }

        $hasPrimary = false;

        foreach ($images as $imageData) {
            $path = $imageData['path'] ?? null;
            if (!$path) {
                continue;
            }

            $isPrimary = (bool)($imageData['is_primary'] ?? false);
            if ($isPrimary) {
                $hasPrimary = true;
            }

            Image::updateOrCreate(
                [
                    'imageable_type' => Tour::class,
                    'imageable_id' => $tour->id,
                    'path' => $path,
                ],
                [
                    'alt' => $imageData['alt'] ?? $tour->title,
                    'is_primary' => $isPrimary,
                ]
            );
        }

        if (!$hasPrimary) {
            $firstImage = $tour->images()->first();
            if ($firstImage) {
                $tour->images()->update(['is_primary' => false]);
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    private function syncTourDates(Tour $tour, array $tourDates): void
    {
        foreach ($tourDates as $dateData) {
            $startAt = $dateData['start_at'] ?? null;
            $endAt = $dateData['end_at'] ?? null;

            if (!$startAt || !$endAt) {
                continue;
            }

            TourDate::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                ],
                [
                    'capacity' => $dateData['capacity'] ?? $tour->capacity,
                ]
            );
        }
    }

    private function syncTourAddons(Tour $tour, array $tourAddons): void
    {
        foreach ($tourAddons as $addonData) {
            $addonSlug = $addonData['addon_slug'] ?? null;
            if (!$addonSlug) {
                continue;
            }

            $addon = Addon::where('slug', $addonSlug)->first();
            if (!$addon) {
                continue;
            }

            TourAddon::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'addon_id' => $addon->id,
                ],
                [
                    'is_required' => (bool)($addonData['is_required'] ?? false),
                    'override_price' => $addonData['override_price'] ?? null,
                ]
            );
        }
    }

    private function syncTourPricings(Tour $tour, array $pricings): void
    {
        foreach ($pricings as $pricingData) {
            $pricing = TourPricing::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'pricing_mode' => $pricingData['pricing_mode'] ?? 'group',
                    'season' => $pricingData['season'] ?? 'normal',
                    'title' => $pricingData['title'] ?? 'Tarif',
                ],
                [
                    'is_active' => (bool)($pricingData['is_active'] ?? true),
                ]
            );

            foreach (($pricingData['translations'] ?? []) as $locale => $translation) {
                TourPricingTranslation::updateOrCreate(
                    [
                        'tour_pricing_id' => $pricing->id,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $translation['title'] ?? $pricing->title,
                    ]
                );
            }

            TourGroupPrice::where('tour_pricing_id', $pricing->id)->delete();
            foreach (($pricingData['group_prices'] ?? []) as $groupPriceData) {
                TourGroupPrice::create([
                    'tour_pricing_id' => $pricing->id,
                    'category' => $groupPriceData['category'] ?? 'adult',
                    'age_min' => $groupPriceData['age_min'] ?? null,
                    'age_max' => $groupPriceData['age_max'] ?? null,
                    'price' => $groupPriceData['price'] ?? 0,
                ]);
            }

            TourPrivatePrice::where('tour_pricing_id', $pricing->id)->delete();
            foreach (($pricingData['private_prices'] ?? []) as $privatePriceData) {
                TourPrivatePrice::create([
                    'tour_pricing_id' => $pricing->id,
                    'min_people' => $privatePriceData['min_people'] ?? 1,
                    'max_people' => $privatePriceData['max_people'] ?? 1,
                    'price' => $privatePriceData['price'] ?? 0,
                ]);
            }

            foreach (($pricingData['pricing_addons'] ?? []) as $pricingAddonData) {
                $addonSlug = $pricingAddonData['addon_slug'] ?? null;
                if (!$addonSlug) {
                    continue;
                }

                $addon = Addon::where('slug', $addonSlug)->first();
                if (!$addon) {
                    continue;
                }

                PricingAddon::updateOrCreate(
                    [
                        'tour_pricing_id' => $pricing->id,
                        'addon_id' => $addon->id,
                    ],
                    [
                        'is_required' => (bool)($pricingAddonData['is_required'] ?? false),
                        'is_included' => (bool)($pricingAddonData['is_included'] ?? false),
                        'override_price' => $pricingAddonData['override_price'] ?? null,
                    ]
                );
            }

            foreach (($pricingData['accommodations'] ?? []) as $pricingAccommodationData) {
                $accommodationSlug = $pricingAccommodationData['accommodation_slug'] ?? null;
                if (!$accommodationSlug) {
                    continue;
                }

                $accommodation = Accommodation::where('slug', $accommodationSlug)->first();
                if (!$accommodation) {
                    continue;
                }

                PricingAccommodation::updateOrCreate(
                    [
                        'tour_pricing_id' => $pricing->id,
                        'accommodation_id' => $accommodation->id,
                    ],
                    [
                        'is_optional' => (bool)($pricingAccommodationData['is_optional'] ?? true),
                        'nights' => $pricingAccommodationData['nights'] ?? 1,
                        'display_order' => $pricingAccommodationData['display_order'] ?? 0,
                    ]
                );
            }
        }
    }

    private function syncTourPromotions(Tour $tour, array $promotions): void
    {
        foreach ($promotions as $promotionData) {
            $name = $promotionData['name'] ?? null;
            $startDate = $promotionData['start_date'] ?? null;
            $endDate = $promotionData['end_date'] ?? null;

            if (!$name || !$startDate || !$endDate) {
                continue;
            }

            TourPromotion::updateOrCreate(
                [
                    'tour_id' => $tour->id,
                    'name' => $name,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'description' => $promotionData['description'] ?? null,
                    'discount_type' => $promotionData['discount_type'] ?? 'percentage',
                    'discount_value' => $promotionData['discount_value'] ?? 0,
                    'is_active' => (bool)($promotionData['is_active'] ?? true),
                    'usage_limit' => $promotionData['usage_limit'] ?? null,
                    'used_count' => $promotionData['used_count'] ?? 0,
                    'badge_text' => $promotionData['badge_text'] ?? null,
                    'badge_color' => $promotionData['badge_color'] ?? null,
                ]
            );
        }
    }
}
