<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->default('general');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('featured_image')->nullable();
            $table->unsignedInteger('reading_time')->default(5);
            $table->unsignedInteger('views_count')->default(0);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('category');
        });

        Schema::create('guide_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('slug');
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['guide_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::create('guide_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['guide_id', 'tour_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'channel_external_id')) {
                $table->string('channel_external_id')->nullable()->after('channel');
            }
            if (!Schema::hasColumn('bookings', 'channel_notes')) {
                $table->text('channel_notes')->nullable()->after('channel_external_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'channel_external_id')) {
                $table->dropColumn('channel_external_id');
            }
            if (Schema::hasColumn('bookings', 'channel_notes')) {
                $table->dropColumn('channel_notes');
            }
        });

        Schema::dropIfExists('guide_tour');
        Schema::dropIfExists('guide_translations');
        Schema::dropIfExists('guides');
    }
};
