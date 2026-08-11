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
use Illuminate\Support\Facades\URL;

class ReviewRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public bool $isReminder = false
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isReminder
            ? __('N\'oubliez pas de partager votre avis !')
            : __('Comment s\'est passée votre excursion ?');

        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.bookings.review-request',
            with: [
                'booking' => $this->booking,
                'tour' => $this->booking->tour,
                'customerName' => $this->booking->customer_name,
                'reviewUrl' => $this->getReviewUrl(),
                'isReminder' => $this->isReminder,
                'promoCode' => $this->generatePromoCode(),
            ],
        );
    }

    protected function getReviewUrl(): string
    {
        return URL::signedRoute('reviews.create', [
            'locale' => $this->booking->locale ?? 'fr',
            'booking' => $this->booking->id,
        ], now()->addDays(30));
    }

    protected function generatePromoCode(): string
    {
        return 'MERCI' . strtoupper(substr(md5((string) $this->booking->id), 0, 6));
    }
}
