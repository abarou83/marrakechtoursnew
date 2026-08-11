<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Currency;
use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class DetectGeo
{
    private const COUNTRY_LOCALE_MAP = [
        'FR' => 'fr',
        'BE' => 'fr',
        'CH' => 'fr',
        'LU' => 'fr',
        'MC' => 'fr',
        'CA' => 'fr',
        'GB' => 'en',
        'US' => 'en',
        'AU' => 'en',
        'NZ' => 'en',
        'IE' => 'en',
        'ES' => 'es',
        'MX' => 'es',
        'AR' => 'es',
        'CO' => 'es',
        'MA' => 'ar',
        'DZ' => 'ar',
        'TN' => 'ar',
        'EG' => 'ar',
        'SA' => 'ar',
        'AE' => 'ar',
        'QA' => 'ar',
        'KW' => 'ar',
    ];

    private const COUNTRY_CURRENCY_MAP = [
        'FR' => 'EUR',
        'BE' => 'EUR',
        'CH' => 'EUR',
        'LU' => 'EUR',
        'ES' => 'EUR',
        'IT' => 'EUR',
        'DE' => 'EUR',
        'NL' => 'EUR',
        'PT' => 'EUR',
        'AT' => 'EUR',
        'GB' => 'GBP',
        'US' => 'USD',
        'CA' => 'USD',
        'AU' => 'USD',
        'MA' => 'MAD',
    ];

    private const COUNTRY_PHONE_PREFIX = [
        'FR' => '+33',
        'BE' => '+32',
        'CH' => '+41',
        'GB' => '+44',
        'US' => '+1',
        'CA' => '+1',
        'ES' => '+34',
        'MA' => '+212',
        'DZ' => '+213',
        'TN' => '+216',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('geo.detected') || $this->shouldRefreshGeo()) {
            $this->detectAndStoreGeo($request);
        }

        view()->share('geoData', [
            'country_code' => Session::get('geo.country_code'),
            'currency' => Session::get('geo.currency', 'EUR'),
            'locale' => Session::get('geo.locale', 'fr'),
            'phone_prefix' => Session::get('geo.phone_prefix', '+212'),
            'timezone' => Session::get('geo.timezone', 'Africa/Casablanca'),
        ]);

        return $next($request);
    }

    private function detectAndStoreGeo(Request $request): void
    {
        $ip = $request->ip();

        if ($this->isLocalIp($ip)) {
            $this->storeDefaultGeo();

            return;
        }

        try {
            $position = Location::get($ip);

            if ($position) {
                $countryCode = strtoupper($position->countryCode ?? 'FR');

                Session::put('geo.detected', true);
                Session::put('geo.detected_at', now());
                Session::put('geo.country_code', $countryCode);
                Session::put('geo.country_name', $position->countryName);
                Session::put('geo.city', $position->cityName);
                Session::put('geo.timezone', $position->timezone ?? 'Europe/Paris');
                Session::put('geo.locale', $this->getLocaleForCountry($countryCode));
                Session::put('geo.currency', $this->getCurrencyForCountry($countryCode));
                Session::put('geo.phone_prefix', $this->getPhonePrefixForCountry($countryCode));

                return;
            }
        } catch (\Exception $e) {
            report($e);
        }

        $this->storeDefaultGeo();
    }

    private function storeDefaultGeo(): void
    {
        Session::put('geo.detected', true);
        Session::put('geo.detected_at', now());
        Session::put('geo.country_code', 'FR');
        Session::put('geo.locale', 'fr');
        Session::put('geo.currency', 'EUR');
        Session::put('geo.phone_prefix', '+33');
        Session::put('geo.timezone', 'Europe/Paris');
    }

    private function getLocaleForCountry(string $countryCode): string
    {
        return self::COUNTRY_LOCALE_MAP[$countryCode] ?? 'fr';
    }

    private function getCurrencyForCountry(string $countryCode): string
    {
        return self::COUNTRY_CURRENCY_MAP[$countryCode] ?? 'EUR';
    }

    private function getPhonePrefixForCountry(string $countryCode): string
    {
        return self::COUNTRY_PHONE_PREFIX[$countryCode] ?? '+212';
    }

    private function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');
    }

    private function shouldRefreshGeo(): bool
    {
        $detectedAt = Session::get('geo.detected_at');

        if (! $detectedAt) {
            return true;
        }

        return now()->diffInHours($detectedAt) > 24;
    }
}
