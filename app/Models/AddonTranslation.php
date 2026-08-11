<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonTranslation extends Model
{
    protected $fillable = [
        'addon_id',
        'locale',
        'name',
    ];

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}
