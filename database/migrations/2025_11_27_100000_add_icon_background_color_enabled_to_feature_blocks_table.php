<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feature_blocks', function (Blueprint $table) {
            $table->boolean('icon_background_color_enabled')->default(false)->after('icon_background_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_blocks', function (Blueprint $table) {
            $table->dropColumn('icon_background_color_enabled');
        });
    }
};


