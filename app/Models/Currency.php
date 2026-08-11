<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'rate_to_base',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'rate_to_base' => 'decimal:6',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
}
