<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = [
        'tour_id',
        'client_id',
        'email',
        'customer_name',
        'travel_date',
        'adults',
        'children',
        'total_amount',
        'currency',
        'cart_data',
        'marketing_opt_in',
        'recovery_email_sent_at',
        'converted_at',
        'booking_id',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'total_amount' => 'decimal:2',
        'cart_data' => 'array',
        'marketing_opt_in' => 'boolean',
        'recovery_email_sent_at' => 'datetime',
        'converted_at' => 'datetime',
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

    public function scopePendingRecovery($query)
    {
        return $query->whereNull('recovery_email_sent_at')
            ->whereNull('converted_at');
    }
}
