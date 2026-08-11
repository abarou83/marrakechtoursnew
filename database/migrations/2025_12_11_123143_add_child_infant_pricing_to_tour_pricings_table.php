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
        Schema::table('tour_pricings', function (Blueprint $table) {
            $table->decimal('child_discount_percentage', 5, 2)->nullable()->after('is_active')->comment('Pourcentage de réduction pour les enfants (ex: 10 pour -10%)');
            $table->decimal('infant_price', 10, 2)->default(0)->after('child_discount_percentage')->comment('Prix pour les bébés (0 = gratuit)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_pricings', function (Blueprint $table) {
            $table->dropColumn(['child_discount_percentage', 'infant_price']);
        });
    }
};
