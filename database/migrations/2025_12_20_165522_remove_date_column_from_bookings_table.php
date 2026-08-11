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
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'date')) {
                // Supprimer la colonne date (l'index sera supprimé automatiquement)
                $table->dropColumn('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'date')) {
                $table->date('date')->nullable()->after('tour_date_id');
            }
        });
        
        // Recréer l'index après avoir ajouté la colonne
        if (Schema::hasColumn('bookings', 'date') && Schema::hasColumn('bookings', 'tour_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index(['tour_id', 'date'], 'bookings_tour_id_date_index');
            });
        }
    }
};
