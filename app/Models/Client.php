<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'google_id',
        'preferred_language',
        'preferred_currency',
        'referral_code',
        'notification_preferences',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'client_id');
    }

    public function getDefaultNotificationPreferencesAttribute(): array
    {
        return [
            'email_booking_confirmation' => true,
            'email_booking_reminder' => true,
            'email_promotions' => false,
            'email_newsletter' => false,
        ];
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences ?? $this->default_notification_preferences;
        return $prefs[$type] ?? false;
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return $initials;
    }
}
