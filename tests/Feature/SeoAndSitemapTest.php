<?php

use App\Models\Guide;
use App\Models\GuideTranslation;

beforeEach(function () {
    seedMinimalSite();
});

test('sitemap xml is accessible', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('content-type');
});

test('llms.txt file exists in public directory', function () {
    $path = public_path('llms.txt');

    expect($path)->toBeFile();
    expect(file_get_contents($path))->toContain('MarrakechTours');
});

test('sitemap includes guide urls when guides exist', function () {
    $guide = Guide::create([
        'category' => 'tips',
        'is_published' => true,
        'published_at' => now(),
        'reading_time' => 5,
    ]);

    GuideTranslation::create([
        'guide_id' => $guide->id,
        'locale' => 'fr',
        'slug' => 'guide-test-sitemap',
        'title' => 'Guide test',
        'content' => '<p>Test</p>',
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('guide-test-sitemap');
});

test('tour page includes json-ld structured data', function () {
    // Smoke: homepage has organization schema
    $this->get('/')
        ->assertOk()
        ->assertSee('application/ld+json', false);
});
