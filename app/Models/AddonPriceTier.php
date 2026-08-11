<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonPriceTier extends Model
{
    protected $fillable = [
        'addon_id',
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
     * Get the addon that owns this price tier
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /**
     * Check if this tier matches the number of people
     */
    public function matchesPeople($people)
    {
        return $people >= $this->min_people && $people <= $this->max_people;
    }
}
