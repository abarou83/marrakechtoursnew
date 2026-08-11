<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Tour;

class AbandonedCartService
{
    public function track(array $data): AbandonedCart
    {
        return AbandonedCart::updateOrCreate(
            [
                'email' => $data['email'],
                'tour_id' => $data['tour_id'],
                'converted_at' => null,
            ],
            [
                'client_id' => $data['client_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'travel_date' => $data['travel_date'] ?? null,
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'total_amount' => $data['total_amount'] ?? null,
                'currency' => $data['currency'] ?? 'EUR',
                'cart_data' => $data['cart_data'] ?? null,
                'marketing_opt_in' => $data['marketing_opt_in'] ?? false,
            ]
        );
    }

    public function markConverted(string $email, int $tourId, int $bookingId): void
    {
        AbandonedCart::where('email', $email)
            ->where('tour_id', $tourId)
            ->whereNull('converted_at')
            ->update([
                'converted_at' => now(),
                'booking_id' => $bookingId,
            ]);
    }
}
