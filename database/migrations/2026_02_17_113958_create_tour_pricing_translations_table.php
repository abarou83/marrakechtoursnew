<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_pricing_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_pricing_id')->constrained('tour_pricings')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title');
            $table->unique(['tour_pricing_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_pricing_translations');
    }
};
