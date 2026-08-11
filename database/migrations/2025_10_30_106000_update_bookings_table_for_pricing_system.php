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
        Schema::table('bookings', function (Blueprint $table) {
            // Add new pricing system fields only if they don't exist
            if (!Schema::hasColumn('bookings', 'date')) {
                $table->date('date')->nullable()->after('tour_date_id');
            }
            
            if (!Schema::hasColumn('bookings', 'pricing_mode')) {
                $table->enum('pricing_mode', ['group', 'private'])->default('group')->after('date');
            }
            
            if (!Schema::hasColumn('bookings', 'adults')) {
                $table->integer('adults')->default(0)->after('pricing_mode');
            }
            
            if (!Schema::hasColumn('bookings', 'children')) {
                $table->integer('children')->default(0)->after('adults');
            }
            
            if (!Schema::hasColumn('bookings', 'base_price')) {
                $table->decimal('base_price', 10, 2)->default(0)->after('children');
            }
            
            if (!Schema::hasColumn('bookings', 'addons_total')) {
                $table->decimal('addons_total', 10, 2)->default(0)->after('base_price');
            }
            
            // Rename total_amount if it doesn't match, or keep it and add total_price
            if (!Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('addons_total');
            }
        });

        // Add indexes (skip for SQLite as it handles indexes differently)
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            $connection = Schema::getConnection();
            
            if (Schema::hasColumn('bookings', 'tour_id') && Schema::hasColumn('bookings', 'date')) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->index(['tour_id', 'date']);
                    });
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
            
            if (Schema::hasColumn('bookings', 'pricing_mode')) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->index('pricing_mode');
                    });
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columnsToDrop = ['date', 'pricing_mode', 'adults', 'children', 'base_price', 'addons_total', 'total_price'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};




