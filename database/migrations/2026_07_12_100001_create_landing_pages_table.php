<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('destination');
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('tour_filters')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_indexed')->default(true);
            $table->integer('tours_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->unique(['type', 'destination_id', 'category_id']);
            $table->index(['is_published', 'is_indexed']);
        });

        Schema::create('landing_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('slug')->unique();
            $table->string('h1');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->text('intro_text')->nullable();
            $table->text('content')->nullable();
            $table->json('faqs')->nullable();
            $table->timestamps();

            $table->unique(['landing_page_id', 'locale']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_translations');
        Schema::dropIfExists('landing_pages');
    }
};
