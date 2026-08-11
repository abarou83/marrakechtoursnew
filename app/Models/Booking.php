<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'client_id',
        'tour_id',
        'pricing_id',
        'tour_date_id',
        'preferred_date',
        'travel_date',
        'seats',
        'adults',
        'children',
        'infants',
        'pricing_mode',
        'base_price',
        'addons_total',
        'accommodation_total',
        'discount_amount',
        'total_ht',
        'tax_amount',
        'total_ttc',
        'total_amount',
        'total_price',
        'currency',
        'exchange_rate',
        'promo_code_id',
        'status',
        'payment_status',
        'payment_intent_id',
        'payment_provider',
        'country_code',
        'guest_name',
        'guest_email',
        'guest_phone',
        'customer_name',
        'customer_email',
        'customer_phone',
        'special_requests',
        'price_breakdown',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'refunded_at',
        'refund_amount',
        'voucher_path',
        'locale',
        'channel',
        'channel_external_id',
        'channel_notes',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referral_code',
        'gift_card_id',
        'gift_card_amount',
        'deposit_amount',
        'payment_type',
        'review_requested_at',
        'last_reminder_sent_at',
        'reminder_type',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'accommodation_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'refund_amount' => 'decimal:2',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'status' => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
        'price_breakdown' => 'array',
        'travel_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'review_requested_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'gift_card_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function tourDate()
    {
        return $this->belongsTo(TourDate::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function pricing()
    {
        return $this->belongsTo(TourPricing::class, 'pricing_id');
    }

    /**
     * Get booking addons
     */
    public function addons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    /**
     * Alias for backward compatibility
     */
    public function bookingAddons()
    {
        return $this->addons();
    }

    /**
     * Get accommodations for this booking
     */
    public function accommodations()
    {
        return $this->hasMany(BookingAccommodation::class);
    }

    /**
     * Get total price (use total_ttc if available, fallback to total_price or total_amount)
     */
    public function getTotalPriceAttribute($value)
    {
        return $this->total_ttc ?? $value ?? $this->total_amount ?? 0;
    }

    /**
     * Get formatted reference
     */
    public function getFormattedReferenceAttribute(): string
    {
        return $this->reference ?? "#{$this->id}";
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::Pending);
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::Confirmed);
    }

    /**
     * Scope for upcoming bookings (travel_date in the future)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('travel_date', '>=', now()->toDateString());
    }
}
