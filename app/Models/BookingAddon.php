<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
    protected $fillable = [
        'booking_id',
        'addon_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the addon
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}




