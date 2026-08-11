<?php

declare(strict_types=1);

namespace App\Livewire\Islands;

use App\Models\Tour;
use Livewire\Component;

class TourCalendarIsland extends Component
{
    public Tour $tour;

    public function mount(Tour $tour): void
    {
        $this->tour = $tour;
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 animate-pulse space-y-4">
            <div class="h-14 bg-sand-200 rounded-lg"></div>
            <div class="h-14 bg-sand-200 rounded-lg"></div>
            <div class="h-12 bg-sand-200 rounded-lg"></div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.islands.tour-calendar-island');
    }
}
