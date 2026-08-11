<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourAddon extends Model
{
    protected $fillable = [
        'tour_id',
        'addon_id',
        'is_required',
        'override_price',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'override_price' => 'decimal:2',
    ];

    /**
     * Get the tour
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the addon
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}




