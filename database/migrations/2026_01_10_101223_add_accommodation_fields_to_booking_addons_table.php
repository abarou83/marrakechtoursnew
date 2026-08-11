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
        Schema::table('booking_addons', function (Blueprint $table) {
            // These fields will be used if accommodation is stored as an addon
            $table->foreignId('accommodation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('room_type', ['single', 'double', 'twin', 'triple'])->nullable();
            $table->integer('nights')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_addons', function (Blueprint $table) {
            $table->dropForeign(['accommodation_id']);
            $table->dropColumn(['accommodation_id', 'room_type', 'nights']);
        });
    }
};