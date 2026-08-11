<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_activity_logs')) {
            return;
        }

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_activity_logs')) {
            return;
        }

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
