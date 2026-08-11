<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'tour_ids',
        'category_ids',
        'valid_from',
        'valid_until',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'used_count' => 'integer',
        'tour_ids' => 'array',
        'category_ids' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereRaw('used_count < max_uses');
            });
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function isValidForTour(Tour $tour): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        if (! empty($this->tour_ids) && ! in_array($tour->id, $this->tour_ids)) {
            return false;
        }

        if (! empty($this->category_ids) && ! in_array($tour->category_id, $this->category_ids)) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        $userUsageCount = $this->bookings()
            ->where('user_id', $user->id)
            ->count();

        return $userUsageCount < $this->max_uses_per_user;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return 0;
        }

        $discount = match ($this->type) {
            self::TYPE_PERCENT => $amount * ($this->value / 100),
            self::TYPE_FIXED => $this->value,
            default => 0,
        };

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function getFormattedValueAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENT => $this->value . '%',
            self::TYPE_FIXED => number_format($this->value, 2) . ' €',
            default => (string) $this->value,
        };
    }
}
