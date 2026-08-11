<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideTranslation extends Model
{
    protected $fillable = [
        'guide_id',
        'locale',
        'slug',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
