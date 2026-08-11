<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $table = 'page_translations';

    protected $fillable = [
        'page_id',
        'locale',
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}

