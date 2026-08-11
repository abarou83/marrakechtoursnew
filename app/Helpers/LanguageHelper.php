<?php

namespace App\Helpers;

use App\Models\Language;

class LanguageHelper
{
    /**
     * Get all available locales with their names and flags (from database)
     */
    public static function getAvailableLocales()
    {
        try {
            $languages = Language::active()->get();
            $locales = [];
            
            foreach ($languages as $language) {
                $locales[$language->code] = [
                    'name' => $language->name,
                    'flag' => $language->flag,
                    'native' => $language->native_name,
                ];
            }
            
            return $locales;
        } catch (\Exception $e) {
            // Fallback si la table n'existe pas encore
            return [
                'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'native' => 'Français'],
                'en' => ['name' => 'English', 'flag' => '🇬🇧', 'native' => 'English'],
            ];
        }
    }

    /**
     * Get current locale info
     */
    public static function getCurrentLocaleInfo()
    {
        $locale = app()->getLocale();
        $locales = self::getAvailableLocales();
        
        // Si la locale actuelle n'est pas active, utiliser la langue par défaut
        if (!isset($locales[$locale])) {
            $defaultLang = Language::getDefault();
            if ($defaultLang) {
                $locale = $defaultLang->code;
            }
        }
        
        return $locales[$locale] ?? ['name' => 'Français', 'flag' => '🇫🇷', 'native' => 'Français'];
    }

    /**
     * Get locale name
     */
    public static function getLocaleName($locale)
    {
        $locales = self::getAvailableLocales();
        return $locales[$locale]['name'] ?? $locale;
    }

    /**
     * Get default locale
     */
    public static function getDefaultLocale()
    {
        try {
            $defaultLang = Language::getDefault();
            return $defaultLang ? $defaultLang->code : 'fr';
        } catch (\Exception $e) {
            return 'fr';
        }
    }

    /**
     * Convert language code to country code for flag-icons
     */
    public static function getCountryCode($langCode)
    {
        $mapping = [
            'fr' => 'fr',
            'en' => 'gb', // UK flag for English
            'es' => 'es',
            'de' => 'de',
            'it' => 'it',
            'pt' => 'pt',
            'nl' => 'nl',
            'ar' => 'sa', // Saudi Arabia as default for Arabic
            'be' => 'be',
            'pl' => 'pl',
            'ru' => 'ru',
            'zh' => 'cn',
            'ja' => 'jp',
            'ko' => 'kr',
            'tr' => 'tr',
            'hi' => 'in',
            'th' => 'th',
            'vi' => 'vn',
        ];
        
        return $mapping[strtolower($langCode)] ?? strtolower($langCode);
    }
}

