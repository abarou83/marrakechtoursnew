<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingAddon extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'addon_id',
        'is_required',
        'override_price',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'override_price' => 'decimal:2',
    ];

    /**
     * Get the tour pricing that owns this addon
     */
    public function tourPricing()
    {
        return $this->belongsTo(TourPricing::class);
    }

    /**
     * Get the addon
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}
