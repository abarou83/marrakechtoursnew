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
        Schema::create('tour_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ex: "Private Guide Only", "VIP Package"
            $table->text('description')->nullable();
            $table->decimal('price_min', 10, 2); // Prix minimum
            $table->decimal('price_max', 10, 2)->nullable(); // Prix maximum (si range)
            $table->string('currency', 3)->default('EUR'); // EUR, USD, etc.
            $table->integer('min_participants')->default(1);
            $table->integer('max_participants')->nullable();
            $table->boolean('is_default')->default(false); // Tarif par défaut
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_pricings');
    }
};




