<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;

class GeoService
{
    protected array $countryLocaleMap = [
        'FR' => 'fr', 'BE' => 'fr', 'CH' => 'fr', 'LU' => 'fr', 'MC' => 'fr', 'CA' => 'fr',
        'GB' => 'en', 'UK' => 'en', 'US' => 'en', 'AU' => 'en', 'NZ' => 'en', 'IE' => 'en',
        'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'PE' => 'es', 'CL' => 'es',
        'MA' => 'fr', 'DZ' => 'fr', 'TN' => 'fr',
        'SA' => 'ar', 'AE' => 'ar', 'KW' => 'ar', 'QA' => 'ar', 'BH' => 'ar', 'OM' => 'ar',
        'EG' => 'ar', 'JO' => 'ar', 'LB' => 'ar', 'IQ' => 'ar',
    ];

    protected array $countryCurrencyMap = [
        'FR' => 'EUR', 'BE' => 'EUR', 'ES' => 'EUR', 'IT' => 'EUR', 'DE' => 'EUR', 'NL' => 'EUR',
        'PT' => 'EUR', 'AT' => 'EUR', 'IE' => 'EUR', 'LU' => 'EUR', 'FI' => 'EUR', 'GR' => 'EUR',
        'GB' => 'GBP', 'UK' => 'GBP',
        'US' => 'USD', 'CA' => 'USD', 'AU' => 'USD', 'NZ' => 'USD',
        'SA' => 'USD', 'AE' => 'USD', 'KW' => 'USD', 'QA' => 'USD',
        'MA' => 'MAD',
        'CH' => 'EUR',
        'MX' => 'USD', 'AR' => 'USD', 'CO' => 'USD',
    ];

    protected array $countryPhonePrefixMap = [
        'FR' => '+33', 'BE' => '+32', 'CH' => '+41', 'ES' => '+34',
        'GB' => '+44', 'UK' => '+44', 'US' => '+1', 'CA' => '+1',
        'MA' => '+212', 'SA' => '+966', 'AE' => '+971',
    ];

    protected array $countryTimezoneMap = [
        'FR' => 'Europe/Paris', 'BE' => 'Europe/Brussels', 'CH' => 'Europe/Zurich',
        'GB' => 'Europe/London', 'UK' => 'Europe/London', 'US' => 'America/New_York',
        'ES' => 'Europe/Madrid', 'MA' => 'Africa/Casablanca',
        'SA' => 'Asia/Riyadh', 'AE' => 'Asia/Dubai',
    ];

    protected array $whatsappContacts = [
        'fr' => '+212 XXX-XXXXXX',
        'en' => '+212 XXX-XXXXXX',
        'es' => '+212 XXX-XXXXXX',
        'ar' => '+212 XXX-XXXXXX',
    ];

    /**
     * Detect geo information from IP address
     */
    public function detectFromIp(?string $ip = null): array
    {
        $ip = $ip ?? request()->ip();

        if ($ip === '127.0.0.1' || $ip === '::1') {
            return $this->getDefaultGeoData();
        }

        $cacheKey = "geo:ip:{$ip}";

        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                $position = Location::get($ip);

                if (!$position) {
                    return $this->getDefaultGeoData();
                }

                $countryCode = $position->countryCode ?? 'FR';

                return $this->buildGeoData($countryCode);
            } catch (\Exception $e) {
                return $this->getDefaultGeoData();
            }
        });
    }

    /**
     * Build geo data from country code
     */
    public function buildGeoData(string $countryCode): array
    {
        $countryCode = strtoupper($countryCode);

        $locale = $this->countryLocaleMap[$countryCode] ?? 'fr';
        $currency = $this->countryCurrencyMap[$countryCode] ?? 'EUR';
        $timezone = $this->countryTimezoneMap[$countryCode] ?? 'Europe/Paris';
        $phonePrefix = $this->countryPhonePrefixMap[$countryCode] ?? '+33';

        return [
            'country_code' => $countryCode,
            'locale' => $locale,
            'currency' => $currency,
            'timezone' => $timezone,
            'phone_prefix' => $phonePrefix,
            'whatsapp_contact' => $this->whatsappContacts[$locale] ?? $this->whatsappContacts['fr'],
            'is_rtl' => $locale === 'ar',
        ];
    }

    /**
     * Get default geo data (French)
     */
    public function getDefaultGeoData(): array
    {
        return [
            'country_code' => 'FR',
            'locale' => 'fr',
            'currency' => 'EUR',
            'timezone' => 'Europe/Paris',
            'phone_prefix' => '+33',
            'whatsapp_contact' => $this->whatsappContacts['fr'],
            'is_rtl' => false,
        ];
    }

    /**
     * Resolve geo settings with priority cascade
     * Priority: cookie preference → URL locale → Accept-Language → GeoIP → default
     */
    public function resolveGeoSettings(): array
    {
        if ($cookieLocale = request()->cookie('preferred_locale')) {
            $cookieCurrency = request()->cookie('preferred_currency', 'EUR');
            return [
                'locale' => $cookieLocale,
                'currency' => $cookieCurrency,
                'source' => 'cookie',
            ];
        }

        $urlLocale = request()->segment(1);
        if (in_array($urlLocale, ['fr', 'en', 'es', 'ar'])) {
            return [
                'locale' => $urlLocale,
                'currency' => session('currency', 'EUR'),
                'source' => 'url',
            ];
        }

        $acceptLanguage = request()->getPreferredLanguage(['fr', 'en', 'es', 'ar']);
        if ($acceptLanguage) {
            $geoData = $this->detectFromIp();
            return [
                'locale' => $acceptLanguage,
                'currency' => $geoData['currency'],
                'source' => 'accept-language',
            ];
        }

        $geoData = $this->detectFromIp();
        return [
            'locale' => $geoData['locale'],
            'currency' => $geoData['currency'],
            'source' => 'geoip',
        ];
    }

    /**
     * Get contact information for a specific locale
     */
    public function getContactInfo(string $locale): array
    {
        $whatsapp = $this->whatsappContacts[$locale] ?? $this->whatsappContacts['fr'];

        return [
            'whatsapp' => $whatsapp,
            'whatsapp_url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp),
            'email' => 'contact@marrakechtours.net',
            'phone' => '+212 XXX-XXXXXX',
            'address' => 'Médina de Marrakech, 40000 Marrakech, Maroc',
        ];
    }

    /**
     * Get exchange rate for a currency
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $cacheKey = "exchange_rate:{$fromCurrency}:{$toCurrency}";

        return Cache::remember($cacheKey, 21600, function () use ($fromCurrency, $toCurrency) {
            try {
                $response = Http::timeout(5)
                    ->get("https://api.exchangerate.host/latest", [
                        'base' => $fromCurrency,
                        'symbols' => $toCurrency,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'][$toCurrency] ?? 1.0;
                }
            } catch (\Exception $e) {
            }

            return $this->getFallbackRate($fromCurrency, $toCurrency);
        });
    }

    /**
     * Get fallback exchange rates (updated manually)
     */
    protected function getFallbackRate(string $from, string $to): float
    {
        $rates = [
            'EUR' => ['USD' => 1.08, 'GBP' => 0.86, 'MAD' => 10.80],
            'USD' => ['EUR' => 0.93, 'GBP' => 0.80, 'MAD' => 10.00],
            'GBP' => ['EUR' => 1.16, 'USD' => 1.25, 'MAD' => 12.50],
            'MAD' => ['EUR' => 0.093, 'USD' => 0.10, 'GBP' => 0.08],
        ];

        return $rates[$from][$to] ?? 1.0;
    }

    /**
     * Convert price from one currency to another
     */
    public function convertPrice(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice(float $amount, string $currency): string
    {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'MAD' => 'DH',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        $formatted = number_format($amount, 2, ',', ' ');

        return match ($currency) {
            'USD', 'GBP' => "{$symbol}{$formatted}",
            default => "{$formatted} {$symbol}",
        };
    }

    /**
     * Get available currencies
     */
    public function getAvailableCurrencies(): array
    {
        return [
            'EUR' => ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro'],
            'USD' => ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'],
            'GBP' => ['code' => 'GBP', 'symbol' => '£', 'name' => 'British Pound'],
            'MAD' => ['code' => 'MAD', 'symbol' => 'DH', 'name' => 'Dirham Marocain'],
        ];
    }

    /**
     * Get available locales
     */
    public function getAvailableLocales(): array
    {
        return [
            'fr' => ['code' => 'fr', 'name' => 'Français', 'native' => 'Français', 'rtl' => false],
            'en' => ['code' => 'en', 'name' => 'English', 'native' => 'English', 'rtl' => false],
            'es' => ['code' => 'es', 'name' => 'Spanish', 'native' => 'Español', 'rtl' => false],
            'ar' => ['code' => 'ar', 'name' => 'Arabic', 'native' => 'العربية', 'rtl' => true],
        ];
    }
}
