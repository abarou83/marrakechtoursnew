<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['blog_category_id', 'locale']);
            $table->index(['locale', 'slug']);
        });

        Schema::create('blog_post_category', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_category_id']);
        });

        if (Schema::hasTable('blog_posts') && !Schema::hasColumn('blog_posts', 'tags')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->json('tags')->nullable()->after('is_active');
                $table->unsignedBigInteger('views_count')->default(0)->after('tags');
                $table->unsignedBigInteger('author_id')->nullable()->after('views_count');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_category_translations');
        Schema::dropIfExists('blog_categories');

        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropColumn(['tags', 'views_count', 'author_id']);
            });
        }
    }
};
