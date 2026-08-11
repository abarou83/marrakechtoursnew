<?php

declare(strict_types=1);

namespace App\Services;

class UtmService
{
    public function getFromSession(): array
    {
        return session('utm', []);
    }

    public function getChannel(): string
    {
        return session('booking_channel', 'direct');
    }

    public function getReferralCode(): ?string
    {
        return session('referral_code');
    }

    public function applyToBookingData(array $data): array
    {
        $utm = $this->getFromSession();

        $data['channel'] = $this->getChannel();
        $data['utm_source'] = $utm['source'] ?? null;
        $data['utm_medium'] = $utm['medium'] ?? null;
        $data['utm_campaign'] = $utm['campaign'] ?? null;
        $data['referral_code'] = $data['referral_code'] ?? $this->getReferralCode();

        return $data;
    }
}
