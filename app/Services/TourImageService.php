<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Image;
use App\Models\Tour;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class TourImageService
{
    /**
     * @return array{matched: int, assigned: int, files: int}
     */
    public function importFromDirectory(string $directory, bool $onlyWithoutImages = true): array
    {
        if (! is_dir($directory)) {
            throw new \InvalidArgumentException("Dossier introuvable : {$directory}");
        }

        $files = $this->collectImageFiles($directory);
        $stats = ['matched' => 0, 'assigned' => 0, 'files' => count($files)];

        if ($files === []) {
            return $stats;
        }

        $bySlug = [];
        $pool = [];

        foreach ($files as $filePath) {
            $slug = Str::slug(pathinfo($filePath, PATHINFO_FILENAME));
            if ($slug !== '') {
                $bySlug[$slug][] = $filePath;
            }
            $pool[] = $filePath;
        }

        $tours = Tour::query()->with('images')->get();

        foreach ($tours as $tour) {
            if ($onlyWithoutImages && $tour->images->isNotEmpty()) {
                continue;
            }

            $tourSlug = Str::slug($tour->slug);
            $source = $bySlug[$tourSlug][0] ?? null;

            if ($source !== null) {
                $this->attachFileToTour($tour, $source, true);
                $stats['matched']++;
                $stats['assigned']++;
            }
        }

        $toursNeedingImages = Tour::query()
            ->whereDoesntHave('images')
            ->orderBy('id')
            ->get();

        if ($toursNeedingImages->isEmpty() || $pool === []) {
            return $stats;
        }

        $poolIndex = 0;
        foreach ($toursNeedingImages as $tour) {
            $source = $pool[$poolIndex % count($pool)];
            $poolIndex++;
            $this->attachFileToTour($tour, $source, true);
            $stats['assigned']++;
        }

        return $stats;
    }

    /**
     * @return array{attached: int, failed: int}
     */
    public function fillMissingFromPexels(?int $limit = null): array
    {
        $apiKey = config('services.pexels.key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new \RuntimeException('Définissez PEXELS_API_KEY dans le fichier .env');
        }

        $stats = ['attached' => 0, 'failed' => 0];

        $query = Tour::query()->whereDoesntHave('images')->orderBy('id');
        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        /** @var Collection<int, Tour> $tours */
        $tours = $query->get();

        foreach ($tours as $tour) {
            $ok = $this->attachPexelsImage($tour, $apiKey);
            if ($ok) {
                $stats['attached']++;
            } else {
                $stats['failed']++;
            }

            usleep(350_000);
        }

        return $stats;
    }

    /**
     * @return array{attached: int, failed: int}
     */
    public function fillMissingFromPixabay(?int $limit = null): array
    {
        $apiKey = config('services.pixabay.key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new \RuntimeException('Définissez PIXABAY_API_KEY dans le fichier .env');
        }

        $stats = ['attached' => 0, 'failed' => 0];

        $query = Tour::query()->whereDoesntHave('images')->orderBy('id');
        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        /** @var Collection<int, Tour> $tours */
        $tours = $query->get();

        foreach ($tours as $tour) {
            $ok = $this->attachPixabayImage($tour, $apiKey);
            if ($ok) {
                $stats['attached']++;
            } else {
                $stats['failed']++;
            }

            usleep(650_000);
        }

        return $stats;
    }

    public function attachFileToTour(Tour $tour, string $sourcePath, bool $primary = true): Image
    {
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg');
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $destination = 'tours/'.Str::uuid().'.'.$extension;
        $contents = file_get_contents($sourcePath);
        if ($contents === false) {
            throw new \RuntimeException("Impossible de lire : {$sourcePath}");
        }

        Storage::disk('public')->put($destination, $contents);

        if ($primary) {
            $tour->images()->update(['is_primary' => false]);
        }

        return Image::create([
            'imageable_type' => Tour::class,
            'imageable_id' => $tour->id,
            'path' => $destination,
            'alt' => $tour->title,
            'is_primary' => $primary,
        ]);
    }

    protected function attachPexelsImage(Tour $tour, string $apiKey): bool
    {
        $searchTerms = trim(implode(' ', array_filter([
            $tour->location,
            is_string($tour->title) ? $tour->title : null,
            'Morocco',
        ])));

        $searchTerms = Str::limit($searchTerms, 90, '');

        $photos = $this->searchPexels($apiKey, $searchTerms !== '' ? $searchTerms : 'Marrakech Morocco');
        if ($photos === []) {
            $photos = $this->searchPexels($apiKey, 'Morocco travel');
        }

        if ($photos === []) {
            return false;
        }

        $photo = $photos[array_rand($photos)];
        $src = $photo['src'] ?? [];
        $url = $src['large2x'] ?? $src['large'] ?? $src['original'] ?? null;
        if (! is_string($url) || $url === '') {
            return false;
        }

        $imageResponse = Http::timeout(45)->get($url);
        if (! $imageResponse->successful()) {
            return false;
        }

        $destination = 'tours/pexels-'.$tour->id.'-'.Str::uuid().'.jpg';
        Storage::disk('public')->put($destination, $imageResponse->body());

        $tour->images()->update(['is_primary' => false]);

        $alt = $tour->title;
        if (is_string($photo['alt'] ?? null) && $photo['alt'] !== '') {
            $alt = $photo['alt'];
        }

        Image::create([
            'imageable_type' => Tour::class,
            'imageable_id' => $tour->id,
            'path' => $destination,
            'alt' => $alt,
            'is_primary' => true,
        ]);

        return true;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchPexels(string $apiKey, string $query): array
    {
        $response = Http::timeout(25)
            ->withHeaders(['Authorization' => $apiKey])
            ->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'orientation' => 'landscape',
                'size' => 'large',
                'per_page' => 8,
                'locale' => 'fr-FR',
            ]);

        if (! $response->successful()) {
            return [];
        }

        $photos = $response->json('photos');

        return is_array($photos) ? $photos : [];
    }

    protected function attachPixabayImage(Tour $tour, string $apiKey): bool
    {
        $searchTerms = trim(implode(' ', array_filter([
            $tour->location,
            is_string($tour->title) ? $tour->title : null,
            'Morocco',
        ])));

        $searchTerms = Str::limit($searchTerms, 90, '');

        $hits = $this->searchPixabay($apiKey, $searchTerms !== '' ? $searchTerms : 'Marrakech Morocco');
        if ($hits === []) {
            $hits = $this->searchPixabay($apiKey, 'Morocco travel desert');
        }

        if ($hits === []) {
            return false;
        }

        $hit = $hits[array_rand($hits)];
        $url = $hit['largeImageURL'] ?? $hit['webformatURL'] ?? null;
        if (! is_string($url) || $url === '') {
            return false;
        }

        $imageResponse = Http::timeout(45)->get($url);
        if (! $imageResponse->successful()) {
            return false;
        }

        $extension = 'jpg';
        if (preg_match('/\.(jpe?g|png|webp)/i', parse_url($url, PHP_URL_PATH) ?? '', $matches)) {
            $extension = strtolower(str_replace('jpeg', 'jpg', $matches[1]));
        }

        $destination = 'tours/pixabay-'.$tour->id.'-'.Str::uuid().'.'.$extension;
        Storage::disk('public')->put($destination, $imageResponse->body());

        $tour->images()->update(['is_primary' => false]);

        Image::create([
            'imageable_type' => Tour::class,
            'imageable_id' => $tour->id,
            'path' => $destination,
            'alt' => is_string($hit['tags'] ?? null) ? $hit['tags'] : $tour->title,
            'is_primary' => true,
        ]);

        return true;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchPixabay(string $apiKey, string $query): array
    {
        $response = Http::timeout(25)->get('https://pixabay.com/api/', [
            'key' => $apiKey,
            'q' => $query,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'safesearch' => 'true',
            'per_page' => 8,
            'lang' => 'fr',
        ]);

        if (! $response->successful()) {
            return [];
        }

        $hits = $response->json('hits');

        return is_array($hits) ? $hits : [];
    }

    /**
     * @return list<string>
     */
    protected function collectImageFiles(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }
}
