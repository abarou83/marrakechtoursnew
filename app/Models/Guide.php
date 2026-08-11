<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Guide extends Model
{
    protected $fillable = [
        'category',
        'is_published',
        'published_at',
        'author_id',
        'featured_image',
        'reading_time',
        'views_count',
        'position',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer',
        'views_count' => 'integer',
        'position' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(GuideTranslation::class);
    }

    public function translate(?string $locale = null): ?GuideTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', config('app.fallback_locale', 'fr'))->first();
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'guide_tour')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
