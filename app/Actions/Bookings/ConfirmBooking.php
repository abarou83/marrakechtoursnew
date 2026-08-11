<?php

declare(strict_types=1);

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingConfirmed;
use Illuminate\Support\Facades\DB;

class ConfirmBooking
{
    public function execute(Booking $booking, string $paymentIntentId): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Confirmed)) {
            throw new \Exception(__('Cette réservation ne peut pas être confirmée.'));
        }

        return DB::transaction(function () use ($booking, $paymentIntentId) {
            $booking->update([
                'status' => BookingStatus::Confirmed,
                'payment_status' => PaymentStatus::Paid,
                'payment_intent_id' => $paymentIntentId,
                'confirmed_at' => now(),
            ]);

            event(new BookingConfirmed($booking));

            return $booking->fresh();
        });
    }
}
