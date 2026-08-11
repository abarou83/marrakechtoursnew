<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
        $this->seed(\Database\Seeders\CurrencySeeder::class);
    }

    public function test_wishlist_toggle_requires_auth(): void
    {
        $response = $this->postJson('/api/wishlist/toggle', ['tour_id' => 1]);

        $response->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'login_required']);
    }
}
