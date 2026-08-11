<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingConfirmed;
use App\Services\GiftCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;

        Log::info('PayPal Webhook received', [
            'type' => $eventType,
            'id' => $payload['id'] ?? null,
        ]);

        match ($eventType) {
            'CHECKOUT.ORDER.APPROVED',
            'PAYMENT.CAPTURE.COMPLETED' => $this->handlePaymentCompleted($payload),
            'PAYMENT.CAPTURE.REFUNDED' => $this->handleRefund($payload),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    protected function handlePaymentCompleted(array $payload): void
    {
        $resource = $payload['resource'] ?? [];
        $orderId = $resource['id'] ?? $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$orderId) {
            return;
        }

        $booking = Booking::where('payment_intent_id', $orderId)
            ->where('payment_provider', 'paypal')
            ->first();

        if (!$booking || $booking->payment_status === PaymentStatus::Paid) {
            return;
        }

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::Paid,
            'confirmed_at' => now(),
        ]);

        $this->redeemGiftCardIfNeeded($booking);

        event(new BookingConfirmed($booking));

        Log::info('PayPal Webhook: Booking confirmed', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
        ]);
    }

    protected function handleRefund(array $payload): void
    {
        $resource = $payload['resource'] ?? [];
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$orderId) {
            return;
        }

        $booking = Booking::where('payment_intent_id', $orderId)->first();

        if ($booking) {
            $booking->update([
                'payment_status' => PaymentStatus::Refunded,
                'refunded_at' => now(),
            ]);
        }
    }

    protected function redeemGiftCardIfNeeded(Booking $booking): void
    {
        if (!$booking->gift_card_id || $booking->gift_card_amount <= 0) {
            return;
        }

        $giftCard = $booking->giftCard ?? \App\Models\GiftCard::find($booking->gift_card_id);

        if ($giftCard) {
            app(GiftCardService::class)->redeem(
                $giftCard,
                (float) $booking->gift_card_amount,
                $booking->client_id
            );
        }
    }
}
