<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Models\User;
use App\Mail\AdminNewBookingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAdminNewBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Booking $booking,
    ) {}

    public function handle(): void
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->send(new AdminNewBookingMail($this->booking));
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to notify admin of new booking', [
            'booking_id' => $this->booking->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
