<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourDate extends Model
{
    protected $fillable = [
        'tour_id',
        'start_at',
        'end_at',
        'capacity',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
