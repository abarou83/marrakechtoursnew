<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'initial_amount',
        'remaining_amount',
        'currency',
        'purchaser_client_id',
        'recipient_name',
        'recipient_email',
        'message',
        'expires_at',
        'is_active',
        'payment_intent_id',
        'payment_status',
        'redeemed_at',
        'redeemed_by_client_id',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'expires_at' => 'date',
        'is_active' => 'boolean',
        'redeemed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (GiftCard $giftCard) {
            if (!$giftCard->code) {
                $giftCard->code = self::generateUniqueCode();
            }
            if (!$giftCard->remaining_amount) {
                $giftCard->remaining_amount = $giftCard->initial_amount;
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'GC-' . strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'purchaser_client_id');
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'redeemed_by_client_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('remaining_amount', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeValid(Builder $query): Builder
    {
        return $this->scopeActive($query);
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->remaining_amount > 0
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function redeem(float $amount, ?int $clientId = null): float
    {
        $actualAmount = min($amount, $this->remaining_amount);

        $this->remaining_amount -= $actualAmount;

        if ($this->remaining_amount <= 0) {
            $this->redeemed_at = now();
            $this->redeemed_by_client_id = $clientId;
        }

        $this->save();

        return $actualAmount;
    }
}
