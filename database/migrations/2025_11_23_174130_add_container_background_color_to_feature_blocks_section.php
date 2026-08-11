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
        Schema::create('feature_blocks_section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('container_background_color', 7)->nullable()->default('#F9FAFB');
            $table->timestamps();
        });
        
        // Insert default settings
        \DB::table('feature_blocks_section_settings')->insert([
            'container_background_color' => '#F9FAFB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_blocks_section_settings');
    }
};
