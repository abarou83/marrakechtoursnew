<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Jobs\SendBookingReminderJob;
use App\Jobs\SendReviewRequestJob;
use App\Models\Booking;
use Illuminate\Console\Command;

class SendScheduledEmails extends Command
{
    protected $signature = 'bookings:send-scheduled-emails';

    protected $description = 'Send scheduled booking reminders and review requests';

    public function handle(): int
    {
        $this->sendJ7Reminders();
        $this->sendJ1Reminders();
        $this->sendJ1ReviewRequests();
        $this->sendJ7ReviewReminders();

        return self::SUCCESS;
    }

    protected function sendJ7Reminders(): void
    {
        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('travel_date', now()->addDays(7)->toDateString())
            ->whereNull('last_reminder_sent_at')
            ->get();

        foreach ($bookings as $booking) {
            SendBookingReminderJob::dispatch($booking, 'j-7');
        }

        $this->info("Dispatched J-7 reminders for {$bookings->count()} bookings");
    }

    protected function sendJ1Reminders(): void
    {
        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('travel_date', now()->addDay()->toDateString())
            ->where(function ($q) {
                $q->whereNull('last_reminder_sent_at')
                    ->orWhere('reminder_type', '!=', 'j-1');
            })
            ->get();

        foreach ($bookings as $booking) {
            SendBookingReminderJob::dispatch($booking, 'j-1');
        }

        $this->info("Dispatched J-1 reminders for {$bookings->count()} bookings");
    }

    protected function sendJ1ReviewRequests(): void
    {
        $bookings = Booking::query()
            ->where('status', BookingStatus::Completed)
            ->whereDate('travel_date', now()->subDay()->toDateString())
            ->whereNull('review_requested_at')
            ->doesntHave('review')
            ->get();

        foreach ($bookings as $booking) {
            SendReviewRequestJob::dispatch($booking, false);
        }

        $this->info("Dispatched J+1 review requests for {$bookings->count()} bookings");
    }

    protected function sendJ7ReviewReminders(): void
    {
        $bookings = Booking::query()
            ->where('status', BookingStatus::Completed)
            ->whereDate('travel_date', now()->subDays(7)->toDateString())
            ->whereNotNull('review_requested_at')
            ->doesntHave('review')
            ->get();

        foreach ($bookings as $booking) {
            SendReviewRequestJob::dispatch($booking, true);
        }

        $this->info("Dispatched J+7 review reminders for {$bookings->count()} bookings");
    }
}
