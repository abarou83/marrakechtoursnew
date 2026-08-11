<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    public const LOCATION_HEADER = 'header';
    public const LOCATION_FOOTER = 'footer';
    public const LOCATION_FOOTER_BOTTOM = 'footer_bottom';

    protected $fillable = [
        'name',
        'slug',
        'location',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }

    public function allItems()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    public function activeItems()
    {
        return $this->hasMany(MenuItem::class)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    public function translations()
    {
        return $this->hasMany(MenuTranslation::class);
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

    public static function locationLabels(): array
    {
        return [
            self::LOCATION_HEADER => 'Navigation (header)',
            self::LOCATION_FOOTER => 'Footer (colonne)',
            self::LOCATION_FOOTER_BOTTOM => 'Footer (barre du bas)',
        ];
    }

    public function getDisplayName($locale = null): string
    {
        $translation = $this->translate($locale);

        if ($translation && $translation->name) {
            return $translation->name;
        }

        return $this->name;
    }

    public function scopeForLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    public static function activeForLocation(string $location)
    {
        return static::where('is_active', true)
            ->forLocation($location)
            ->orderBy('position')
            ->with([
                'activeItems.category',
                'activeItems.page',
                'activeItems.tour',
                'activeItems.translations',
                'translations',
            ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            if (!$menu->slug) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        static::updating(function ($menu) {
            if ($menu->isDirty('name') && !$menu->isDirty('slug')) {
                $menu->slug = Str::slug($menu->name);
            }
        });
    }
}
