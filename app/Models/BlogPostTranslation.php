<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostTranslation extends Model
{
    protected $fillable = [
        'blog_post_id',
        'locale',
        'slug',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }
}
