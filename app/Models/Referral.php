<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'code',
        'referrer_client_id',
        'referred_client_id',
        'referrer_booking_id',
        'referred_booking_id',
        'referrer_reward',
        'referred_discount',
        'currency',
        'status',
        'used_at',
        'rewarded_at',
    ];

    protected $casts = [
        'referrer_reward' => 'decimal:2',
        'referred_discount' => 'decimal:2',
        'used_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Referral $referral) {
            if (!$referral->code) {
                $referral->code = self::generateUniqueCode($referral->referrer_client_id);
            }
        });
    }

    public static function generateUniqueCode(int $clientId): string
    {
        $prefix = 'REF';
        $suffix = strtoupper(Str::random(6));

        return "{$prefix}{$clientId}{$suffix}";
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referrer_client_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referred_client_id');
    }

    public function referrerBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'referrer_booking_id');
    }

    public function referredBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'referred_booking_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeUsed(Builder $query): Builder
    {
        return $query->where('status', 'used');
    }

    public function scopeRewarded(Builder $query): Builder
    {
        return $query->where('status', 'rewarded');
    }

    public function markAsUsed(int $referredClientId, int $bookingId): void
    {
        $this->update([
            'status' => 'used',
            'referred_client_id' => $referredClientId,
            'referred_booking_id' => $bookingId,
            'used_at' => now(),
        ]);
    }

    public function markAsRewarded(int $referrerBookingId): void
    {
        $this->update([
            'status' => 'rewarded',
            'referrer_booking_id' => $referrerBookingId,
            'rewarded_at' => now(),
        ]);
    }
}
