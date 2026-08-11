<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAccommodation extends Model
{
    protected $fillable = [
        'booking_id',
        'accommodation_id',
        'accommodation_room_id',
        'room_type',
        'nights',
        'price_per_night',
        'total_price',
    ];

    protected $casts = [
        'nights' => 'integer',
        'price_per_night' => 'decimal:2',
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
     * Get the accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the accommodation room
     */
    public function accommodationRoom()
    {
        return $this->belongsTo(AccommodationRoom::class);
    }
}