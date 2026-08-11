<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('tour_id')->nullable()->after('page_id')->constrained('tours')->onDelete('set null');
        });

        // Only for MySQL - SQLite uses check constraints
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE menu_items MODIFY COLUMN link_type ENUM('internal', 'external', 'custom', 'category', 'page', 'tour') DEFAULT 'custom'");
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE menu_items MODIFY COLUMN link_type ENUM('internal', 'external', 'category', 'page') DEFAULT 'internal'");
        }
    }
};
