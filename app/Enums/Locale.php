<?php

declare(strict_types=1);

namespace App\Enums;

enum Locale: string
{
    case FR = 'fr';
    case EN = 'en';
    case ES = 'es';
    case AR = 'ar';

    public function label(): string
    {
        return match ($this) {
            self::FR => 'Français',
            self::EN => 'English',
            self::ES => 'Español',
            self::AR => 'العربية',
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::FR => '🇫🇷',
            self::EN => '🇬🇧',
            self::ES => '🇪🇸',
            self::AR => '🇲🇦',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::AR => 'rtl',
            default => 'ltr',
        };
    }

    public function isRtl(): bool
    {
        return $this->direction() === 'rtl';
    }

    public function dateFormat(): string
    {
        return match ($this) {
            self::FR => 'd/m/Y',
            self::EN => 'm/d/Y',
            self::ES => 'd/m/Y',
            self::AR => 'Y/m/d',
        };
    }

    public function defaultCurrency(): Currency
    {
        return match ($this) {
            self::FR => Currency::EUR,
            self::EN => Currency::GBP,
            self::ES => Currency::EUR,
            self::AR => Currency::MAD,
        };
    }

    public static function default(): self
    {
        return self::FR;
    }

    public static function fromCountryCode(string $countryCode): self
    {
        return match (strtoupper($countryCode)) {
            'FR', 'BE', 'CH', 'LU', 'MC' => self::FR,
            'GB', 'US', 'CA', 'AU', 'NZ', 'IE' => self::EN,
            'ES', 'MX', 'AR', 'CO', 'PE', 'CL' => self::ES,
            'MA', 'DZ', 'TN', 'EG', 'SA', 'AE', 'QA', 'KW' => self::AR,
            default => self::FR,
        };
    }
}
