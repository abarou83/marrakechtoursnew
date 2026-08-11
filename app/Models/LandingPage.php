<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class LandingPage extends Model
{
    protected $fillable = [
        'type',
        'destination_id',
        'category_id',
        'tour_filters',
        'is_published',
        'is_indexed',
        'tours_count',
        'views_count',
    ];

    protected $casts = [
        'tour_filters' => 'array',
        'is_published' => 'boolean',
        'is_indexed' => 'boolean',
        'tours_count' => 'integer',
        'views_count' => 'integer',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(LandingPageTranslation::class);
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeIndexable(Builder $query): Builder
    {
        return $query->where('is_indexed', true)->where('tours_count', '>=', 3);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getToursQuery(): Builder
    {
        $query = Tour::query()->where('is_active', true);

        if ($this->destination_id) {
            $query->whereHas('destinations', fn($q) => $q->where('destinations.id', $this->destination_id));
        }

        if ($this->category_id) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $this->category_id));
        }

        if ($this->tour_filters) {
            foreach ($this->tour_filters as $key => $value) {
                if ($key === 'difficulty' && $value) {
                    $query->where('difficulty', $value);
                }
                if ($key === 'duration_max' && $value) {
                    $query->where('duration', '<=', $value);
                }
                if ($key === 'price_max' && $value) {
                    $query->where('price', '<=', $value);
                }
            }
        }

        return $query;
    }

    public function updateToursCount(): void
    {
        $this->update(['tours_count' => $this->getToursQuery()->count()]);
    }

    public function shouldBeIndexed(): bool
    {
        return $this->is_published && $this->tours_count >= 3;
    }
}
