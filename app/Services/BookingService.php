<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tour;
use App\Models\Booking;
use App\Models\Client;
use App\Models\PromoCode;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected PricingService $pricingService,
        protected AvailabilityService $availabilityService,
    ) {}

    /**
     * Create a new booking with all necessary validations and locks
     * CRITICAL: Uses transaction + lockForUpdate to prevent double-booking
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $tour = Tour::findOrFail($data['tour_id']);
            $travelDate = Carbon::parse($data['travel_date']);

            if (!$this->availabilityService->isWithinBookingDeadline($tour, $travelDate)) {
                throw new \Exception(__('La date limite de réservation est dépassée.'));
            }

            $totalPeople = $this->calculateTotalPeople($data);

            $this->availabilityService->reserveSpots($tour, $travelDate, $totalPeople);

            $priceCalculation = $this->pricingService->calculatePrice(
                $tour,
                $data['pricing_mode'],
                $travelDate,
                $data['adults'],
                $data['children'] ?? 0,
                $data['infants'] ?? 0,
                $data['addons'] ?? [],
                $data['pricing_id'] ?? null,
                $data['accommodation'] ?? null
            );

            $discount = 0;
            $promoCode = null;
            $referralCode = null;
            $referrer = null;
            $giftCardId = null;
            $giftCardAmount = 0;

            if (!empty($data['promo_code'])) {
                $promoCode = $this->validateAndApplyPromoCode(
                    $data['promo_code'],
                    $tour,
                    $priceCalculation['total_price']
                );
                $discount = $this->calculateDiscount($promoCode, $priceCalculation['total_price']);
            } elseif (!empty($data['referral_code'])) {
                $referralService = app(ReferralService::class);
                $client = !empty($data['client_id']) ? Client::find($data['client_id']) : null;
                $referralResult = $referralService->applyReferralDiscount(
                    $data['referral_code'],
                    $priceCalculation['total_price'],
                    $client
                );
                if ($referralResult['valid']) {
                    $discount = $referralResult['discount'];
                    $referralCode = $referralResult['code'];
                    $referrer = $referralResult['referrer'];
                }
            }

            $totalHT = $priceCalculation['total_price'] - $discount;
            $taxRate = config('booking.tax_rate', 0);
            $taxAmount = $totalHT * $taxRate;
            $totalTTC = $totalHT + $taxAmount;

            if (!empty($data['gift_card_code'])) {
                $giftCardService = app(GiftCardService::class);
                $giftCardResult = $giftCardService->validateForAmount($data['gift_card_code'], $totalTTC);
                if ($giftCardResult['valid']) {
                    $giftCardAmount = $giftCardResult['amount'];
                    $giftCardId = $giftCardResult['gift_card']->id;
                    $totalTTC = max(0, $totalTTC - $giftCardAmount);
                    $totalHT = max(0, $totalHT - $giftCardAmount);
                }
            }

            $utmService = app(UtmService::class);
            $data = $utmService->applyToBookingData($data);

            $paymentType = $data['payment_type'] ?? 'full';
            $depositAmount = null;
            if ($paymentType === 'deposit' && config('marketing.deposit.enabled')) {
                $depositPercent = config('marketing.deposit.percent', 20);
                $depositAmount = round($totalTTC * ($depositPercent / 100), 2);
            }

            $booking = Booking::create([
                'reference' => $this->generateReference(),
                'tour_id' => $tour->id,
                'client_id' => $data['client_id'] ?? null,
                'pricing_id' => $priceCalculation['pricing_id'],
                'pricing_mode' => $data['pricing_mode'],
                'travel_date' => $travelDate,
                'adults' => $data['adults'],
                'children' => $data['children'] ?? 0,
                'infants' => $data['infants'] ?? 0,
                'base_price' => $priceCalculation['base_price'],
                'addons_total' => $priceCalculation['addons_total'],
                'accommodation_total' => $priceCalculation['accommodation_total'] ?? 0,
                'discount_amount' => $discount,
                'total_ht' => round($totalHT, 2),
                'tax_amount' => round($taxAmount, 2),
                'total_ttc' => round($totalTTC, 2),
                'currency' => $data['currency'] ?? 'EUR',
                'exchange_rate' => $data['exchange_rate'] ?? 1.0,
                'promo_code_id' => $promoCode?->id,
                'status' => BookingStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'country_code' => $data['country_code'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'special_requests' => $data['special_requests'] ?? null,
                'price_breakdown' => json_encode($priceCalculation),
                'locale' => app()->getLocale(),
                'channel' => $data['channel'] ?? 'direct',
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'referral_code' => $referralCode ?? $data['referral_code'] ?? null,
                'gift_card_id' => $giftCardId,
                'gift_card_amount' => $giftCardAmount,
                'deposit_amount' => $depositAmount,
                'payment_type' => $paymentType,
            ]);

            if (!empty($priceCalculation['addons'])) {
                foreach ($priceCalculation['addons'] as $addon) {
                    $booking->addons()->create([
                        'addon_id' => $addon['addon_id'],
                        'quantity' => $addon['quantity'],
                        'unit_price' => $addon['unit_price'],
                        'total_price' => $addon['total_price'],
                    ]);
                }
            }

            if (!empty($priceCalculation['accommodation_rooms'])) {
                foreach ($priceCalculation['accommodation_rooms'] as $room) {
                    $booking->accommodations()->create([
                        'accommodation_id' => $room['accommodation_id'],
                        'room_id' => $room['room_id'],
                        'room_type' => $room['room_type'],
                        'quantity' => $room['quantity'],
                        'price_per_night' => $room['price_per_night'],
                        'nights' => $room['nights'],
                        'subtotal' => $room['subtotal'],
                    ]);
                }
            }

            if ($promoCode) {
                $promoCode->increment('used_count');
            }

            if ($referralCode && $referrer) {
                app(ReferralService::class)->recordUsage($booking, $referrer, $discount);
            }

            Log::info('Booking created', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'tour_id' => $tour->id,
                'total_ttc' => $totalTTC,
            ]);

            return $booking;
        }, 5);
    }

    /**
     * Confirm a booking after successful payment
     */
    public function confirmBooking(Booking $booking, string $paymentIntentId): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Confirmed)) {
            throw new \Exception(__('Cette réservation ne peut pas être confirmée.'));
        }

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::Paid,
            'payment_intent_id' => $paymentIntentId,
            'confirmed_at' => now(),
        ]);

        Log::info('Booking confirmed', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'payment_intent_id' => $paymentIntentId,
        ]);

        return $booking->fresh();
    }

    /**
     * Cancel a booking and release spots
     */
    public function cancelBooking(Booking $booking, string $reason = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Cancelled)) {
            throw new \Exception(__('Cette réservation ne peut pas être annulée.'));
        }

        return DB::transaction(function () use ($booking, $reason) {
            $totalPeople = $this->calculateTotalPeopleFromBooking($booking);

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

            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'reason' => $reason,
            ]);

            return $booking->fresh();
        }, 5);
    }

    /**
     * Process refund for a cancelled booking
     */
    public function refundBooking(Booking $booking, float $amount = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Refunded)) {
            throw new \Exception(__('Cette réservation ne peut pas être remboursée.'));
        }

        $refundAmount = $amount ?? $booking->total_ttc;

        $booking->update([
            'status' => BookingStatus::Refunded,
            'payment_status' => PaymentStatus::Refunded,
            'refunded_at' => now(),
            'refund_amount' => $refundAmount,
        ]);

        Log::info('Booking refunded', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'refund_amount' => $refundAmount,
        ]);

        return $booking->fresh();
    }

    /**
     * Generate unique booking reference
     * Format: MTK-YYYY-XXXXX (ex: MTK-2026-00421)
     */
    protected function generateReference(): string
    {
        $prefix = 'MTK';
        $year = now()->year;

        $lastBooking = Booking::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastBooking ? ((int) substr($lastBooking->reference, -5)) + 1 : 1;

        return sprintf('%s-%d-%05d', $prefix, $year, $sequence);
    }

    /**
     * Validate and get promo code
     */
    protected function validateAndApplyPromoCode(string $code, Tour $tour, float $subtotal): ?PromoCode
    {
        $promoCode = PromoCode::where('code', strtoupper($code))
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (!$promoCode) {
            throw new \Exception(__('Code promo invalide ou expiré.'));
        }

        if ($promoCode->max_uses && $promoCode->used_count >= $promoCode->max_uses) {
            throw new \Exception(__('Ce code promo a atteint sa limite d\'utilisation.'));
        }

        if ($promoCode->min_amount && $subtotal < $promoCode->min_amount) {
            throw new \Exception(__('Le montant minimum pour ce code promo est de :amount €.', [
                'amount' => $promoCode->min_amount,
            ]));
        }

        if ($promoCode->tour_ids) {
            $allowedTours = json_decode($promoCode->tour_ids, true);
            if (!in_array($tour->id, $allowedTours)) {
                throw new \Exception(__('Ce code promo n\'est pas valide pour ce tour.'));
            }
        }

        return $promoCode;
    }

    /**
     * Calculate discount amount from promo code
     */
    protected function calculateDiscount(PromoCode $promoCode, float $subtotal): float
    {
        if ($promoCode->type === 'percent') {
            return round($subtotal * ($promoCode->value / 100), 2);
        }

        return min($promoCode->value, $subtotal);
    }

    /**
     * Calculate total people from booking data
     */
    protected function calculateTotalPeople(array $data): int
    {
        return ($data['adults'] ?? 0) + ($data['children'] ?? 0) + ($data['infants'] ?? 0);
    }

    /**
     * Calculate total people from existing booking
     */
    protected function calculateTotalPeopleFromBooking(Booking $booking): int
    {
        return $booking->adults + $booking->children + $booking->infants;
    }

    /**
     * Get booking by reference
     */
    public function findByReference(string $reference): ?Booking
    {
        return Booking::where('reference', $reference)->first();
    }
}
