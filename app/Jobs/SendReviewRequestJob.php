<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Mail\ReviewRequest;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReviewRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 300;

    public function __construct(
        public Booking $booking,
        public bool $isReminder = false
    ) {}

    public function handle(): void
    {
        if ($this->booking->status !== BookingStatus::Completed) {
            return;
        }

        if ($this->booking->review_requested_at && ! $this->isReminder) {
            return;
        }

        if ($this->booking->review()->exists()) {
            return;
        }

        Mail::to($this->booking->customer_email)
            ->locale($this->booking->locale ?? 'fr')
            ->send(new ReviewRequest($this->booking, $this->isReminder));

        $this->booking->update([
            'review_requested_at' => now(),
        ]);
    }

    public function tags(): array
    {
        return [
            'booking:' . $this->booking->id,
            'review-request',
            $this->isReminder ? 'reminder' : 'initial',
        ];
    }
}
