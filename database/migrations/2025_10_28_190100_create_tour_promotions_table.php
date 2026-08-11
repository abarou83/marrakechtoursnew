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
        Schema::create('tour_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ex: "Early Bird", "Summer Sale"
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']); // % ou montant fixe
            $table->decimal('discount_value', 10, 2); // 20 (pour 20%) ou 10.00 (pour 10€)
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_limit')->nullable(); // Limite d'utilisation
            $table->integer('used_count')->default(0); // Nombre d'utilisations
            $table->string('badge_text')->nullable(); // ex: "PROMO", "-20%"
            $table->string('badge_color')->nullable(); // ex: "red", "green"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_promotions');
    }
};




