<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'difficulty')) {
                $table->string('difficulty')->default('moderate')->after('type');
            }
            if (!Schema::hasColumn('tours', 'min_age')) {
                $table->unsignedTinyInteger('min_age')->default(0)->after('difficulty');
            }
            if (!Schema::hasColumn('tours', 'departure_point')) {
                $table->string('departure_point')->nullable()->after('location');
            }
            if (!Schema::hasColumn('tours', 'departure_lat')) {
                $table->decimal('departure_lat', 10, 7)->nullable()->after('departure_point');
            }
            if (!Schema::hasColumn('tours', 'departure_lng')) {
                $table->decimal('departure_lng', 10, 7)->nullable()->after('departure_lat');
            }
            if (!Schema::hasColumn('tours', 'included')) {
                $table->json('included')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tours', 'excluded')) {
                $table->json('excluded')->nullable()->after('included');
            }
            if (!Schema::hasColumn('tours', 'highlights')) {
                $table->json('highlights')->nullable()->after('excluded');
            }
            if (!Schema::hasColumn('tours', 'cancellation_policy')) {
                $table->text('cancellation_policy')->nullable()->after('highlights');
            }
            if (!Schema::hasColumn('tours', 'booking_deadline_hours')) {
                $table->unsignedInteger('booking_deadline_hours')->default(24)->after('cancellation_policy');
            }
            if (!Schema::hasColumn('tours', 'avg_rating')) {
                $table->decimal('avg_rating', 2, 1)->default(0)->after('capacity');
            }
            if (!Schema::hasColumn('tours', 'reviews_count')) {
                $table->unsignedInteger('reviews_count')->default(0)->after('avg_rating');
            }
            if (!Schema::hasColumn('tours', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('reviews_count');
            }
            if (!Schema::hasColumn('tours', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('tours', 'is_bestseller')) {
                $table->boolean('is_bestseller')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('tours', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['is_active', 'is_featured']);
            $table->index(['is_active', 'is_bestseller']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'is_featured']);
            $table->dropIndex(['is_active', 'is_bestseller']);
            $table->dropIndex(['slug']);

            $columns = [
                'difficulty', 'min_age', 'departure_point', 'departure_lat', 'departure_lng',
                'included', 'excluded', 'highlights', 'cancellation_policy', 'booking_deadline_hours',
                'avg_rating', 'reviews_count', 'views_count', 'is_featured', 'is_bestseller', 'deleted_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tours', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
