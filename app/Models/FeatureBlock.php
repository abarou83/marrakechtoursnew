<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureBlock extends Model
{
    protected $fillable = [
        'icon',
        'image_path',
        'icon_background_color',
        'icon_background_color_enabled',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'icon_background_color_enabled' => 'boolean',
    ];

    /**
     * Scope to get active blocks ordered
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Scope to order by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Translations relationship
     */
    public function translations()
    {
        return $this->hasMany(FeatureBlockTranslation::class);
    }

    /**
     * Get translation for current locale
     */
    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        $translation = $this->translations()->where('locale', $locale)->first();
        
        if ($translation) {
            return $translation;
        }
        
        // Fallback to the default locale
        return $this->translations()->where('locale', config('app.fallback_locale', 'fr'))->first();
    }
}
