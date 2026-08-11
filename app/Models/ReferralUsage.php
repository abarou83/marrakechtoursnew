<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralUsage extends Model
{
    protected $fillable = [
        'referrer_client_id',
        'referred_client_id',
        'booking_id',
        'code',
        'discount_amount',
        'reward_amount',
        'status',
        'rewarded_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'reward_amount' => 'decimal:2',
        'rewarded_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referrer_client_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referred_client_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
