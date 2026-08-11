<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPrivatePrice extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'min_people',
        'max_people',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_people' => 'integer',
        'max_people' => 'integer',
    ];

    /**
     * Get the tour pricing that owns this private price
     */
    public function tourPricing()
    {
        return $this->belongsTo(TourPricing::class);
    }

    /**
     * Check if this tier matches the number of people
     */
    public function matchesPeople($people)
    {
        return $people >= $this->min_people && $people <= $this->max_people;
    }
}




