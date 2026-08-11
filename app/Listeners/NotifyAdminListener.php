<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Jobs\NotifyAdminNewBooking;

class NotifyAdminListener
{
    public function handle(BookingCreated $event): void
    {
        NotifyAdminNewBooking::dispatch($event->booking);
    }
}
