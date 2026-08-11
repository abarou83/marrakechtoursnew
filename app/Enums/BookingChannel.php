<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingChannel: string
{
    case Direct = 'direct';
    case Viator = 'viator';
    case GetYourGuide = 'gyg';
    case WhatsApp = 'whatsapp';
    case Phone = 'phone';
    case GiftCard = 'gift_card';

    public function label(): string
    {
        return match ($this) {
            self::Direct => __('Direct (site web)'),
            self::Viator => 'Viator',
            self::GetYourGuide => 'GetYourGuide',
            self::WhatsApp => 'WhatsApp',
            self::Phone => __('Téléphone'),
            self::GiftCard => __('Carte cadeau'),
        };
    }

    public function isOta(): bool
    {
        return in_array($this, [self::Viator, self::GetYourGuide]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
