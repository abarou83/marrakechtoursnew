<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GiftCard;
use App\Models\Client;

class GiftCardService
{
    public function findValidCode(string $code): ?GiftCard
    {
        return GiftCard::where('code', strtoupper(trim($code)))
            ->active()
            ->first();
    }

    public function validateForAmount(string $code, float $amount): array
    {
        $giftCard = $this->findValidCode($code);

        if (!$giftCard) {
            return ['valid' => false, 'error' => __('Carte cadeau invalide ou expirée.')];
        }

        if (!$giftCard->isValid()) {
            return ['valid' => false, 'error' => __('Cette carte cadeau n\'est plus utilisable.')];
        }

        $applicable = min($amount, (float) $giftCard->remaining_amount);

        return [
            'valid' => true,
            'gift_card' => $giftCard,
            'amount' => $applicable,
            'remaining_after' => (float) $giftCard->remaining_amount - $applicable,
        ];
    }

    public function purchase(array $data, ?Client $client = null): GiftCard
    {
        return GiftCard::create([
            'initial_amount' => $data['amount'],
            'remaining_amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'EUR',
            'purchaser_client_id' => $client?->id,
            'recipient_name' => $data['recipient_name'] ?? null,
            'recipient_email' => $data['recipient_email'] ?? null,
            'message' => $data['message'] ?? null,
            'expires_at' => now()->addMonths(config('marketing.gift_card.validity_months', 12)),
            'is_active' => true,
        ]);
    }

    public function redeem(GiftCard $giftCard, float $amount, ?int $clientId = null): float
    {
        return $giftCard->redeem($amount, $clientId);
    }
}
