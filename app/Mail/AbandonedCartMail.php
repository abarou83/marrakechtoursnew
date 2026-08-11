<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AbandonedCart $cart
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: __('Votre réservation vous attend — :tour', [
                'tour' => $this->cart->tour?->translate()?->title ?? $this->cart->tour?->title ?? 'MarrakechTours',
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.marketing.abandoned-cart',
            with: [
                'cart' => $this->cart,
                'tour' => $this->cart->tour,
                'bookingUrl' => route('tours.booking.wizard', $this->cart->tour),
                'promoPercent' => config('marketing.abandoned_cart.promo_percent', 5),
            ],
        );
    }
}
