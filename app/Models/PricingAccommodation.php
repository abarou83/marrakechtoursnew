<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingAccommodation extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'accommodation_id',
        'is_optional',
        'nights',
        'display_order',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'nights' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the tour pricing
     */
    public function tourPricing()
    {
        return $this->belongsTo(TourPricing::class);
    }

    /**
     * Get the accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}