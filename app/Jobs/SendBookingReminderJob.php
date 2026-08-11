<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Mail\BookingReminder;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 300;

    public function __construct(
        public Booking $booking,
        public string $reminderType
    ) {}

    public function handle(): void
    {
        if ($this->booking->status !== BookingStatus::Confirmed) {
            return;
        }

        if ($this->booking->tour_date < now()) {
            return;
        }

        Mail::to($this->booking->customer_email)
            ->locale($this->booking->locale ?? 'fr')
            ->send(new BookingReminder($this->booking, $this->reminderType));

        $this->booking->update([
            'last_reminder_sent_at' => now(),
            'reminder_type' => $this->reminderType,
        ]);
    }

    public function tags(): array
    {
        return [
            'booking:' . $this->booking->id,
            'reminder:' . $this->reminderType,
        ];
    }
}
