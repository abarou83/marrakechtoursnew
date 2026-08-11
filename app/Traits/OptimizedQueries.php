<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OptimizedQueries
{
    public function scopeWithEssentials(Builder $query): Builder
    {
        $essentials = $this->getEssentialRelations();

        if (!empty($essentials)) {
            $query->with($essentials);
        }

        return $query;
    }

    public function scopeWithTranslations(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'fr');

        return $query->with(['translations' => function ($q) use ($locale, $fallback) {
            $q->whereIn('locale', [$locale, $fallback]);
        }]);
    }

    public function scopeSelectEssentials(Builder $query): Builder
    {
        $essentialColumns = $this->getEssentialColumns();

        if (!empty($essentialColumns)) {
            $query->select($essentialColumns);
        }

        return $query;
    }

    protected function getEssentialRelations(): array
    {
        return $this->essentialRelations ?? [];
    }

    protected function getEssentialColumns(): array
    {
        return $this->essentialColumns ?? ['*'];
    }

    public function scopeOrderByPopularity(Builder $query): Builder
    {
        if (in_array('views_count', $this->fillable ?? [])) {
            return $query->orderByDesc('views_count');
        }

        return $query->orderByDesc('created_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        if (in_array('is_active', $this->fillable ?? [])) {
            return $query->where('is_active', true);
        }

        return $query;
    }

    public function scopeForLocale(Builder $query, ?string $locale = null): Builder
    {
        return $this->scopeWithTranslations($query, $locale);
    }
}
