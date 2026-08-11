<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds is_included field to mark addons that are included in the base price
     * (free for the customer, already part of the tour price)
     */
    public function up(): void
    {
        Schema::table('pricing_addons', function (Blueprint $table) {
            $table->boolean('is_included')->default(false)->after('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_addons', function (Blueprint $table) {
            $table->dropColumn('is_included');
        });
    }
};
