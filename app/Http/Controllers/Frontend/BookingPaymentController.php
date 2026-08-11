<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingConfirmed;
use App\Services\GiftCardService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingPaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function success(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        if ($booking->payment_provider === 'paypal' && $booking->payment_status !== PaymentStatus::Paid) {
            $token = $request->query('token');
            if ($token) {
                $this->capturePayPalOrder($booking, $token);
            }
        }

        if ($booking->payment_status === PaymentStatus::Paid) {
            return redirect()->route('booking.confirmation', $reference);
        }

        return redirect()
            ->route('tours.booking.wizard', $booking->tour)
            ->with('error', __('Le paiement n\'a pas pu être confirmé. Contactez-nous avec la référence :reference.', ['reference' => $reference]));
    }

    public function cancel(string $reference)
    {
        $booking = Booking::with('tour')->where('reference', $reference)->firstOrFail();

        return redirect()
            ->route('tours.booking.wizard', $booking->tour)
            ->with('error', __('Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.'));
    }

    protected function capturePayPalOrder(Booking $booking, string $orderId): void
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $mode = config('services.paypal.mode', 'sandbox');
        $baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        try {
            $tokenResponse = Http::asForm()
                ->withBasicAuth($clientId, $secret)
                ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if (!$tokenResponse->successful()) {
                return;
            }

            $accessToken = $tokenResponse->json('access_token');
            $captureResponse = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if (!$captureResponse->successful()) {
                Log::error('PayPal capture failed', ['reference' => $booking->reference, 'body' => $captureResponse->body()]);
                return;
            }

            $booking->update([
                'status' => BookingStatus::Confirmed,
                'payment_status' => PaymentStatus::Paid,
                'confirmed_at' => now(),
                'payment_intent_id' => $orderId,
            ]);

            $this->redeemGiftCardIfNeeded($booking);
            event(new BookingConfirmed($booking));
        } catch (\Exception $e) {
            Log::error('PayPal capture exception', ['reference' => $booking->reference, 'error' => $e->getMessage()]);
        }
    }

    protected function redeemGiftCardIfNeeded(Booking $booking): void
    {
        if (!$booking->gift_card_id || $booking->gift_card_amount <= 0) {
            return;
        }

        $giftCard = $booking->giftCard;
        if ($giftCard) {
            app(GiftCardService::class)->redeem(
                $giftCard,
                (float) $booking->gift_card_amount,
                $booking->client_id
            );
        }
    }
}
