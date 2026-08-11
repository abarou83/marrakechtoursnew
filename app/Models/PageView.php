<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'path',
        'country_code',
        'device_type',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'session_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PageView $pageView) {
            $pageView->created_at = now();
        });
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
