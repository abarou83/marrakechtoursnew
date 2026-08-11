<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureBlockTranslation extends Model
{
    protected $fillable = [
        'feature_block_id',
        'locale',
        'title',
        'description',
    ];
}
