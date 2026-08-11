<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'date',
        'spots_total',
        'spots_available',
        'price_override',
        'is_available',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'spots_total' => 'integer',
        'spots_available' => 'integer',
        'price_override' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->where('spots_available', '>', 0);
    }

    public function scopeFuture(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    public function hasAvailableSpots(int $required = 1): bool
    {
        return $this->is_available && $this->spots_available >= $required;
    }

    public function reserveSpots(int $count): bool
    {
        if (! $this->hasAvailableSpots($count)) {
            return false;
        }

        $this->decrement('spots_available', $count);

        return true;
    }

    public function releaseSpots(int $count): void
    {
        $newAvailable = min($this->spots_available + $count, $this->spots_total);
        $this->update(['spots_available' => $newAvailable]);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->price_override ?? $this->tour->price_adult;
    }

    public function getAvailabilityPercentageAttribute(): int
    {
        if ($this->spots_total === 0) {
            return 0;
        }

        return (int) round(($this->spots_available / $this->spots_total) * 100);
    }

    public function getStatusAttribute(): string
    {
        if (! $this->is_available) {
            return 'closed';
        }

        if ($this->spots_available === 0) {
            return 'full';
        }

        if ($this->availability_percentage <= 20) {
            return 'almost_full';
        }

        return 'available';
    }
}
