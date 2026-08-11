<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{
    protected $table = 'banner_translations';

    protected $fillable = [
        'banner_id',
        'locale',
        'title',
        'slug',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class, 'banner_id');
    }
}
