<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tour = App\Models\Tour::with(['images', 'primaryImage', 'translations'])->findOrFail(5);
$html = view('livewire.islands.tour-gallery-island', [
    'tourImages' => collect([$tour->primaryImage ?? $tour->images->first()])->filter(),
    'allImages' => $tour->images,
    'galleryImages' => [['path' => public_storage_url($tour->images->first()->path), 'alt' => 'test']],
    'allGalleryImages' => [['path' => public_storage_url($tour->images->first()->path), 'alt' => 'test']],
])->render();

echo substr($html, 0, 300).PHP_EOL;
echo 'len='.strlen($html).PHP_EOL;
