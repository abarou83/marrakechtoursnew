<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Mail\BookingConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Booking $booking,
    ) {}

    public function handle(): void
    {
        Mail::to($this->booking->customer_email)
            ->locale($this->booking->locale ?? 'fr')
            ->send(new BookingConfirmationMail($this->booking));
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send booking confirmation', [
            'booking_id' => $this->booking->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
