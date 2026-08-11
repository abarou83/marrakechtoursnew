<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Jobs\SendBookingConfirmation;
use App\Jobs\GenerateVoucherPdf;

class SendBookingConfirmationListener
{
    public function handle(BookingConfirmed $event): void
    {
        SendBookingConfirmation::dispatch($event->booking);
        GenerateVoucherPdf::dispatch($event->booking);
    }
}
