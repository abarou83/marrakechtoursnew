<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPricingTranslation extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'locale',
        'title',
    ];

    public function tourPricing()
    {
        return $this->belongsTo(TourPricing::class);
    }
}
