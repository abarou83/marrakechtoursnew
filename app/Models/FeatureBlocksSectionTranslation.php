<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureBlocksSectionTranslation extends Model
{
    protected $table = 'feature_blocks_section_translations';
    
    protected $fillable = [
        'locale',
        'title',
        'description',
    ];

    /**
     * Get translation for a specific locale or current locale
     */
    public static function getForLocale($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = static::where('locale', $locale)->first();
        
        if ($translation) {
            return $translation;
        }
        
        // Fallback to default locale
        return static::where('locale', config('app.fallback_locale', 'fr'))->first();
    }
}
