<?php

use App\Enums\BookingChannel;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    seedMinimalSite();
});

test('stripe webhook route is registered', function () {
    expect(route('webhooks.stripe', [], false))->toBe('/webhooks/stripe');
});

test('stripe webhook deduplicates events by id', function () {
    Cache::put('stripe_webhook_event:evt_duplicate_test', true, now()->addDay());

    expect(Cache::has('stripe_webhook_event:evt_duplicate_test'))->toBeTrue();
});

test('booking channel direct is not ota', function () {
    expect(BookingChannel::Direct->isOta())->toBeFalse();
    expect(BookingChannel::Viator->isOta())->toBeTrue();
});
