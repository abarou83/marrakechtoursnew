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
        Schema::create('pricing_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_pricing_id')->constrained('tour_pricings')->onDelete('cascade');
            $table->foreignId('addon_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(false);
            $table->decimal('override_price', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['tour_pricing_id', 'addon_id']);
            $table->index('tour_pricing_id');
            $table->index('addon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_addons');
    }
};
