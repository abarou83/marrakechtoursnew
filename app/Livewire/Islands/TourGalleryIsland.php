<?php

declare(strict_types=1);

namespace App\Livewire\Islands;

use App\Models\Tour;
use Livewire\Component;

class TourGalleryIsland extends Component
{
    public Tour $tour;

    public function mount(Tour $tour): void
    {
        $this->tour = $tour->load(['images', 'primaryImage', 'translations']);
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="mb-8 animate-pulse">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="lg:w-2/3 h-[400px] bg-sand-200 rounded-xl"></div>
                <div class="lg:w-1/3 flex flex-col gap-4">
                    <div class="h-[190px] bg-sand-200 rounded-xl"></div>
                    <div class="h-[190px] bg-sand-200 rounded-xl"></div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $allImages = $this->tour->images->sortByDesc('is_primary');
        $mainImage = $this->tour->primaryImage ?? $allImages->first();
        $otherImages = $allImages->where('id', '!=', $mainImage?->id)->take(4);
        $tourImages = collect([$mainImage])->merge($otherImages)->filter();

        return view('livewire.islands.tour-gallery-island', [
            'tourImages' => $tourImages,
            'allImages' => $allImages,
            'galleryImages' => $tourImages->map(fn ($img) => [
                'path' => public_storage_url($img->path),
                'alt' => $img->alt ?? ($this->tour->translate()?->title ?? $this->tour->title),
            ])->values()->all(),
            'allGalleryImages' => $allImages->map(fn ($img) => [
                'path' => public_storage_url($img->path),
                'alt' => $img->alt ?? ($this->tour->translate()?->title ?? $this->tour->title),
            ])->values()->all(),
        ]);
    }
}
