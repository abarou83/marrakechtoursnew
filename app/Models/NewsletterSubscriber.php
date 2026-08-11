<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'locale',
        'source',
        'client_id',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber) {
            if (!$subscriber->unsubscribe_token) {
                $subscriber->unsubscribe_token = Str::random(64);
            }
            if (!$subscriber->subscribed_at) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isActive(): bool
    {
        return $this->unsubscribed_at === null;
    }

    public function unsubscribe(): void
    {
        $this->update(['unsubscribed_at' => now()]);
    }
}
