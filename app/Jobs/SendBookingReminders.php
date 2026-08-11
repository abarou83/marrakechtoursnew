<?php

namespace App\Jobs;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendBookingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $reminderType = '24h'
    ) {}

    public function handle(): void
    {
        $now = now();
        
        $query = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNotNull('customer_email')
            ->with(['tour.translations']);

        if ($this->reminderType === '24h') {
            $targetDate = $now->copy()->addDay()->toDateString();
            $reminderColumn = 'reminder_24h_sent_at';
        } else {
            $targetDate = $now->toDateString();
            $reminderColumn = 'reminder_3h_sent_at';
        }

        $bookings = $query
            ->whereDate('travel_date', $targetDate)
            ->whereNull($reminderColumn)
            ->get();

        Log::info("Sending {$this->reminderType} booking reminders", [
            'count' => $bookings->count(),
            'target_date' => $targetDate,
        ]);

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->customer_email)
                    ->queue(new BookingReminderMail($booking, $this->reminderType));

                $booking->update([
                    $reminderColumn => now(),
                ]);

                Log::info("Reminder sent for booking", [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                    'type' => $this->reminderType,
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to send reminder", [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
