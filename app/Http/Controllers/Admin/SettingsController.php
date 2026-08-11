<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\GooglePlaceReviewsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $colors = SiteSetting::where('group', 'colors')->get()->keyBy('key');
        $logo = site_setting('logo_path');
        $favicon = site_setting('favicon_path');
        $logoSmall = site_setting('logo_small_path');
        $footerLogo = site_setting('footer_logo_path');
        
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();
        
        return view('admin.settings.index', compact('colors', 'logo', 'favicon', 'logoSmall', 'footerLogo', 'availableLocales', 'locales'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'background_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'success_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'border_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp|max:2048',
            'logo_small' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp|max:1024',
            'favicon' => 'nullable|file|mimes:ico,png,svg|max:512',
            'footer_logo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp|max:2048',
            'seo_home_og_image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        foreach ($validated as $key => $value) {
            if (in_array($key, ['logo', 'logo_small', 'favicon', 'footer_logo'])) continue;
            SiteSetting::set($key, $value, 'colors');
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            SiteSetting::set('logo_path', $path, 'general');
        }

        // Handle logo small upload
        if ($request->hasFile('logo_small')) {
            $path = $request->file('logo_small')->store('logos', 'public');
            SiteSetting::set('logo_small_path', $path, 'general');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicons', 'public');
            SiteSetting::set('favicon_path', $path, 'general');
        }

        // Handle footer logo upload
        if ($request->hasFile('footer_logo')) {
            $path = $request->file('footer_logo')->store('logos', 'public');
            SiteSetting::set('footer_logo_path', $path, 'general');
        }

        // Handle URL rewrite setting
        SiteSetting::set('url_rewrite', $request->has('url_rewrite') ? '1' : '0', 'seo');

        // Handle maintenance mode settings
        SiteSetting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0', 'general');

        if ($request->filled('maintenance_message')) {
            SiteSetting::set('maintenance_message', $request->maintenance_message, 'general');
        }

        if ($request->has('maintenance_bypass_token')) {
            SiteSetting::set(
                'maintenance_bypass_token',
                trim((string) $request->input('maintenance_bypass_token', '')),
                'general'
            );
        }

        // Handle WhatsApp number
        if ($request->filled('whatsapp_number')) {
            SiteSetting::set('whatsapp_number', $request->whatsapp_number, 'general');
        }

        // Handle company information
        $companyFields = ['company_name', 'company_email', 'company_phone', 'company_address'];
        foreach ($companyFields as $field) {
            if ($request->filled($field)) {
                SiteSetting::set($field, $request->$field, 'company');
            }
        }

        // Handle social media links
        $socialFields = ['social_facebook', 'social_instagram', 'social_twitter', 'social_linkedin', 'social_youtube', 'social_tiktok'];
        foreach ($socialFields as $field) {
            SiteSetting::set($field, $request->$field ?? '', 'social');
        }

        // Handle SEO settings (multilingual)
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $seoFields = ['seo_home_title', 'seo_home_description', 'seo_home_keywords'];
        
        foreach ($seoFields as $field) {
            // Handle multilingual fields
            foreach ($availableLocales as $locale) {
                $localeField = $field . '_' . $locale;
                if ($request->filled($localeField)) {
                    SiteSetting::set($localeField, $request->$localeField, 'seo');
                }
            }
            // Keep backward compatibility with non-localized fields
            if ($request->filled($field)) {
                SiteSetting::set($field, $request->$field, 'seo');
            }
        }

        // Handle OG Image upload
        if ($request->hasFile('seo_home_og_image')) {
            $path = $request->file('seo_home_og_image')->store('seo', 'public');
            SiteSetting::set('seo_home_og_image', $path, 'seo');
        }

        // Handle Reviews Home settings (multilingual)
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        foreach ($availableLocales as $locale) {
            $titleField = 'reviews_home_title_' . $locale;
            if ($request->filled($titleField)) {
                SiteSetting::set($titleField, $request->$titleField, 'reviews');
            }
        }
        // Keep backward compatibility
        if ($request->filled('reviews_home_title')) {
            SiteSetting::set('reviews_home_title', $request->reviews_home_title, 'reviews');
        }

        $oldPlaceId = GooglePlaceReviewsService::normalizePlaceId((string) site_setting('reviews_home_place_id', ''));
        SiteSetting::set('reviews_home_source', 'google_places', 'reviews');

        $newPlaceId = $oldPlaceId;
        if ($request->has('reviews_home_place_id')) {
            $newPlaceId = GooglePlaceReviewsService::normalizePlaceId((string) $request->input('reviews_home_place_id', ''));
            SiteSetting::set('reviews_home_place_id', $newPlaceId, 'reviews');
        }

        GooglePlaceReviewsService::forgetCacheForPlaceId($oldPlaceId);
        GooglePlaceReviewsService::forgetCacheForPlaceId($newPlaceId);

        // Handle Google rating
        if ($request->filled('reviews_home_google_rating')) {
            SiteSetting::set('reviews_home_google_rating', $request->reviews_home_google_rating, 'reviews');
        }
        if ($request->filled('reviews_home_google_text')) {
            SiteSetting::set('reviews_home_google_text', $request->reviews_home_google_text, 'reviews');
        }

        // Handle active toggle
        SiteSetting::set('reviews_home_active', $request->has('reviews_home_active') ? '1' : '0', 'reviews');

        // Clear cache
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès !');
    }
}



