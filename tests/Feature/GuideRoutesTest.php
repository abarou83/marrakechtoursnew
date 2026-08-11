<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\GuideTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuideRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
        $this->seed(\Database\Seeders\CurrencySeeder::class);
    }

    public function test_guides_index_is_accessible(): void
    {
        $response = $this->withSession(['locale' => 'fr'])->get('/guide');

        $response->assertStatus(200);
    }

    public function test_published_guide_show_page_is_accessible(): void
    {
        $guide = Guide::create([
            'category' => 'tips',
            'is_published' => true,
            'published_at' => now()->subDay(),
            'reading_time' => 5,
        ]);

        GuideTranslation::create([
            'guide_id' => $guide->id,
            'locale' => 'fr',
            'slug' => 'test-guide-maroc',
            'title' => 'Guide test Maroc',
            'content' => '<p>Contenu de test.</p>',
        ]);

        $response = $this->withSession(['locale' => 'fr'])->get('/guide/test-guide-maroc');

        $response->assertStatus(200)
            ->assertSee('Guide test Maroc');
    }

    public function test_unpublished_guide_returns_404(): void
    {
        $guide = Guide::create([
            'category' => 'tips',
            'is_published' => false,
            'reading_time' => 5,
        ]);

        GuideTranslation::create([
            'guide_id' => $guide->id,
            'locale' => 'fr',
            'slug' => 'guide-brouillon',
            'title' => 'Brouillon',
            'content' => '<p>Hidden</p>',
        ]);

        $this->get('/guide/guide-brouillon')->assertStatus(404);
    }
}
