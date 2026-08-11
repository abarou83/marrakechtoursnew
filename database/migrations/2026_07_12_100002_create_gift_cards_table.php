<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->decimal('initial_amount', 10, 2);
            $table->decimal('remaining_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->foreignId('purchaser_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
