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
        // Migrer les données existantes vers banner_translations
        $banners = DB::table('banners')->get();
        foreach ($banners as $banner) {
            if (Schema::hasColumn('banners', 'title')) {
                DB::table('banner_translations')->insertOrIgnore([
                    'banner_id' => $banner->id,
                    'locale' => config('app.locale', 'fr'),
                    'title' => $banner->title ?? 'Untitled',
                    'slug' => $banner->slug ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // For SQLite, we need to handle index dropping differently
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // For SQLite, drop the unique index first if it exists
            try {
                Schema::table('banners', function (Blueprint $table) {
                    $table->dropUnique(['slug']);
                });
            } catch (\Exception $e) {
                // Index may not exist
            }
        }

        // Supprimer les colonnes si elles existent
        if (Schema::hasColumn('banners', 'title')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        
        if (Schema::hasColumn('banners', 'slug')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('slug')->nullable()->after('title');
        });
    }
};
