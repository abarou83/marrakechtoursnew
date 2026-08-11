<?php

declare(strict_types=1);

namespace App\Services\Channel;

use App\Contracts\ChannelSyncInterface;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

/**
 * Manual channel sync — logs operations for future API integrations.
 */
class ManualChannelSync implements ChannelSyncInterface
{
    public function __construct(
        protected string $channelName = 'manual'
    ) {}

    public function channel(): string
    {
        return $this->channelName;
    }

    public function pushBooking(Booking $booking): bool
    {
        Log::info('Manual channel sync: push booking', [
            'channel' => $this->channel(),
            'reference' => $booking->reference,
            'external_id' => $booking->channel_external_id,
        ]);

        return true;
    }

    public function pullBookings(?\DateTimeInterface $since = null): array
    {
        Log::info('Manual channel sync: pull bookings (stub)', [
            'channel' => $this->channel(),
            'since' => $since?->format('c'),
        ]);

        return [];
    }
}
