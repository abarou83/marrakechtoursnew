<?php

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');

uses(Tests\TestCase::class)->in('Unit');

function seedMinimalSite(): void
{
    test()->seed(\Database\Seeders\LanguageSeeder::class);
    test()->seed(\Database\Seeders\CurrencySeeder::class);
}
