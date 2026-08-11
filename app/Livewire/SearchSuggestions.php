<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SearchSuggestions extends Component
{
    #[Url(except: '')]
    public string $query = '';

    public bool $showResults = false;

    public array $tours = [];

    public array $destinations = [];

    public array $categories = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->clearResults();

            return;
        }

        $this->search();
    }

    public function search(): void
    {
        $locale = app()->getLocale();
        $searchTerm = '%' . $this->query . '%';

        $this->destinations = Destination::active()
            ->where("name->{$locale}", 'like', $searchTerm)
            ->orWhere('name->fr', 'like', $searchTerm)
            ->limit(3)
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'slug' => $d->getTranslation('slug', $locale),
                'tours_count' => $d->tours_count,
            ])
            ->toArray();

        $this->categories = Category::active()
            ->where("name->{$locale}", 'like', $searchTerm)
            ->orWhere('name->fr', 'like', $searchTerm)
            ->limit(3)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->getTranslation('slug', $locale),
            ])
            ->toArray();

        $this->tours = Tour::active()
            ->where(function ($q) use ($locale, $searchTerm) {
                $q->where("name->{$locale}", 'like', $searchTerm)
                    ->orWhere('name->fr', 'like', $searchTerm);
            })
            ->with(['category:id,name', 'media'])
            ->limit(5)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->getTranslation('slug', $locale),
                'category' => $t->category?->name,
                'price' => $t->price_adult,
                'image' => $t->getFirstMediaUrl('featured', 'thumb') ?: null,
            ])
            ->toArray();

        $this->showResults = ! empty($this->destinations) || ! empty($this->categories) || ! empty($this->tours);
    }

    public function clearResults(): void
    {
        $this->tours = [];
        $this->destinations = [];
        $this->categories = [];
        $this->showResults = false;
    }

    public function selectResult(string $type, string $slug): void
    {
        $locale = app()->getLocale();

        $route = match ($type) {
            'destination' => route('destinations.show', ['locale' => $locale, 'slug' => $slug]),
            'category' => route('categories.show', ['locale' => $locale, 'slug' => $slug]),
            'tour' => route('tours.show', ['locale' => $locale, 'slug' => $slug]),
            default => route('tours.index', ['locale' => $locale, 'q' => $this->query]),
        };

        $this->redirect($route);
    }

    public function render(): View
    {
        return view('livewire.search-suggestions');
    }
}
