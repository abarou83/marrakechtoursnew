<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlaceReviewsService
{
    private const CACHE_PREFIX = 'google_place_reviews_v3_';

    private ?string $lastApiErrorSummary = null;

    /**
     * Résumé de la dernière erreur API (ex. après un fetch null), utile pour la commande Artisan.
     */
    public function lastApiErrorSummary(): ?string
    {
        return $this->lastApiErrorSummary;
    }

    /**
     * Récupère les avis Google Maps (Places API — Place Details) pour un lieu.
     * Les réponses réussies sont mises en cache 12 h. Les échecs ne sont pas mis en cache
     * (évite de bloquer 12 h après une erreur de config ou un premier essai raté).
     *
     * @return array{display_name: string, rating: ?float, user_rating_count: int, google_maps_uri: ?string, reviews: array<int, array{author: string, rating: int, text: string, time_label: string, author_uri: ?string, photo_uri: ?string}>, review_summary: ?array{text: string, disclosure_text: string, reviews_uri: ?string, flag_content_uri: ?string}}|null
     */
    public function fetch(string $placeId, ?string $appLocale = null): ?array
    {
        $this->lastApiErrorSummary = null;

        $placeId = self::normalizePlaceId($placeId);
        $apiKey = config('services.google.places_api_key');

        if ($placeId === '' || ! is_string($apiKey) || $apiKey === '') {
            return null;
        }

        $langKey = self::languageKeyForCache($appLocale);
        $apiLang = self::apiLanguageCode($appLocale);

        $cacheKey = self::cacheKey($placeId, $langKey);

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $result = $this->fetchFromApi($placeId, $apiKey, $apiLang);

        if ($result !== null) {
            Cache::put($cacheKey, $result, now()->addHours(12));
        }

        return $result;
    }

    /**
     * Code langue BCP-47 à 2 lettres pour l’API (ex. fr, en), ou null = défaut Google (souvent en).
     */
    public static function apiLanguageCode(?string $appLocale): ?string
    {
        if ($appLocale === null || $appLocale === '') {
            return null;
        }
        $normalized = strtolower(str_replace('_', '-', $appLocale));
        $primary = explode('-', $normalized)[0] ?? $normalized;

        return strlen($primary) === 2 ? $primary : null;
    }

    private static function languageKeyForCache(?string $appLocale): string
    {
        return self::apiLanguageCode($appLocale) ?? 'default';
    }

    /**
     * Nettoie le Place ID (souvent collé comme "places/ChIJ..." ou avec espaces / guillemets).
     */
    public static function normalizePlaceId(string $placeId): string
    {
        $placeId = trim($placeId, " \t\n\r\0\x0B\"'");
        if (str_starts_with($placeId, 'places/')) {
            $placeId = substr($placeId, strlen('places/'));
        }

        return trim($placeId);
    }

    public static function forgetCacheForPlaceId(string $placeId): void
    {
        $placeId = self::normalizePlaceId($placeId);
        if ($placeId === '') {
            return;
        }
        foreach (self::cacheLanguageSuffixes() as $suffix) {
            Cache::forget(self::cacheKey($placeId, $suffix));
        }
        // Anciennes clés
        Cache::forget('google_place_reviews_' . md5($placeId));
        Cache::forget('google_place_reviews_v2_' . md5($placeId));
    }

    /**
     * @return list<string>
     */
    private static function cacheLanguageSuffixes(): array
    {
        $codes = collect(['default', 'fr', 'en', 'es', 'de', 'it', 'pt', 'nl', 'ar']);
        try {
            $codes = $codes->merge(
                Language::active()->pluck('code')->map(function ($c) {
                    $c = strtolower(str_replace('_', '-', (string) $c));

                    return explode('-', $c)[0] ?? $c;
                })->filter(fn ($c) => strlen($c) === 2)
            );
        } catch (\Throwable) {
            // pas de table languages en CLI rare
        }
        $codes = $codes->merge([
            self::languageKeyForCache(config('app.locale')),
            self::languageKeyForCache(config('app.fallback_locale')),
        ]);

        return $codes->unique()->values()->all();
    }

    private static function cacheKey(string $normalizedPlaceId, string $languageSuffix): string
    {
        return self::CACHE_PREFIX . md5($normalizedPlaceId . '|' . $languageSuffix);
    }

    private function fetchFromApi(string $placeId, string $apiKey, ?string $languageCode): ?array
    {
        $pathSegment = rawurlencode($placeId);
        $url = 'https://places.googleapis.com/v1/places/' . $pathSegment;
        if ($languageCode !== null) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'languageCode=' . rawurlencode($languageCode);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'id,displayName,rating,userRatingCount,reviews,googleMapsUri,reviewSummary',
            ])->timeout(20)->get($url);
        } catch (\Throwable $e) {
            Log::error('Google Places API: exception HTTP', [
                'place_id' => $placeId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            $body = $response->body();
            $json = $response->json();
            $reason = null;
            $message = null;
            if (is_array($json) && isset($json['error']) && is_array($json['error'])) {
                $message = isset($json['error']['message']) && is_string($json['error']['message'])
                    ? $json['error']['message']
                    : null;
                $details = $json['error']['details'] ?? [];
                if (is_array($details) && isset($details[0]['reason']) && is_string($details[0]['reason'])) {
                    $reason = $details[0]['reason'];
                }
            }
            $this->lastApiErrorSummary = $reason && $message
                ? "{$reason} — {$message}"
                : ($message ?? mb_substr($body, 0, 400));

            Log::warning('Google Places API: échec Place Details', [
                'status' => $response->status(),
                'place_id' => $placeId,
                'google_reason' => $reason,
                'body' => mb_substr($body, 0, 2000),
            ]);

            return null;
        }

        $data = $response->json();
        if (! is_array($data)) {
            return null;
        }

        $reviews = [];
        foreach ($data['reviews'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }
            $text = $item['text']['text'] ?? $item['originalText']['text'] ?? '';
            $author = $item['authorAttribution']['displayName'] ?? '';
            $reviews[] = [
                'author' => is_string($author) ? $author : '',
                'rating' => (int) ($item['rating'] ?? 0),
                'text' => is_string($text) ? $text : '',
                'time_label' => is_string($item['relativePublishTimeDescription'] ?? null)
                    ? $item['relativePublishTimeDescription']
                    : '',
                'author_uri' => $item['authorAttribution']['uri'] ?? null,
                'photo_uri' => $item['authorAttribution']['photoUri'] ?? null,
            ];
        }

        $displayName = $data['displayName']['text'] ?? '';

        $reviewSummary = null;
        if (! empty($data['reviewSummary']) && is_array($data['reviewSummary'])) {
            $rs = $data['reviewSummary'];
            $sumText = $rs['text']['text'] ?? '';
            if (is_string($sumText) && $sumText !== '') {
                $disclosure = $rs['disclosureText']['text'] ?? '';
                $reviewSummary = [
                    'text' => $sumText,
                    'disclosure_text' => is_string($disclosure) ? $disclosure : '',
                    'reviews_uri' => isset($rs['reviewsUri']) && is_string($rs['reviewsUri']) ? $rs['reviewsUri'] : null,
                    'flag_content_uri' => isset($rs['flagContentUri']) && is_string($rs['flagContentUri']) ? $rs['flagContentUri'] : null,
                ];
            }
        }

        return [
            'display_name' => is_string($displayName) ? $displayName : '',
            'rating' => isset($data['rating']) ? (float) $data['rating'] : null,
            'user_rating_count' => (int) ($data['userRatingCount'] ?? 0),
            'google_maps_uri' => isset($data['googleMapsUri']) && is_string($data['googleMapsUri'])
                ? $data['googleMapsUri']
                : null,
            'reviews' => $reviews,
            'review_summary' => $reviewSummary,
        ];
    }
}
