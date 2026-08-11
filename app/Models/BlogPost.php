<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BlogPost extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'is_active',
        'published_at',
        'featured_image',
        'author_id',
        'tags',
        'views_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
        'views_count' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(BlogPostTranslation::class);
    }

    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $translation = $this->translations()->where('locale', $locale)->first();

        if ($translation) {
            return $translation;
        }

        return $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_post_category', 'blog_post_id', 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile();

        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('og')
            ->width(1200)
            ->height(630);

        $this->addMediaConversion('card')
            ->width(600)
            ->height(400);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeLatestPublished($query)
    {
        return $query->published()->orderByDesc('published_at')->orderByDesc('created_at');
    }

    public function getReadingTimeAttribute(): int
    {
        $translation = $this->translate();
        if (!$translation || !$translation->content) {
            return 1;
        }
        
        $wordCount = str_word_count(strip_tags($translation->content));
        return max(1, (int) ceil($wordCount / 200));
    }
}
