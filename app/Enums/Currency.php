<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case MAD = 'MAD';

    public function symbol(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::USD => '$',
            self::GBP => '£',
            self::MAD => 'DH',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::EUR => 'Euro',
            self::USD => 'US Dollar',
            self::GBP => 'British Pound',
            self::MAD => 'Dirham Marocain',
        };
    }

    public function position(): string
    {
        return match ($this) {
            self::EUR, self::GBP => 'after',
            self::USD => 'before',
            self::MAD => 'after',
        };
    }

    public function decimals(): int
    {
        return match ($this) {
            self::MAD => 0,
            default => 2,
        };
    }

    public function format(float $amount): string
    {
        $formatted = number_format($amount, $this->decimals(), ',', ' ');

        return match ($this->position()) {
            'before' => $this->symbol() . $formatted,
            'after' => $formatted . ' ' . $this->symbol(),
        };
    }

    public static function default(): self
    {
        return self::EUR;
    }
}
