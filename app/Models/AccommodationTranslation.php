<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccommodationTranslation extends Model
{
    protected $fillable = [
        'accommodation_id',
        'locale',
        'name',
        'description',
        'location',
    ];

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
