<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccommodationRoom extends Model
{
    protected $fillable = [
        'accommodation_id',
        'room_type',
        'price_per_night',
        'max_occupancy',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'max_occupancy' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get display name for room type
     */
    public function getRoomTypeNameAttribute()
    {
        $names = [
            'single' => 'Chambre Simple',
            'double' => 'Chambre Double',
            'twin' => 'Chambre Twin',
            'triple' => 'Chambre Triple',
        ];

        return $names[$this->room_type] ?? ucfirst($this->room_type);
    }

    /**
     * Scope for active rooms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}