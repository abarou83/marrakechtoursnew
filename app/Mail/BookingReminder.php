<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reminderType
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->reminderType) {
            'j-7' => __('Votre excursion approche - J-7'),
            'j-1' => __('C\'est demain ! Dernières informations'),
            default => __('Rappel de votre réservation'),
        };

        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: $subject . ' | ' . $this->booking->tour?->name,
        );
    }

    public function content(): Content
    {
        $view = match ($this->reminderType) {
            'j-7' => 'emails.bookings.reminder-j7',
            'j-1' => 'emails.bookings.reminder-j1',
            default => 'emails.bookings.reminder',
        };

        return new Content(
            markdown: $view,
            with: [
                'booking' => $this->booking,
                'tour' => $this->booking->tour,
                'tourDate' => $this->booking->tour_date,
                'customerName' => $this->booking->customer_name,
                'voucherCode' => $this->booking->voucher_code,
                'whatsappLink' => $this->getWhatsAppLink(),
            ],
        );
    }

    protected function getWhatsAppLink(): string
    {
        $phone = config('services.whatsapp.number', '+212600000000');
        $message = urlencode(__('Bonjour, j\'ai une question concernant ma réservation :reference', [
            'reference' => $this->booking->reference,
        ]));

        return "https://wa.me/{$phone}?text={$message}";
    }
}
