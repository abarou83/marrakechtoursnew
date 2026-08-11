<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    public function getPayableAmount(Booking $booking): float
    {
        if ($booking->payment_type === 'deposit' && $booking->deposit_amount > 0) {
            return (float) $booking->deposit_amount;
        }

        return (float) ($booking->total_ttc ?? $booking->total_price ?? 0);
    }

    /**
     * Create a payment intent with the selected provider
     */
    public function createPaymentIntent(Booking $booking, PaymentProvider $provider): array
    {
        return match ($provider) {
            PaymentProvider::Stripe => $this->createStripePaymentIntent($booking),
            PaymentProvider::PayPal => $this->createPayPalOrder($booking),
        };
    }

    /**
     * Create Stripe Payment Intent
     */
    protected function createStripePaymentIntent(Booking $booking): array
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $idempotencyKey = "booking_{$booking->reference}_" . now()->timestamp;

        try {
            $amount = $this->getPayableAmount($booking);

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($booking->currency),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->reference,
                    'tour_id' => $booking->tour_id,
                    'customer_email' => $booking->customer_email,
                ],
                'receipt_email' => $booking->customer_email,
                'description' => "Réservation {$booking->reference} - {$booking->tour->title}",
            ], [
                'idempotency_key' => $idempotencyKey,
            ]);

            $booking->update([
                'payment_intent_id' => $paymentIntent->id,
                'payment_provider' => PaymentProvider::Stripe->value,
            ]);

            Log::info('Stripe PaymentIntent created', [
                'booking_reference' => $booking->reference,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
            ]);

            return [
                'provider' => 'stripe',
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'publishable_key' => config('services.stripe.key'),
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception(__('Erreur lors de la création du paiement. Veuillez réessayer.'));
        }
    }

    /**
     * Create PayPal Order
     */
    protected function createPayPalOrder(Booking $booking): array
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $mode = config('services.paypal.mode', 'sandbox');

        $baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        try {
            $accessToken = $this->getPayPalAccessToken($baseUrl, $clientId, $secret);

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $booking->reference,
                        'description' => "Réservation {$booking->reference}",
                        'amount' => [
                            'currency_code' => $booking->currency,
                            'value' => number_format($this->getPayableAmount($booking), 2, '.', ''),
                        ],
                        'custom_id' => (string) $booking->id,
                    ],
                ],
                'application_context' => [
                    'brand_name' => 'Marrakech Tours',
                    'locale' => app()->getLocale() . '-' . strtoupper(app()->getLocale()),
                    'landing_page' => 'NO_PREFERENCE',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('booking.payment.success', $booking->reference),
                    'cancel_url' => route('booking.payment.cancel', $booking->reference),
                ],
            ];

            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->withHeaders(['PayPal-Request-Id' => $booking->reference . '_' . now()->timestamp])
                ->post("{$baseUrl}/v2/checkout/orders", $orderData);

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            $order = $response->json();

            $booking->update([
                'payment_intent_id' => $order['id'],
                'payment_provider' => PaymentProvider::PayPal->value,
            ]);

            Log::info('PayPal Order created', [
                'booking_reference' => $booking->reference,
                'order_id' => $order['id'],
            ]);

            $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

            return [
                'provider' => 'paypal',
                'order_id' => $order['id'],
                'approve_url' => $approveUrl,
                'client_id' => $clientId,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal Order creation failed', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception(__('Erreur lors de la création du paiement PayPal. Veuillez réessayer.'));
        }
    }

    /**
     * Get PayPal access token
     */
    protected function getPayPalAccessToken(string $baseUrl, string $clientId, string $secret): string
    {
        $response = \Illuminate\Support\Facades\Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal access token');
        }

        return $response->json('access_token');
    }

    /**
     * Handle Stripe webhook
     */
    public function handleStripeWebhook(string $payload, string $signature): array
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Invalid signature');
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        return match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => ['status' => 'ignored', 'type' => $event->type],
        };
    }

    /**
     * Handle successful Stripe payment
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$booking) {
            Log::warning('Booking not found for PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return ['status' => 'booking_not_found'];
        }

        $booking->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => \App\Enums\BookingStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        Log::info('Payment succeeded', [
            'booking_reference' => $booking->reference,
            'payment_intent_id' => $paymentIntent->id,
        ]);

        return ['status' => 'success', 'booking_reference' => $booking->reference];
    }

    /**
     * Handle failed Stripe payment
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();

        if ($booking) {
            $booking->update([
                'payment_status' => PaymentStatus::Failed,
            ]);

            Log::info('Payment failed', [
                'booking_reference' => $booking->reference,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        }

        return ['status' => 'failed'];
    }

    /**
     * Handle Stripe refund
     */
    protected function handleChargeRefunded($charge): array
    {
        $booking = Booking::where('payment_intent_id', $charge->payment_intent)->first();

        if ($booking) {
            $booking->update([
                'payment_status' => PaymentStatus::Refunded,
                'status' => \App\Enums\BookingStatus::Refunded,
                'refunded_at' => now(),
            ]);

            Log::info('Payment refunded', [
                'booking_reference' => $booking->reference,
            ]);
        }

        return ['status' => 'refunded'];
    }

    /**
     * Process refund
     */
    public function refund(Booking $booking, ?float $amount = null): array
    {
        $refundAmount = $amount ?? $booking->total_ttc;

        if ($booking->payment_provider === PaymentProvider::Stripe->value) {
            return $this->refundStripe($booking, $refundAmount);
        }

        if ($booking->payment_provider === PaymentProvider::PayPal->value) {
            return $this->refundPayPal($booking, $refundAmount);
        }

        throw new \Exception(__('Provider de paiement non supporté.'));
    }

    /**
     * Process Stripe refund
     */
    protected function refundStripe(Booking $booking, float $amount): array
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            $refund = $stripe->refunds->create([
                'payment_intent' => $booking->payment_intent_id,
                'amount' => (int) ($amount * 100),
                'reason' => 'requested_by_customer',
            ]);

            Log::info('Stripe refund processed', [
                'booking_reference' => $booking->reference,
                'refund_id' => $refund->id,
                'amount' => $amount,
            ]);

            return [
                'status' => 'success',
                'refund_id' => $refund->id,
                'amount' => $amount,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe refund failed', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception(__('Erreur lors du remboursement. Veuillez réessayer.'));
        }
    }

    /**
     * Process PayPal refund
     */
    protected function refundPayPal(Booking $booking, float $amount): array
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $mode = config('services.paypal.mode', 'sandbox');

        $baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        try {
            $accessToken = $this->getPayPalAccessToken($baseUrl, $clientId, $secret);

            $orderResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->get("{$baseUrl}/v2/checkout/orders/{$booking->payment_intent_id}");

            if (!$orderResponse->successful()) {
                throw new \Exception('Could not retrieve PayPal order');
            }

            $captureId = $orderResponse->json('purchase_units.0.payments.captures.0.id');

            if (!$captureId) {
                throw new \Exception('No capture found for this order');
            }

            $refundResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/payments/captures/{$captureId}/refund", [
                    'amount' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency_code' => $booking->currency,
                    ],
                ]);

            if (!$refundResponse->successful()) {
                throw new \Exception($refundResponse->body());
            }

            $refund = $refundResponse->json();

            Log::info('PayPal refund processed', [
                'booking_reference' => $booking->reference,
                'refund_id' => $refund['id'],
                'amount' => $amount,
            ]);

            return [
                'status' => 'success',
                'refund_id' => $refund['id'],
                'amount' => $amount,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal refund failed', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception(__('Erreur lors du remboursement PayPal. Veuillez réessayer.'));
        }
    }
}
