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
        Schema::table('tours', function (Blueprint $table) {
            // Ensure type column exists
            if (!Schema::hasColumn('tours', 'type')) {
                $table->enum('type', ['daytrip', 'activity', 'excursion', 'circuit'])
                      ->default('activity')
                      ->after('slug');
            }
            
            // Ensure is_active exists
            if (!Schema::hasColumn('tours', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
            
            // Ensure location exists
            if (!Schema::hasColumn('tours', 'location')) {
                $table->string('location')->nullable()->after('type');
            }
        });
        
        // Add indexes - skip for SQLite or wrap in try/catch
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('tours', function (Blueprint $table) {
                try { $table->index('type'); } catch (\Exception $e) {}
                try { $table->index('is_active'); } catch (\Exception $e) {}
                try { $table->index(['type', 'is_active']); } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('tours', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
