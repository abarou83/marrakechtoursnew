<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Booking;

interface ChannelSyncInterface
{
    /**
     * Push a booking to an external OTA channel.
     */
    public function pushBooking(Booking $booking): bool;

    /**
     * Pull pending bookings from an external OTA channel.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pullBookings(?\DateTimeInterface $since = null): array;

    /**
     * Channel identifier (viator, gyg, etc.).
     */
    public function channel(): string;
}
