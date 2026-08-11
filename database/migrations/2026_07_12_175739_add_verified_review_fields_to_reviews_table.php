<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('tour_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('reviews', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('reviews', 'title')) {
                $table->string('title')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('reviews', 'guide_rating')) {
                $table->tinyInteger('guide_rating')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'value_rating')) {
                $table->tinyInteger('value_rating')->nullable()->after('guide_rating');
            }
            if (!Schema::hasColumn('reviews', 'recommend')) {
                $table->boolean('recommend')->default(true)->after('value_rating');
            }
            if (!Schema::hasColumn('reviews', 'author_name')) {
                $table->string('author_name')->nullable()->after('recommend');
            }
            if (!Schema::hasColumn('reviews', 'author_country')) {
                $table->string('author_country', 100)->nullable()->after('author_name');
            }
            if (!Schema::hasColumn('reviews', 'travel_date')) {
                $table->date('travel_date')->nullable()->after('author_country');
            }
            if (!Schema::hasColumn('reviews', 'travel_type')) {
                $table->string('travel_type', 50)->nullable()->after('travel_date');
            }
            if (!Schema::hasColumn('reviews', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('travel_type');
            }
            if (!Schema::hasColumn('reviews', 'status')) {
                $table->string('status', 20)->default('pending')->after('is_verified');
            }
            if (!Schema::hasColumn('reviews', 'locale')) {
                $table->string('locale', 10)->default('fr')->after('status');
            }
            if (!Schema::hasColumn('reviews', 'admin_response')) {
                $table->text('admin_response')->nullable()->after('locale');
            }
            if (!Schema::hasColumn('reviews', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('admin_response');
            }
        });

        if (Schema::hasColumn('reviews', 'client_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index('client_id');
                $table->index('status');
                $table->index('is_verified');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $columns = [
                'client_id', 'booking_id', 'title', 'guide_rating', 'value_rating',
                'recommend', 'author_name', 'author_country', 'travel_date', 'travel_type',
                'is_verified', 'status', 'locale', 'admin_response', 'responded_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('reviews', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
