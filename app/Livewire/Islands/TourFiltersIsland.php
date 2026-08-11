<?php

declare(strict_types=1);

namespace App\Livewire\Islands;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Url;

class TourFiltersIsland extends Component
{
    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url(as: 'location')]
    public string $location = '';

    #[Url(as: 'min_price')]
    public string $minPrice = '';

    #[Url(as: 'max_price')]
    public string $maxPrice = '';

    public function updated($property): void
    {
        if (in_array($property, ['q', 'category', 'location', 'minPrice', 'maxPrice'], true)) {
            $this->redirectToFilters();
        }
    }

    public function search(): void
    {
        $this->redirectToFilters();
    }

    public function resetFilters(): void
    {
        $this->redirect(route('tours.index'), navigate: true);
    }

    protected function redirectToFilters(): void
    {
        $params = array_filter([
            'q' => $this->q ?: null,
            'category' => $this->category ?: null,
            'location' => $this->location ?: null,
            'min_price' => $this->minPrice !== '' ? $this->minPrice : null,
            'max_price' => $this->maxPrice !== '' ? $this->maxPrice : null,
        ]);

        $this->redirect(route('tours.index', $params), navigate: true);
    }

    public function render()
    {
        return view('livewire.islands.tour-filters-island', [
            'categories' => Category::with('translations')->orderBy('name')->get(),
        ]);
    }
}
