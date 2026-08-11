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
        // Add page_id column
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable()->after('category_id')->constrained('pages')->onDelete('set null');
        });

        // Modify the enum to include 'page' - only for MySQL
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE menu_items MODIFY COLUMN link_type ENUM('internal', 'external', 'category', 'page') DEFAULT 'internal'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn('page_id');
        });

        // Revert the enum - only for MySQL
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE menu_items MODIFY COLUMN link_type ENUM('internal', 'external', 'category') DEFAULT 'internal'");
        }
    }
};
