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
        // Only run for MySQL - SQLite uses check constraints that were already set in the table creation
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `tour_pricings` MODIFY COLUMN `season` ENUM('low', 'normal', 'high', 'all') DEFAULT 'normal'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("UPDATE `tour_pricings` SET `season` = 'normal' WHERE `season` = 'all'");
            DB::statement("ALTER TABLE `tour_pricings` MODIFY COLUMN `season` ENUM('low', 'normal', 'high') DEFAULT 'normal'");
        }
    }
};
