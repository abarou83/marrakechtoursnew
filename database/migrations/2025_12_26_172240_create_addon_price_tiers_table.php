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
        Schema::create('addon_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_id')->constrained('addons')->onDelete('cascade');
            $table->integer('min_people');
            $table->integer('max_people');
            $table->decimal('price', 10, 2);
            $table->timestamps();
            
            $table->index('addon_id');
            $table->index(['addon_id', 'min_people', 'max_people']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addon_price_tiers');
    }
};
