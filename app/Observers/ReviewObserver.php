<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Review;
use App\Models\Tour;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $this->updateTourStats($review);
    }

    public function updated(Review $review): void
    {
        $this->updateTourStats($review);
    }

    public function deleted(Review $review): void
    {
        $this->updateTourStats($review);
    }

    protected function updateTourStats(Review $review): void
    {
        if (!$review->tour_id) {
            return;
        }

        $tour = Tour::find($review->tour_id);

        if (!$tour) {
            return;
        }

        $stats = Review::where('tour_id', $tour->id)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as reviews_count')
            ->first();

        $tour->update([
            'avg_rating' => round($stats->avg_rating ?? 0, 1),
            'reviews_count' => $stats->reviews_count ?? 0,
        ]);

        Cache::tags(["tour:{$tour->id}"])->flush();

        \Log::debug('Tour review stats updated', [
            'tour_id' => $tour->id,
            'avg_rating' => $tour->avg_rating,
            'reviews_count' => $tour->reviews_count,
        ]);
    }
}
