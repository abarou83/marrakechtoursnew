<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insérer les couleurs par défaut
        DB::table('site_settings')->insert([
            [
                'key' => 'primary_color',
                'value' => '#211951',
                'group' => 'colors',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'secondary_color',
                'value' => '#836FFF',
                'group' => 'colors',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'accent_color',
                'value' => '#15F5BA',
                'group' => 'colors',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'success_color',
                'value' => '#22c55e',
                'group' => 'colors',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

