<?php

use App\Jobs\SendBookingReminders;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send 24h booking reminders at 10:00 AM every day
Schedule::job(new SendBookingReminders('24h'))
    ->dailyAt('10:00')
    ->name('booking-reminders-24h')
    ->withoutOverlapping();

// Send 3h booking reminders every hour from 6:00 AM to 6:00 PM
Schedule::job(new SendBookingReminders('3h'))
    ->hourly()
    ->between('06:00', '18:00')
    ->name('booking-reminders-3h')
    ->withoutOverlapping();

// Close expired availabilities daily at midnight
Schedule::call(function () {
    app(AvailabilityService::class)->closeExpiredAvailabilities();
})->dailyAt('00:01')
    ->name('close-expired-availabilities');

// Purge expired GDPR exports daily
Schedule::command('gdpr:purge-exports')
    ->dailyAt('03:00')
    ->name('purge-gdpr-exports');

// Marketing: abandoned cart recovery every 2 hours
Schedule::job(new \App\Jobs\SendAbandonedCartRecovery())
    ->everyTwoHours()
    ->name('abandoned-cart-recovery')
    ->withoutOverlapping();

// Marketing: lifecycle emails (J-7, J-1, J+1 review, J+7 review reminder)
Schedule::command('bookings:send-scheduled-emails')
    ->dailyAt('09:00')
    ->name('booking-lifecycle-emails');

// Queue worker for o2switch cron (processes pending jobs then exits)
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')
    ->everyMinute()
    ->name('queue-worker')
    ->withoutOverlapping();
