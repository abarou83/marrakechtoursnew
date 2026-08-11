<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureBlocksSectionSetting extends Model
{
    protected $table = 'feature_blocks_section_settings';
    
    protected $fillable = [
        'container_background_color',
    ];

    /**
     * Get the current settings (singleton pattern)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'container_background_color' => '#F9FAFB',
        ]);
    }
}
