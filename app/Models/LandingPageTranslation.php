<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageTranslation extends Model
{
    protected $fillable = [
        'landing_page_id',
        'locale',
        'slug',
        'h1',
        'meta_title',
        'meta_description',
        'intro_text',
        'content',
        'faqs',
    ];

    protected $casts = [
        'faqs' => 'array',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function getUrlAttribute(): string
    {
        return route('landing.show', ['locale' => $this->locale, 'slug' => $this->slug]);
    }
}
