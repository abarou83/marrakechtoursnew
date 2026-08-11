<?php

use App\Models\SiteSetting;

if (!function_exists('translate_model')) {
    /**
     * Helper pour obtenir un attribut traduit d'un modèle
     */
    function translate_model($model, $attribute, $fallback = '')
    {
        if (!$model) {
            return $fallback;
        }

        $translation = $model->translate();
        
        if ($translation && isset($translation->$attribute)) {
            return $translation->$attribute;
        }
        
        return $model->$attribute ?? $fallback;
    }
}

if (!function_exists('public_storage_url')) {
    /**
     * URL publique d'un fichier sur le disque storage/app/public (via public/storage).
     */
    function public_storage_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        return asset('storage/'.$normalized);
    }
}

if (!function_exists('site_setting')) {
    /**
     * Get a site setting value
     */
    function site_setting($key, $default = null)
    {
        return SiteSetting::get($key, $default);
    }
}

if (!function_exists('site_colors')) {
    /**
     * Get all site colors
     */
    function site_colors()
    {
        return SiteSetting::colors();
    }
}

if (!function_exists('primary_color')) {
    /**
     * Get primary color
     */
    function primary_color()
    {
        return site_setting('primary_color', '#211951');
    }
}

if (!function_exists('secondary_color')) {
    /**
     * Get secondary color
     */
    function secondary_color()
    {
        return site_setting('secondary_color', '#836FFF');
    }
}

