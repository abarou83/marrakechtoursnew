<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourGroupPrice extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'category',
        'age_min',
        'age_max',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'age_min' => 'integer',
        'age_max' => 'integer',
    ];

    /**
     * Get the tour pricing that owns this group price
     */
    public function tourPricing()
    {
        return $this->belongsTo(TourPricing::class);
    }
}




