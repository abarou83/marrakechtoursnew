<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supportedLocales = ['fr', 'en', 'es', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Session::put('locale', $locale);

        $isRtl = $locale === 'ar';
        view()->share('currentLocale', $locale);
        view()->share('isRtl', $isRtl);
        view()->share('textDirection', $isRtl ? 'rtl' : 'ltr');

        return $next($request);
    }

    protected function resolveLocale(Request $request): string
    {
        if ($cookieLocale = $request->cookie('preferred_locale')) {
            if ($this->isValidLocale($cookieLocale)) {
                return $cookieLocale;
            }
        }

        $urlLocale = $request->route('locale') ?? $request->segment(1);
        if ($urlLocale && $this->isValidLocale($urlLocale)) {
            return $urlLocale;
        }

        if ($request->has('lang')) {
            $langParam = $request->get('lang');
            if ($this->isValidLocale($langParam)) {
                return $langParam;
            }
        }

        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if ($this->isValidLocale($sessionLocale)) {
                return $sessionLocale;
            }
        }

        if ($geoLocale = Session::get('geo.locale')) {
            if ($this->isValidLocale($geoLocale)) {
                return $geoLocale;
            }
        }

        $acceptLanguage = $request->getPreferredLanguage($this->supportedLocales);
        if ($acceptLanguage && $this->isValidLocale($acceptLanguage)) {
            return $acceptLanguage;
        }

        return $this->getDefaultLocale();
    }

    protected function isValidLocale(string $locale): bool
    {
        if (!in_array($locale, $this->supportedLocales)) {
            return false;
        }

        try {
            return Language::isActive($locale);
        } catch (\Exception $e) {
            return in_array($locale, $this->supportedLocales);
        }
    }

    protected function getDefaultLocale(): string
    {
        try {
            $defaultLang = Language::getDefault();
            if ($defaultLang) {
                return $defaultLang->code;
            }
        } catch (\Exception $e) {
        }

        return config('app.locale', 'fr');
    }
}

