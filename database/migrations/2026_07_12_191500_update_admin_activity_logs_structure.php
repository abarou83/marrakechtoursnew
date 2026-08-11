<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_activity_logs', 'admin_name')) {
                $table->string('admin_name')->nullable()->after('admin_id');
            }
            if (!Schema::hasColumn('admin_activity_logs', 'description')) {
                $table->text('description')->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('admin_activity_logs', 'properties')) {
                $table->json('properties')->nullable()->after('description');
            }
            if (!Schema::hasColumn('admin_activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('admin_activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }
        });

        if (Schema::hasColumn('admin_activity_logs', 'model_type') && Schema::hasColumn('admin_activity_logs', 'subject_type')) {
            \DB::statement('UPDATE admin_activity_logs SET subject_type = model_type WHERE subject_type IS NULL');
            \DB::statement('UPDATE admin_activity_logs SET subject_id = model_id WHERE subject_id IS NULL');
        }

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->change();
            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_activity_logs', 'admin_name')) {
                $table->dropColumn('admin_name');
            }
            if (Schema::hasColumn('admin_activity_logs', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('admin_activity_logs', 'properties')) {
                $table->dropColumn('properties');
            }
        });
    }
};
