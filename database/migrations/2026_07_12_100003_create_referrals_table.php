<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('referrer_client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('referred_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('referrer_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('referred_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->decimal('referrer_reward', 10, 2)->default(10);
            $table->decimal('referred_discount', 10, 2)->default(10);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index('referrer_client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
