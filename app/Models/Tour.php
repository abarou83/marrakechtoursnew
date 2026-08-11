<?php

namespace App\Models;

use App\Casts\FlexibleEnumCast;
use App\Enums\Difficulty;
use App\Enums\TourType;
use App\Traits\OptimizedQueries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tour extends Model
{
    use SoftDeletes, OptimizedQueries;

    protected array $essentialRelations = [
        'translations',
        'media',
        'pricings.groupPrices',
    ];

    protected array $essentialColumns = [
        'id', 'slug', 'is_active', 'is_featured', 'is_bestseller',
        'duration', 'avg_rating', 'reviews_count', 'views_count',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'type',
        'difficulty',
        'min_age',
        'description',
        'location',
        'departure_point',
        'departure_lat',
        'departure_lng',
        'included',
        'excluded',
        'highlights',
        'cancellation_policy',
        'booking_deadline_hours',
        'duration',
        'price',
        'capacity',
        'avg_rating',
        'reviews_count',
        'views_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'focus_keyword',
        'status',
        'is_active',
        'is_featured',
        'is_bestseller',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'departure_lat' => 'decimal:7',
        'departure_lng' => 'decimal:7',
        'avg_rating' => 'decimal:1',
        'included' => 'array',
        'excluded' => 'array',
        'highlights' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'difficulty' => FlexibleEnumCast::class . ':' . Difficulty::class,
        'type' => FlexibleEnumCast::class . ':' . TourType::class,
    ];

    /**
     * Many-to-many relationship with categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_tour')->withTimestamps();
    }

    /**
     * Legacy relationship - keep for backward compatibility
     * Returns the first category or null
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tourDates()
    {
        return $this->hasMany(TourDate::class);
    }

    public function availabilities()
    {
        return $this->hasMany(TourAvailability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getMinPrice(): ?float
    {
        $pricing = $this->pricings()
            ->where('is_active', true)
            ->where('pricing_mode', 'group')
            ->where('season', 'normal')
            ->with('groupPrices')
            ->first();

        if (! $pricing) {
            $pricing = $this->pricings()
                ->where('is_active', true)
                ->where('pricing_mode', 'group')
                ->with('groupPrices')
                ->first();
        }

        if ($pricing && $pricing->groupPrices->isNotEmpty()) {
            $adult = $pricing->groupPrices->firstWhere('category', 'adult');

            return $adult ? (float) $adult->price : (float) $pricing->groupPrices->min('price');
        }

        return $this->price !== null ? (float) $this->price : null;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function translations()
    {
        return $this->hasMany(TourTranslation::class);
    }

    public function pricings()
    {
        return $this->hasMany(TourPricing::class);
    }

    /**
     * Get addons for this tour
     */
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'tour_addons')
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
     * Get active addons for this tour
     */
    public function activeAddons()
    {
        return $this->addons()->where('addons.is_active', true);
    }

    public function promotions()
    {
        return $this->hasMany(TourPromotion::class);
    }

    /**
     * Obtenir la promotion active
     */
    public function activePromotion()
    {
        return $this->promotions()->active()->first();
    }

    /**
     * Obtenir le tarif par défaut
     * Returns the first active group pricing for normal season, or first active pricing
     */
    public function defaultPricing()
    {
        // Try to get group pricing for normal season first
        $default = $this->pricings()
            ->where('pricing_mode', 'group')
            ->where('season', 'normal')
            ->where('is_active', true)
            ->with(['groupPrices', 'privatePrices'])
            ->first();
        
        // If not found, get any active pricing
        if (!$default) {
            $default = $this->pricings()
                ->active()
                ->with(['groupPrices', 'privatePrices'])
                ->first();
        }
        
        return $default;
    }

    /**
     * Obtenir le tarif selon le nombre de participants
     * For new pricing system, this finds the appropriate private pricing tier
     */
    public function getPricingForParticipants($participants)
    {
        // Try to find a private pricing that matches
        $privatePricing = $this->pricings()
            ->active()
            ->where('pricing_mode', 'private')
            ->where('season', 'normal')
            ->first();

        if ($privatePricing) {
            $tier = $privatePricing->privatePrices()
                ->where('min_people', '<=', $participants)
                ->where('max_people', '>=', $participants)
                ->first();
            
            if ($tier) {
                return $privatePricing;
            }
        }

        // Fallback to group pricing
        return $this->pricings()
            ->active()
            ->where('pricing_mode', 'group')
            ->where('season', 'normal')
            ->first();
    }

    /**
     * Obtenir tous les tarifs actifs triés par mode et saison
     */
    public function getActivePricings()
    {
        return $this->pricings()
            ->active()
            ->with('translations')
            ->orderBy('pricing_mode')
            ->orderBy('season')
            ->get();
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
     * Get the URL key for this tour (slug or id based on url_rewrite setting)
     */
    public function getUrlKeyAttribute()
    {
        $urlRewrite = site_setting('url_rewrite', '1');
        if ($urlRewrite && $urlRewrite !== '0') {
            return $this->slug ?: $this->id;
        }
        return $this->id;
    }

    /**
     * Résolution des routes /tours/{tour}/… par slug ou par id.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        $query = static::query()->where('status', 'published');

        if (is_numeric($value)) {
            return $query->where('id', (int) $value)->first();
        }

        return $query->where('slug', $value)->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            if (!$tour->slug) {
                $tour->slug = Str::slug($tour->title);
            }
        });

        static::updating(function ($tour) {
            if ($tour->isDirty('title') && !$tour->isDirty('slug')) {
                $tour->slug = Str::slug($tour->title);
            }
        });
    }
}
