<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingConfirmed;
use App\Events\BookingCancelled;
use App\Services\GiftCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe Webhook received', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        $cacheKey = 'stripe_webhook_event:' . $event->id;
        if (!Cache::add($cacheKey, true, now()->addDays(7))) {
            Log::info('Stripe Webhook: duplicate event skipped', ['id' => $event->id]);
            return response()->json(['received' => true, 'duplicate' => true]);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'charge.refunded' => $this->handleRefund($event->data->object),
            'charge.dispute.created' => $this->handleDispute($event->data->object),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    protected function handlePaymentSucceeded($paymentIntent): void
    {
        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$booking) {
            Log::warning('Stripe Webhook: Booking not found for PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        if ($booking->payment_status === PaymentStatus::Paid) {
            Log::info('Stripe Webhook: Payment already processed', [
                'booking_id' => $booking->id,
            ]);
            return;
        }

        $booking->loadMissing('tour');

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::Paid,
            'confirmed_at' => now(),
            'payment_provider' => 'stripe',
        ]);

        $this->redeemGiftCardIfNeeded($booking);

        Log::info('Stripe Webhook: Booking confirmed', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
        ]);

        event(new BookingConfirmed($booking));
    }

    protected function handlePaymentFailed($paymentIntent): void
    {
        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$booking) {
            return;
        }

        $booking->update([
            'payment_status' => PaymentStatus::Failed,
        ]);

        Log::warning('Stripe Webhook: Payment failed', [
            'booking_id' => $booking->id,
            'error' => $paymentIntent->last_payment_error?->message ?? 'Unknown error',
        ]);
    }

    protected function handleRefund($charge): void
    {
        $paymentIntentId = $charge->payment_intent;
        $booking = Booking::where('payment_intent_id', $paymentIntentId)->first();

        if (!$booking) {
            return;
        }

        $refundAmount = $charge->amount_refunded / 100;

        $booking->update([
            'payment_status' => PaymentStatus::Refunded,
            'refund_amount' => $refundAmount,
            'refunded_at' => now(),
        ]);

        Log::info('Stripe Webhook: Booking refunded', [
            'booking_id' => $booking->id,
            'refund_amount' => $refundAmount,
        ]);
    }

    protected function handleDispute($dispute): void
    {
        $charge = \Stripe\Charge::retrieve($dispute->charge);
        $booking = Booking::where('payment_intent_id', $charge->payment_intent)->first();

        if (!$booking) {
            return;
        }

        Log::warning('Stripe Webhook: Dispute created', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'dispute_id' => $dispute->id,
            'reason' => $dispute->reason,
        ]);

        // Notify admin about dispute
        // TODO: Send notification email
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
