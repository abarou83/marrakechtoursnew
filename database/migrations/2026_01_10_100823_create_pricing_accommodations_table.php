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
        Schema::create('pricing_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_pricing_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->boolean('is_optional')->default(true)->comment('Si false, l\'hébergement est inclus par défaut');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Unique constraint: one accommodation per pricing
            $table->unique(['tour_pricing_id', 'accommodation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_accommodations');
    }
};