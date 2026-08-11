<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    protected $fillable = [
        'ip_hash',
        'user_agent_hash',
        'client_id',
        'choices',
        'consent_version',
        'source',
    ];

    protected $casts = [
        'choices' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function recordConsent(array $choices, ?int $clientId = null): self
    {
        return self::create([
            'ip_hash' => hash('sha256', request()->ip() ?? ''),
            'user_agent_hash' => hash('sha256', request()->userAgent() ?? ''),
            'client_id' => $clientId,
            'choices' => $choices,
            'consent_version' => config('gdpr.consent_version', config('app.consent_version', '1.0')),
            'source' => 'cookie_banner',
        ]);
    }

    public function hasConsent(string $key): bool
    {
        return $this->choices[$key] ?? false;
    }

    public function hasAnalytics(): bool
    {
        return $this->hasConsent('analytics');
    }

    public function hasMarketing(): bool
    {
        return $this->hasConsent('marketing');
    }

    public function hasNecessary(): bool
    {
        return true;
    }
}
