<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Consent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GdprService
{
    public function exportClientData(Client $client): array
    {
        $data = [
            'export_date' => now()->toIso8601String(),
            'personal_info' => $this->getPersonalInfo($client),
            'bookings' => $this->getBookingsData($client),
            'reviews' => $this->getReviewsData($client),
            'wishlist' => $this->getWishlistData($client),
            'consents' => $this->getConsentsData($client),
            'activity_log' => $this->getActivityLog($client),
        ];

        return $data;
    }

    public function exportClientDataAsJson(Client $client): string
    {
        $data = $this->exportClientData($client);
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function generateExportFile(Client $client): string
    {
        $json = $this->exportClientDataAsJson($client);
        $filename = "gdpr_export_{$client->id}_" . now()->format('Y-m-d_His') . '.json';
        $path = "exports/{$filename}";

        Storage::put($path, $json);

        Log::info('GDPR data export generated', [
            'client_id' => $client->id,
            'filename' => $filename,
        ]);

        return $path;
    }

    public function deleteClientData(Client $client, bool $keepBookings = true): array
    {
        $deletedItems = [];

        Wishlist::where('client_id', $client->id)->delete();
        $deletedItems['wishlists'] = true;

        Review::where('client_id', $client->id)->update([
            'author_name' => 'Utilisateur supprimé',
            'client_id' => null,
        ]);
        $deletedItems['reviews_anonymized'] = true;

        Consent::where('client_id', $client->id)->delete();
        $deletedItems['consents'] = true;

        if ($keepBookings) {
            Booking::where('client_id', $client->id)->update([
                'customer_name' => 'Client supprimé',
                'customer_email' => 'deleted@example.com',
                'customer_phone' => null,
                'special_requests' => null,
            ]);
            $deletedItems['bookings_anonymized'] = true;
        } else {
            Booking::where('client_id', $client->id)
                ->whereNotIn('status', ['confirmed', 'completed'])
                ->delete();
            $deletedItems['bookings_deleted'] = true;
        }

        $client->update([
            'name' => 'Utilisateur supprimé',
            'email' => "deleted_{$client->id}_" . time() . '@deleted.local',
            'phone' => null,
            'address' => null,
            'city' => null,
            'country' => null,
            'postal_code' => null,
            'google_id' => null,
            'notification_preferences' => null,
        ]);

        $deletedItems['client_anonymized'] = true;

        Log::warning('GDPR data deletion executed', [
            'client_id' => $client->id,
            'deleted_items' => $deletedItems,
        ]);

        return $deletedItems;
    }

    public function recordConsent(Client $client, array $choices): Consent
    {
        return Consent::recordConsent($choices, $client->id);
    }

    public function getActiveConsents(Client $client): array
    {
        return Consent::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($consent) => [
                'choices' => $consent->choices,
                'version' => $consent->consent_version,
                'recorded_at' => $consent->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    public function hasValidConsent(Client $client, string $type): bool
    {
        $consent = Consent::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->first();

        if (!$consent) {
            return false;
        }

        $maxAge = config('gdpr.consent_max_age_days', 365);

        if ($consent->created_at->diffInDays(now()) >= $maxAge) {
            return false;
        }

        return (bool) ($consent->choices[$type] ?? false);
    }

    protected function getPersonalInfo(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'city' => $client->city,
            'country' => $client->country,
            'postal_code' => $client->postal_code,
            'preferred_language' => $client->preferred_language,
            'preferred_currency' => $client->preferred_currency,
            'created_at' => $client->created_at?->toIso8601String(),
            'email_verified_at' => $client->email_verified_at?->toIso8601String(),
        ];
    }

    protected function getBookingsData(Client $client): array
    {
        return $client->bookings()
            ->with(['tour:id,title,slug'])
            ->get()
            ->map(fn($booking) => [
                'reference' => $booking->reference,
                'tour' => $booking->tour?->title,
                'travel_date' => $booking->travel_date?->toDateString(),
                'adults' => $booking->adults,
                'children' => $booking->children,
                'total' => $booking->total_ttc ?? $booking->total_price,
                'status' => $booking->status?->value ?? $booking->status,
                'created_at' => $booking->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    protected function getReviewsData(Client $client): array
    {
        return $client->reviews()
            ->with(['tour:id,title'])
            ->get()
            ->map(fn($review) => [
                'tour' => $review->tour?->title,
                'rating' => $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'status' => $review->status,
                'created_at' => $review->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    protected function getWishlistData(Client $client): array
    {
        return $client->wishlists()
            ->with(['tour:id,title,slug'])
            ->get()
            ->map(fn($wishlist) => [
                'tour' => $wishlist->tour?->title,
                'added_at' => $wishlist->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    protected function getConsentsData(Client $client): array
    {
        return Consent::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($consent) => [
                'choices' => $consent->choices,
                'version' => $consent->consent_version,
                'source' => $consent->source,
                'recorded_at' => $consent->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    protected function getActivityLog(Client $client): array
    {
        return [
            'last_login' => $client->last_login_at?->toIso8601String(),
            'account_created' => $client->created_at?->toIso8601String(),
        ];
    }
}
