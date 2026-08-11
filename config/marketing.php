<?php

return [
    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID'),

    'meta_pixel_id' => env('META_PIXEL_ID'),

    'meta_capi_enabled' => env('META_CAPI_ENABLED', false),

    'referral' => [
        'referrer_reward' => 10.00,
        'referred_discount_percent' => 10,
        'currency' => 'EUR',
    ],

    'gift_card' => [
        'amounts' => [50, 75, 100, 150, 200],
        'validity_months' => 12,
        'min_amount' => 25,
        'max_amount' => 500,
    ],

    'abandoned_cart' => [
        'delay_hours' => 2,
        'promo_percent' => 5,
    ],

    'exit_intent' => [
        'enabled' => env('EXIT_INTENT_ENABLED', true),
        'promo_code' => env('EXIT_INTENT_PROMO', 'BIENVENUE10'),
        'promo_percent' => 10,
    ],

    'deposit' => [
        'enabled' => env('DEPOSIT_ENABLED', true),
        'percent' => 20,
    ],
];
