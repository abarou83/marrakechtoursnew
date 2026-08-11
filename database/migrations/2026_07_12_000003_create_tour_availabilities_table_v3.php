<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_availabilities')) {
            Schema::create('tour_availabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
                $table->date('date');
                $table->unsignedInteger('spots_total')->default(0);
                $table->unsignedInteger('spots_available')->default(0);
                $table->decimal('price_override', 10, 2)->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                $table->unique(['tour_id', 'date']);
                $table->index(['tour_id', 'date', 'is_available']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_availabilities');
    }
};
