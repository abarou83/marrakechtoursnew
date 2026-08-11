<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'contact@marrakechtours.net'),
                config('mail.from.name', 'MarrakechTours')
            ),
            subject: __('Confirmation de réservation :ref - MarrakechTours', ['ref' => $this->booking->reference]),
            replyTo: [
                new Address('contact@marrakechtours.net', 'MarrakechTours Support'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.confirmation',
            with: [
                'booking' => $this->booking,
                'tour' => $this->booking->tour,
                'url' => route('booking.confirmation', $this->booking->reference),
                'voucherUrl' => route('booking.voucher', $this->booking->reference),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.voucher', ['booking' => $this->booking]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "voucher-{$this->booking->reference}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
