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
        // Ajouter les champs SEO manquants à tour_translations
        Schema::table('tour_translations', function (Blueprint $table) {
            $table->string('canonical_url')->nullable()->after('focus_keyword');
            $table->string('og_image')->nullable()->after('canonical_url');
        });

        // Ajouter les champs SEO manquants à category_translations
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('canonical_url')->nullable()->after('focus_keyword');
            $table->string('og_image')->nullable()->after('canonical_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_translations', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'og_image']);
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'og_image']);
        });
    }
};




