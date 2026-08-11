<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tour;
use Illuminate\Support\Facades\Cache;

class TourObserver
{
    public function created(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    public function updated(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    public function deleted(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    public function restored(Tour $tour): void
    {
        $this->clearTourCache($tour);
    }

    protected function clearTourCache(Tour $tour): void
    {
        Cache::tags(['tours', "tour:{$tour->id}"])->flush();

        Cache::forget('homepage_featured_tours');
        Cache::forget('homepage_bestseller_tours');
        Cache::forget('tours_listing_all');

        if ($tour->category_id) {
            Cache::forget("category:{$tour->category_id}:tours");
        }

        \Log::debug('Tour cache cleared', ['tour_id' => $tour->id]);
    }
}
