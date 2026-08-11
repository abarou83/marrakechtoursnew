<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'path',
        'alt',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    protected function url(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->path) {
                return null;
            }

            return public_storage_url($this->path);
        });
    }
}
