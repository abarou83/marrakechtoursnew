<?php

declare(strict_types=1);

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelBooking
{
    public function __construct(
        protected AvailabilityService $availabilityService,
    ) {}

    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Cancelled)) {
            throw new \Exception(__('Cette réservation ne peut pas être annulée.'));
        }

        return DB::transaction(function () use ($booking, $reason) {
            $totalPeople = $booking->adults + $booking->children + $booking->infants;

            $this->availabilityService->releaseSpots(
                $booking->tour,
                Carbon::parse($booking->travel_date),
                $totalPeople
            );

            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            if ($booking->promoCode) {
                $booking->promoCode->decrement('used_count');
            }

            event(new BookingCancelled($booking, $reason));

            return $booking->fresh();
        }, 5);
    }
}
