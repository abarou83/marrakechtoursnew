<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateVoucherPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Booking $booking,
    ) {}

    public function handle(): void
    {
        $booking = $this->booking->load(['tour', 'client', 'addons.addon']);

        $pdf = Pdf::loadView('pdf.voucher', [
            'booking' => $booking,
        ]);

        $filename = "vouchers/{$booking->reference}.pdf";

        Storage::put($filename, $pdf->output());

        $booking->update([
            'voucher_path' => $filename,
        ]);

        \Log::info('Voucher generated', [
            'booking_id' => $booking->id,
            'path' => $filename,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to generate voucher', [
            'booking_id' => $this->booking->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
