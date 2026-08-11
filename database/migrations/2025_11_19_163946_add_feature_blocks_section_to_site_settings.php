<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cette migration ne fait plus rien car on utilise maintenant feature_blocks_section_translations
        // Gardé pour compatibilité mais vide
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'feature_blocks_section_title',
                'feature_blocks_section_description',
            ])
            ->delete();
    }
};
