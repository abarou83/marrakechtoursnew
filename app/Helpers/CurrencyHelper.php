<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    public static function current(): ?Currency
    {
        // Prefer explicit query parameter for immediate switch on the current request
        $reqCode = request()->query('currency');
        if ($reqCode) {
            $code = strtoupper(substr($reqCode, 0, 3));
            $found = Currency::where('code', $code)->where('is_active', true)->first();
            if ($found) {
                return $found;
            }
        }

        $code = session('currency') ?: request()->cookie('currency');
        if (!$code) {
            return Currency::where('is_default', true)->first();
        }
        return Currency::where('code', $code)->first();
    }

    public static function convert(float $amount, string $fromCode = null, string $toCode = null): float
    {
        $from = $fromCode ? Currency::where('code', $fromCode)->first() : Currency::where('is_default', true)->first();
        $to = $toCode ? Currency::where('code', $toCode)->first() : self::current();

        if (!$from || !$to) {
            return $amount;
        }

        // Definition: rate_to_base = how many target-units per 1 base currency unit
        // Convert using relative ratio: amount_in_to = amount * (to.rate / from.rate)
        $fromRate = (float)($from->rate_to_base ?: 1);
        $toRate = (float)($to->rate_to_base ?: 1);
        if ($fromRate <= 0 || $toRate <= 0) {
            return $amount;
        }
        return $amount * ($toRate / $fromRate);
    }

    public static function format(float $amount, string $currencyCode = null): string
    {
        $currency = $currencyCode ? Currency::where('code', $currencyCode)->first() : self::current();
        $symbol = $currency?->symbol ?: '';
        $formatted = number_format($amount, 2, ',', ' ');
        return trim("{$formatted} {$symbol}");
    }
}
