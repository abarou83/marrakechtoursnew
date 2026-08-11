<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTranslation extends Model
{
    protected $fillable = [
        'tour_id',
        'locale',
        'title',
        'description',
        'itinerary',
        'location',
        'duration',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'canonical_url',
        'og_image',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}

