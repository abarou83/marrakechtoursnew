<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'focus_keyword',
    ];

    /**
     * Many-to-many relationship with tours
     */
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'category_tour')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
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

    public function getTranslatedAttribute($attribute, $locale = null)
    {
        $translation = $this->translate($locale);
        return $translation ? $translation->$attribute : $this->$attribute;
    }

    /**
     * Get the URL key (slug or id based on url_rewrite setting)
     */
    public function getUrlKeyAttribute()
    {
        $urlRewrite = site_setting('url_rewrite', '1');
        if ($urlRewrite && $urlRewrite !== '0') {
            return $this->slug ?: $this->id;
        }
        return $this->id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
