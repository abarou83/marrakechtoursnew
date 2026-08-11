<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BannerTranslation;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        if (Banner::query()->exists()) {
            $this->command?->info('Des bannières existent déjà — seed ignoré (supprimez-les dans l’admin pour relancer).');

            return;
        }

        $apiKey = config('services.pexels.key');
        if (! is_string($apiKey) || $apiKey === '') {
            $this->command?->warn('PEXELS_API_KEY manquant — impossible de télécharger les images bannière.');

            return;
        }

        $definitions = [
            [
                'order' => 1,
                'query' => 'Marrakech Morocco medina',
                'translations' => [
                    'fr' => ['title' => 'Marrakech authentique', 'slug' => 'Excursions & activités au départ de la ville ocre'],
                    'en' => ['title' => 'Authentic Marrakech', 'slug' => 'Tours & experiences from the Red City'],
                ],
            ],
            [
                'order' => 2,
                'query' => 'Morocco Sahara desert dunes',
                'translations' => [
                    'fr' => ['title' => 'Désert & Atlas', 'slug' => 'Échappees nature entre dunes, kasbahs et montagnes'],
                    'en' => ['title' => 'Desert & Atlas', 'slug' => 'Desert escapes, kasbahs and mountain views'],
                ],
            ],
        ];

        $toursUrl = url('/tours');

        foreach ($definitions as $definition) {
            $path = $this->downloadBannerImage($apiKey, $definition['query']);
            if ($path === null) {
                $this->command?->warn('Échec téléchargement : '.$definition['query']);

                continue;
            }

            $banner = Banner::create([
                'image_path' => $path,
                'link_url' => $toursUrl,
                'is_active' => true,
                'order' => $definition['order'],
            ]);

            Image::create([
                'imageable_type' => Banner::class,
                'imageable_id' => $banner->id,
                'path' => $path,
                'alt' => $definition['translations']['fr']['title'],
                'is_primary' => true,
            ]);

            foreach ($definition['translations'] as $locale => $content) {
                BannerTranslation::create([
                    'banner_id' => $banner->id,
                    'locale' => $locale,
                    'title' => $content['title'],
                    'slug' => $content['slug'],
                ]);
            }

            $this->command?->info('Bannière créée : '.$definition['translations']['fr']['title']);
            usleep(400_000);
        }
    }

    protected function downloadBannerImage(string $apiKey, string $query): ?string
    {
        $response = Http::timeout(25)
            ->withHeaders(['Authorization' => $apiKey])
            ->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'orientation' => 'landscape',
                'size' => 'large',
                'per_page' => 5,
            ]);

        if (! $response->successful()) {
            return null;
        }

        $photos = $response->json('photos');
        if (! is_array($photos) || $photos === []) {
            return null;
        }

        $photo = $photos[0];
        $src = $photo['src'] ?? [];
        $url = $src['large2x'] ?? $src['large'] ?? $src['original'] ?? null;
        if (! is_string($url) || $url === '') {
            return null;
        }

        $imageResponse = Http::timeout(45)->get($url);
        if (! $imageResponse->successful()) {
            return null;
        }

        $destination = 'banners/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($destination, $imageResponse->body());

        return $destination;
    }
}
