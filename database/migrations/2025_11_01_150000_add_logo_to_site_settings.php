<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insertOrIgnore([
            [
                'key' => 'logo_path',
                'value' => null,
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'logo_path')->delete();
    }
};


