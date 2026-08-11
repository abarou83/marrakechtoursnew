<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'tour_id',
        'client_id',
        'booking_id',
        'rating',
        'title',
        'comment',
        'guide_rating',
        'value_rating',
        'recommend',
        'author_name',
        'author_country',
        'travel_date',
        'travel_type',
        'is_verified',
        'status',
        'locale',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'guide_rating' => 'integer',
        'value_rating' => 'integer',
        'recommend' => 'boolean',
        'is_verified' => 'boolean',
        'travel_date' => 'date',
        'responded_at' => 'datetime',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->rating,
            $this->guide_rating,
            $this->value_rating,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : $this->rating;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
