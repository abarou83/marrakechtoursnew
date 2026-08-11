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
        // Check if tour_group_prices or tour_private_prices tables exist and drop foreign keys first
        if (Schema::hasTable('tour_group_prices')) {
            Schema::table('tour_group_prices', function (Blueprint $table) {
                $table->dropForeign(['tour_pricing_id']);
            });
        }

        if (Schema::hasTable('tour_private_prices')) {
            Schema::table('tour_private_prices', function (Blueprint $table) {
                $table->dropForeign(['tour_pricing_id']);
            });
        }

        // Drop existing tour_pricings table if it exists
        Schema::dropIfExists('tour_pricings');
        
        // Create new tour_pricings table with new structure
        Schema::create('tour_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->enum('pricing_mode', ['group', 'private'])->default('group');
            $table->enum('season', ['low', 'normal', 'high'])->default('normal');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tour_id', 'pricing_mode', 'season']);
            $table->index(['tour_id', 'is_active']);
        });

        // Recreate foreign keys if child tables exist
        if (Schema::hasTable('tour_group_prices')) {
            Schema::table('tour_group_prices', function (Blueprint $table) {
                $table->foreign('tour_pricing_id')
                    ->references('id')
                    ->on('tour_pricings')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('tour_private_prices')) {
            Schema::table('tour_private_prices', function (Blueprint $table) {
                $table->foreign('tour_pricing_id')
                    ->references('id')
                    ->on('tour_pricings')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys from child tables
        if (Schema::hasTable('tour_group_prices')) {
            Schema::table('tour_group_prices', function (Blueprint $table) {
                $table->dropForeign(['tour_pricing_id']);
            });
        }

        if (Schema::hasTable('tour_private_prices')) {
            Schema::table('tour_private_prices', function (Blueprint $table) {
                $table->dropForeign(['tour_pricing_id']);
            });
        }

        Schema::dropIfExists('tour_pricings');
    }
};
