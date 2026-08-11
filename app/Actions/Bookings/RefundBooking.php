<?php

declare(strict_types=1);

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingRefunded;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class RefundBooking
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function execute(Booking $booking, ?float $amount = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Refunded)) {
            throw new \Exception(__('Cette réservation ne peut pas être remboursée.'));
        }

        return DB::transaction(function () use ($booking, $amount) {
            $refundAmount = $amount ?? $booking->total_ttc;

            $refundResult = $this->paymentService->refund($booking, $refundAmount);

            $booking->update([
                'status' => BookingStatus::Refunded,
                'payment_status' => PaymentStatus::Refunded,
                'refunded_at' => now(),
                'refund_amount' => $refundAmount,
            ]);

            event(new BookingRefunded($booking, $refundAmount));

            return $booking->fresh();
        });
    }
}
