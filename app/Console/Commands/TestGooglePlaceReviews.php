<?php

namespace App\Console\Commands;

use App\Services\GooglePlaceReviewsService;
use Illuminate\Console\Command;

class TestGooglePlaceReviews extends Command
{
    protected $signature = 'google:test-place-reviews';

    protected $description = 'Vérifie Place ID + GOOGLE_PLACES_API_KEY (avis Google sur la home)';

    public function handle(GooglePlaceReviewsService $service): int
    {
        $key = config('services.google.places_api_key');
        $rawPlace = (string) site_setting('reviews_home_place_id', '');
        $placeId = GooglePlaceReviewsService::normalizePlaceId($rawPlace);

        if (! is_string($key) || $key === '') {
            $this->error('GOOGLE_PLACES_API_KEY est vide. Ajoutez-la dans .env puis : php artisan config:clear');

            return self::FAILURE;
        }

        if ($placeId === '') {
            $this->error('Place ID vide : Admin → Paramètres → Reviews Home → Place ID Google.');

            return self::FAILURE;
        }

        if ($rawPlace !== $placeId) {
            $this->line('Place ID normalisé (retrait préfixe places/, guillemets, etc.) :');
        }
        $this->line("Place ID : <fg=cyan>{$placeId}</>");

        $source = site_setting('reviews_home_source', 'google_places');
        if ($source !== 'google_places') {
            $this->warn("Source actuelle : « {$source} » — pour la home, choisissez « Google Maps (API Places) » dans les paramètres.");
        }

        $this->line('Test sans cache…');
        GooglePlaceReviewsService::forgetCacheForPlaceId($placeId);

        $data = $service->fetch($placeId, (string) config('app.locale'));

        if ($data === null) {
            $this->newLine();
            $hint = $service->lastApiErrorSummary();
            if ($hint) {
                $this->line("<fg=yellow>Réponse Google :</> {$hint}");
            }
            $this->error('L’API a échoué. Détail aussi dans storage/logs/laravel.log (« Google Places API »).');
            if ($hint && str_contains($hint, 'API_KEY_HTTP_REFERRER_BLOCKED')) {
                $this->newLine();
                $this->line('→ Votre clé est limitée aux <fg=white>sites web</> : Laravel appelle Google depuis le serveur, sans en-tête Referer.');
                $this->line('  Créez une <fg=cyan>deuxième clé API</> pour le backend : restrictions « <fg=cyan>Adresses IP</> » (IP publique du serveur) ou « Aucune » pour le dev local.');
            } else {
                $this->line('Autres causes : Places API (New) non activée, facturation inactive, mauvais projet Cloud.');
            }

            return self::FAILURE;
        }

        $n = count($data['reviews'] ?? []);
        $this->info("Succès : « {$data['display_name']} » — note " . ($data['rating'] ?? '?') . " — {$n} avis détaillés renvoyés (plafond ~5 : limite Google, pas l’app).");
        if (! empty($data['review_summary']['text'])) {
            $this->line('Synthèse « reviewSummary » : oui (texte IA fourni par Google).');
        }

        return self::SUCCESS;
    }
}
