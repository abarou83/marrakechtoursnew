<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
        $this->seed(\Database\Seeders\CurrencySeeder::class);
    }
    public function test_health_endpoint_returns_ok_status(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'version', 'checks']);
    }
}
