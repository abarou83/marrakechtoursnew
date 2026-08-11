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
        // Add SEO fields to tours
        Schema::table('tours', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('meta_keywords');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->string('focus_keyword')->nullable()->after('og_image');
        });

        // Add SEO fields to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('meta_keywords');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->string('focus_keyword')->nullable()->after('og_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url', 'og_image', 'focus_keyword']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url', 'og_image', 'focus_keyword']);
        });
    }
};
