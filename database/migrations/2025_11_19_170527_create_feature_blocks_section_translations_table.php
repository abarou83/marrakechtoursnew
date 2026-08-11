<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_blocks_section_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unique('locale');
            $table->timestamps();
        });
        
        // Insert default values for active languages
        try {
            $activeLocales = Language::active()->pluck('code')->toArray();
            if (empty($activeLocales)) {
                $activeLocales = ['fr', 'en']; // Fallback
            }
            
            $defaultTitles = [
                'fr' => 'Pourquoi réserver avec nous ?',
                'en' => 'Why book with Viator?',
                'ar' => 'لماذا تحجز معنا؟',
            ];
            
            foreach ($activeLocales as $locale) {
                DB::table('feature_blocks_section_translations')->insertOrIgnore([
                    'locale' => $locale,
                    'title' => $defaultTitles[$locale] ?? 'Why book with us?',
                    'description' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // If languages table doesn't exist yet, use defaults
            $defaultLocales = ['fr', 'en'];
            foreach ($defaultLocales as $locale) {
                DB::table('feature_blocks_section_translations')->insertOrIgnore([
                    'locale' => $locale,
                    'title' => $locale === 'fr' ? 'Pourquoi réserver avec nous ?' : 'Why book with Viator?',
                    'description' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_blocks_section_translations');
    }
};
