<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_name',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $properties = null
    ): self {
        $admin = auth('admin')->user();

        return self::create([
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'Système',
            'action' => $action,
            'subject_type' => $model ? get_class($model) : null,
            'subject_id' => $model?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function scopeForAdmin(Builder $query, int $adminId): Builder
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeForSubject(Builder $query, string $subjectType, int $subjectId): Builder
    {
        return $query->where('subject_type', $subjectType)->where('subject_id', $subjectId);
    }

    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getChangesAttribute(): array
    {
        if (!isset($this->properties['changes'])) {
            return [];
        }

        return $this->properties['changes'];
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'login' => 'purple',
            'logout' => 'gray',
            'export' => 'yellow',
            'refund' => 'orange',
            'status_change' => 'indigo',
            default => 'gray',
        };
    }
}
