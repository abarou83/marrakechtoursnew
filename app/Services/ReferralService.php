<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Client;
use App\Models\ReferralUsage;
use Illuminate\Support\Str;

class ReferralService
{
    public function getOrCreateCode(Client $client): string
    {
        if ($client->referral_code) {
            return $client->referral_code;
        }

        do {
            $code = 'REF' . strtoupper(Str::random(6));
        } while (Client::where('referral_code', $code)->exists());

        $client->update(['referral_code' => $code]);

        return $code;
    }

    public function getShareUrl(Client $client): string
    {
        $code = $this->getOrCreateCode($client);

        return route('home', ['ref' => $code]);
    }

    public function findReferrerByCode(string $code): ?Client
    {
        return Client::where('referral_code', strtoupper($code))->first();
    }

    public function applyReferralDiscount(string $code, float $total, ?Client $referredClient = null): array
    {
        $referrer = $this->findReferrerByCode($code);

        if (!$referrer) {
            return ['valid' => false, 'error' => __('Code de parrainage invalide.')];
        }

        if ($referredClient && $referrer->id === $referredClient->id) {
            return ['valid' => false, 'error' => __('Vous ne pouvez pas utiliser votre propre code.')];
        }

        if ($referredClient && ReferralUsage::where('referred_client_id', $referredClient->id)->exists()) {
            return ['valid' => false, 'error' => __('Vous avez déjà utilisé un code de parrainage.')];
        }

        $percent = config('marketing.referral.referred_discount_percent', 10);
        $discount = round($total * ($percent / 100), 2);

        return [
            'valid' => true,
            'referrer' => $referrer,
            'code' => strtoupper($code),
            'discount' => $discount,
            'percent' => $percent,
        ];
    }

    public function recordUsage(Booking $booking, Client $referrer, float $discount): ReferralUsage
    {
        return ReferralUsage::create([
            'referrer_client_id' => $referrer->id,
            'referred_client_id' => $booking->client_id,
            'booking_id' => $booking->id,
            'code' => $booking->referral_code,
            'discount_amount' => $discount,
            'reward_amount' => config('marketing.referral.referrer_reward', 10),
            'status' => 'pending',
        ]);
    }

    public function getStatsForClient(Client $client): array
    {
        $usages = ReferralUsage::where('referrer_client_id', $client->id)->get();

        return [
            'code' => $this->getOrCreateCode($client),
            'share_url' => $this->getShareUrl($client),
            'total_referrals' => $usages->count(),
            'pending_rewards' => $usages->where('status', 'pending')->sum('reward_amount'),
            'earned_rewards' => $usages->where('status', 'rewarded')->sum('reward_amount'),
        ];
    }
}
