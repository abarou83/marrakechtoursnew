<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reminderType = '24h'
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->reminderType) {
            '24h' => __('Rappel : Votre excursion demain ! - :ref', ['ref' => $this->booking->reference]),
            '3h' => __('C\'est bientôt ! Votre excursion dans 3 heures - :ref', ['ref' => $this->booking->reference]),
            default => __('Rappel de réservation - :ref', ['ref' => $this->booking->reference]),
        };

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'contact@marrakechtours.net'),
                config('mail.from.name', 'MarrakechTours')
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.reminder',
            with: [
                'booking' => $this->booking,
                'tour' => $this->booking->tour,
                'reminderType' => $this->reminderType,
                'voucherUrl' => route('booking.voucher', $this->booking->reference),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
