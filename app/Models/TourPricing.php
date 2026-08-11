<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPricing extends Model
{
    protected $fillable = [
        'tour_id',
        'title',
        'pricing_mode',
        'season',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tour
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get group prices for this pricing
     */
    public function groupPrices()
    {
        return $this->hasMany(TourGroupPrice::class);
    }

    /**
     * Get private prices for this pricing
     */
    public function privatePrices()
    {
        return $this->hasMany(TourPrivatePrice::class);
    }

    /**
     * Get addons for this pricing
     */
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'pricing_addons')
            ->withPivot('is_required', 'is_included', 'override_price')
            ->withTimestamps();
    }

    /**
     * Get pricing addon pivot relationships
     */
    public function pricingAddons()
    {
        return $this->hasMany(PricingAddon::class);
    }

    /**
     * Get accommodations for this pricing
     */
    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'pricing_accommodations')
            ->withPivot('is_optional', 'nights', 'display_order')
            ->withTimestamps()
            ->orderBy('pricing_accommodations.display_order');
    }

    /**
     * Get pricing accommodation pivot relationships
     */
    public function pricingAccommodations()
    {
        return $this->hasMany(PricingAccommodation::class);
    }

    /**
     * Get translations for this pricing
     */
    public function translations()
    {
        return $this->hasMany(TourPricingTranslation::class);
    }

    /**
     * Get the translation for a specific locale
     */
    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->where('locale', $locale)->first();
        }

        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * Get the translated title
     */
    public function getTranslatedTitleAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->title : $this->title;
    }

    /**
     * Get adult price for group pricing
     */
    public function getAdultPrice()
    {
        // Ensure relationship is loaded
        if (!$this->relationLoaded('groupPrices')) {
            $this->load('groupPrices');
        }
        
        $adultPrice = $this->groupPrices->where('category', 'adult')->first();
        return $adultPrice ? $adultPrice->price : 0;
    }

    /**
     * Get child price for group pricing
     */
    public function getChildPrice()
    {
        // Ensure relationship is loaded
        if (!$this->relationLoaded('groupPrices')) {
            $this->load('groupPrices');
        }
        
        $childPrice = $this->groupPrices->where('category', 'child')->first();
        return $childPrice ? $childPrice->price : 0;
    }

    /**
     * Get infant price for group pricing
     */
    public function getInfantPrice()
    {
        // Ensure relationship is loaded
        if (!$this->relationLoaded('groupPrices')) {
            $this->load('groupPrices');
        }
        
        $infantPrice = $this->groupPrices->where('category', 'infant')->first();
        return $infantPrice ? $infantPrice->price : 0;
    }

    /**
     * Get private price for a specific number of people
     */
    public function getPrivatePriceForPeople($people)
    {
        // Ensure relationship is loaded
        if (!$this->relationLoaded('privatePrices')) {
            $this->load('privatePrices');
        }
        
        $privatePrice = $this->privatePrices
            ->where('min_people', '<=', $people)
            ->where('max_people', '>=', $people)
            ->first();
        
        return $privatePrice ? $privatePrice->price : null;
    }

    /**
     * Scope for active pricings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific pricing mode
     */
    public function scopeMode($query, $mode)
    {
        return $query->where('pricing_mode', $mode);
    }

    /**
     * Scope for specific season
     */
    public function scopeSeason($query, $season)
    {
        return $query->where('season', $season);
    }

    /**
     * Backward compatibility: Get minimum price
     * For group pricing, returns adult price. For private, returns minimum tier price.
     */
    public function getPriceMinAttribute()
    {
        // Check if attribute is already set (cached)
        if (isset($this->attributes['price_min'])) {
            return $this->attributes['price_min'];
        }

        if ($this->pricing_mode === 'group') {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('groupPrices')) {
                $this->load('groupPrices');
            }
            return $this->getAdultPrice();
        } else {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            $minPrice = $this->privatePrices->min('price');
            return $minPrice ?? 0;
        }
    }

    /**
     * Backward compatibility: Get maximum price
     * For group pricing, returns adult price. For private, returns maximum tier price.
     */
    public function getPriceMaxAttribute()
    {
        // Check if attribute is already set (cached)
        if (isset($this->attributes['price_max'])) {
            return $this->attributes['price_max'];
        }

        if ($this->pricing_mode === 'group') {
            return $this->getAdultPrice();
        } else {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            $maxPrice = $this->privatePrices->max('price');
            return $maxPrice ?? 0;
        }
    }

    /**
     * Backward compatibility: Get currency (defaults to EUR)
     */
    public function getCurrencyAttribute()
    {
        return 'EUR'; // Can be made dynamic later
    }

    /**
     * Backward compatibility: Get name
     */
    public function getNameAttribute()
    {
        $mode = ucfirst($this->pricing_mode);
        $season = ucfirst($this->season);
        return "{$mode} - {$season} Season";
    }

    /**
     * Backward compatibility: Get description
     */
    public function getDescriptionAttribute()
    {
        if ($this->pricing_mode === 'group') {
            $adultPrice = $this->getAdultPrice();
            $childPrice = $this->getChildPrice();
            return "Group pricing: Adult €{$adultPrice}, Child €{$childPrice}";
        } else {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            $tiers = $this->privatePrices->count();
            return "Private pricing with {$tiers} tier(s)";
        }
    }

    /**
     * Backward compatibility: Get min participants
     */
    public function getMinParticipantsAttribute()
    {
        if ($this->pricing_mode === 'private') {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            return $this->privatePrices->min('min_people') ?? 1;
        }
        return 1; // Group pricing minimum
    }

    /**
     * Backward compatibility: Get max participants
     */
    public function getMaxParticipantsAttribute()
    {
        if ($this->pricing_mode === 'private') {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            return $this->privatePrices->max('max_people');
        }
        return null; // Group pricing has no max
    }

    /**
     * Backward compatibility: Check if requires consultation
     */
    public function requiresConsultation()
    {
        if ($this->pricing_mode === 'group') {
            return $this->getAdultPrice() == 0;
        } else {
            // Ensure relationship is loaded
            if (!$this->relationLoaded('privatePrices')) {
                $this->load('privatePrices');
            }
            return $this->privatePrices->count() == 0;
        }
    }
}
