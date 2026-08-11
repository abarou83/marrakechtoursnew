<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_active',
        'is_default',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope pour obtenir uniquement les langues actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Obtenir la langue par défaut
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Obtenir toutes les langues actives
     */
    public static function getActive()
    {
        return self::active()->get();
    }

    /**
     * Vérifier si une langue est active
     */
    public static function isActive($code)
    {
        return self::where('code', $code)->where('is_active', true)->exists();
    }
}




