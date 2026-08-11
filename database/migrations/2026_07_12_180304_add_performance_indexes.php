<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfNotExists('tours', 'idx_tours_active_featured', ['is_active', 'is_featured']);
        $this->addIndexIfNotExists('tours', 'idx_tours_slug', ['slug']);
        $this->addIndexIfNotExists('tours', 'idx_tours_popularity', ['is_active', 'views_count']);

        $this->addIndexIfNotExists('tour_translations', 'idx_tour_trans_locale_slug', ['locale', 'slug']);

        $this->addIndexIfNotExists('bookings', 'idx_bookings_client_status', ['client_id', 'status']);
        $this->addIndexIfNotExists('bookings', 'idx_bookings_travel_date', ['travel_date']);
        $this->addIndexIfNotExists('bookings', 'idx_bookings_reference', ['reference']);
        $this->addIndexIfNotExists('bookings', 'idx_bookings_payment', ['payment_status', 'status']);

        $this->addIndexIfNotExists('reviews', 'idx_reviews_tour_status', ['tour_id', 'status']);
        $this->addIndexIfNotExists('reviews', 'idx_reviews_client', ['client_id']);

        $this->addIndexIfNotExists('blog_posts', 'idx_blog_published', ['is_active', 'published_at']);

        if (Schema::hasTable('blog_post_translations')) {
            $this->addIndexIfNotExists('blog_post_translations', 'idx_blog_trans_slug', ['locale', 'slug']);
        }

        if (Schema::hasTable('landing_pages')) {
            $this->addIndexIfNotExists('landing_pages', 'idx_landing_published', ['is_published', 'is_indexed']);
        }

        if (Schema::hasTable('landing_page_translations')) {
            $this->addIndexIfNotExists('landing_page_translations', 'idx_landing_trans_slug', ['locale', 'slug']);
        }

        $this->addIndexIfNotExists('categories', 'idx_categories_active', ['is_active']);

        if (Schema::hasTable('tour_availabilities')) {
            $this->addIndexIfNotExists('tour_availabilities', 'idx_availability_date', ['tour_id', 'date', 'is_available']);
        }

        if (Schema::hasTable('promo_codes')) {
            $this->addIndexIfNotExists('promo_codes', 'idx_promo_active', ['code', 'is_active']);
        }
    }

    public function down(): void
    {
        $indexes = [
            'tours' => ['idx_tours_active_featured', 'idx_tours_slug', 'idx_tours_popularity'],
            'tour_translations' => ['idx_tour_trans_locale_slug'],
            'bookings' => ['idx_bookings_client_status', 'idx_bookings_travel_date', 'idx_bookings_reference', 'idx_bookings_payment'],
            'reviews' => ['idx_reviews_tour_status', 'idx_reviews_client'],
            'blog_posts' => ['idx_blog_published'],
            'blog_post_translations' => ['idx_blog_trans_slug'],
            'landing_pages' => ['idx_landing_published'],
            'landing_page_translations' => ['idx_landing_trans_slug'],
            'categories' => ['idx_categories_active'],
            'tour_availabilities' => ['idx_availability_date'],
            'promo_codes' => ['idx_promo_active'],
        ];

        foreach ($indexes as $table => $indexNames) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($indexNames) {
                    foreach ($indexNames as $indexName) {
                        try {
                            $t->dropIndex($indexName);
                        } catch (\Exception $e) {
                        }
                    }
                });
            }
        }
    }

    protected function addIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }

        try {
            $existingIndexes = collect(DB::select("PRAGMA index_list('{$table}')"))
                ->pluck('name')
                ->toArray();

            if (in_array($indexName, $existingIndexes)) {
                return;
            }
        } catch (\Exception $e) {
        }

        try {
            Schema::table($table, function (Blueprint $t) use ($indexName, $columns) {
                $t->index($columns, $indexName);
            });
        } catch (\Exception $e) {
        }
    }
};
