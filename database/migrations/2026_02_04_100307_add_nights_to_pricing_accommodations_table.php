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
        Schema::table('pricing_accommodations', function (Blueprint $table) {
            $table->integer('nights')->default(1)->after('is_optional')->comment('Nombre de nuits pour cet hébergement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_accommodations', function (Blueprint $table) {
            $table->dropColumn('nights');
        });
    }
};
