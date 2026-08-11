<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Addon extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'pricing_type',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get translations for this addon
     */
    public function translations()
    {
        return $this->hasMany(AddonTranslation::class);
    }

    /**
     * Get translated version of this addon
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
     * Get tours that have this addon
     */
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_addons')
            ->withPivot('is_required', 'override_price')
            ->withTimestamps();
    }

    /**
     * Get tour addon pivot relationships
     */
    public function tourAddons()
    {
        return $this->hasMany(TourAddon::class);
    }

    /**
     * Get bookings that include this addon
     */
    public function bookingAddons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    /**
     * Get price tiers for this addon (for tiered pricing based on number of people)
     */
    public function priceTiers()
    {
        return $this->hasMany(AddonPriceTier::class);
    }

    /**
     * Get price for a specific number of people using tiers if available
     * 
     * @param int $people Number of people
     * @param decimal|null $overridePrice Override price from pricing_addons
     * @return decimal|null
     */
    public function getPriceForPeople($people, $overridePrice = null)
    {
        // If override price is provided, use it (no tiers)
        if ($overridePrice !== null) {
            return $overridePrice;
        }

        // Check if addon has price tiers
        if (!$this->relationLoaded('priceTiers')) {
            $this->load('priceTiers');
        }

        if ($this->priceTiers->isNotEmpty()) {
            // Find matching tier
            $tier = $this->priceTiers->first(function ($tier) use ($people) {
                return $people >= $tier->min_people && $people <= $tier->max_people;
            });

            if ($tier) {
                return $tier->price;
            }
        }

        // Fallback to base_price if no tiers match
        return $this->base_price;
    }

    /**
     * Scope for active addons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate price for this addon
     * 
     * @param int $quantity Quantity for per_person pricing
     * @param decimal|null $overridePrice Override price from tour_addons
     * @return decimal
     */
    public function calculatePrice($quantity = 1, $overridePrice = null)
    {
        $price = $overridePrice ?? $this->base_price;

        switch ($this->pricing_type) {
            case 'per_person':
                return $price * $quantity;
            case 'per_group':
                return $price;
            case 'free':
                return 0;
            default:
                return 0;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($addon) {
            if (!$addon->slug) {
                $addon->slug = Str::slug($addon->name);
            }
        });

        static::updating(function ($addon) {
            if ($addon->isDirty('name') && !$addon->isDirty('slug')) {
                $addon->slug = Str::slug($addon->name);
            }
        });
    }
}





