<?php

declare(strict_types=1);

namespace App\Livewire\Islands;

use App\Models\Tour;
use Livewire\Component;

class TourPriceIsland extends Component
{
    public Tour $tour;
    public $activePromotion = null;

    public function mount(Tour $tour, $activePromotion = null): void
    {
        $this->tour = $tour->load(['pricings.groupPrices', 'pricings.privatePrices']);
        $this->activePromotion = $activePromotion ?? $tour->activePromotion();
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-4 animate-pulse">
            <div class="h-10 bg-sand-200 rounded w-2/3 mx-auto"></div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.islands.tour-price-island');
    }
}
