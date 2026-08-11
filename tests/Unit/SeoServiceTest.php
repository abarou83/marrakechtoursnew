<?php

use App\Services\SeoService;

beforeEach(function () {
    $this->seo = app(SeoService::class);
});

test('generateMetaTags truncates long descriptions', function () {
    $meta = $this->seo->generateMetaTags([
        'title' => 'Test Tour',
        'description' => str_repeat('word ', 50),
    ]);

    expect(strlen($meta['description']))->toBeLessThanOrEqual(155);
});

test('generateMetaTags includes defaults', function () {
    $meta = $this->seo->generateMetaTags(['title' => 'Tour Sahara']);

    expect($meta)->toHaveKeys(['title', 'description', 'canonical', 'type', 'locale']);
    expect($meta['title'])->toBe('Tour Sahara');
});

test('truncateDescription respects max length', function () {
    $short = $this->seo->truncateDescription('Hello world', 20);
    expect(strlen($short))->toBeLessThanOrEqual(20);
});
