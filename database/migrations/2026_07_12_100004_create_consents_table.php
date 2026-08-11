<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64);
            $table->string('user_agent_hash', 64)->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->json('choices');
            $table->string('consent_version')->default('1.0');
            $table->string('source')->default('cookie_banner');
            $table->timestamps();

            $table->index(['ip_hash', 'created_at']);
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
