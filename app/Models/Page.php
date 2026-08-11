<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class, 'page_id');
    }

    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        $translation = $this->translations()->where('locale', $locale)->first();
        
        if ($translation) {
            return $translation;
        }
        
        // Fallback to the default locale
        return $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    /**
     * Get the URL key (translation slug or id based on url_rewrite setting)
     */
    public function getUrlKeyAttribute()
    {
        $urlRewrite = site_setting('url_rewrite', '1');
        if ($urlRewrite && $urlRewrite !== '0') {
            $translation = $this->translate();
            return ($translation && $translation->slug) ? $translation->slug : $this->id;
        }
        return $this->id;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

