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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->enum('link_type', ['internal', 'external', 'category'])->default('internal');
            $table->string('link_url')->nullable(); // Pour liens externes ou internes personnalisés
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Pour liens vers catégories
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade'); // Pour sous-menus
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable(); // Pour icônes Font Awesome
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
