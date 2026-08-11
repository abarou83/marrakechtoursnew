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

class AdminNewBookingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[NOUVELLE RÉSERVATION] {$this->booking->reference} - {$this->booking->tour->title}",
            to: [
                new Address(
                    config('mail.admin.address', 'admin@marrakechtours.net'),
                    config('mail.admin.name', 'Admin MarrakechTours')
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-booking',
            with: [
                'booking' => $this->booking,
                'tour' => $this->booking->tour,
                'adminUrl' => route('admin.bookings.show', $this->booking->id),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
