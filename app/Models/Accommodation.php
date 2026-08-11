<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Accommodation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'address',
        'stars',
        'is_active',
    ];

    protected $casts = [
        'stars' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get translations for this accommodation
     */
    public function translations()
    {
        return $this->hasMany(AccommodationTranslation::class);
    }

    /**
     * Get translated version of this accommodation
     */
    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $translation = $this->translations()->where('locale', $locale)->first();

        if ($translation) {
            return $translation;
        }

        return $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    /**
     * Get rooms for this accommodation
     */
    public function rooms()
    {
        return $this->hasMany(AccommodationRoom::class);
    }

    /**
     * Get active rooms only
     */
    public function activeRooms()
    {
        return $this->hasMany(AccommodationRoom::class)->where('is_active', true);
    }

    /**
     * Get single room
     */
    public function singleRoom()
    {
        return $this->hasOne(AccommodationRoom::class)->where('room_type', 'single')->where('is_active', true);
    }

    /**
     * Get double room
     */
    public function doubleRoom()
    {
        return $this->hasOne(AccommodationRoom::class)->where('room_type', 'double')->where('is_active', true);
    }

    /**
     * Get tour pricings that include this accommodation
     */
    public function tourPricings()
    {
        return $this->belongsToMany(TourPricing::class, 'pricing_accommodations')
            ->withPivot('is_optional', 'display_order')
            ->withTimestamps();
    }

    /**
     * Scope for active accommodations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($accommodation) {
            if (!$accommodation->slug) {
                $accommodation->slug = Str::slug($accommodation->name);
            }
        });

        static::updating(function ($accommodation) {
            if ($accommodation->isDirty('name') && !$accommodation->isDirty('slug')) {
                $accommodation->slug = Str::slug($accommodation->name);
            }
        });
    }
}