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
        Schema::create('tour_private_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_pricing_id')->constrained('tour_pricings')->onDelete('cascade');
            $table->integer('min_people');
            $table->integer('max_people');
            $table->decimal('price', 10, 2);
            $table->timestamps();
            
            $table->index('tour_pricing_id');
            $table->index(['tour_pricing_id', 'min_people', 'max_people']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_private_prices');
    }
};




