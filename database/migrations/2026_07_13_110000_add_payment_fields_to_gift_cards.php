<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('gift_cards', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('gift_cards', 'payment_status')) {
                $table->string('payment_status', 20)->default('pending')->after('payment_intent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            if (Schema::hasColumn('gift_cards', 'payment_intent_id')) {
                $table->dropColumn('payment_intent_id');
            }
            if (Schema::hasColumn('gift_cards', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
