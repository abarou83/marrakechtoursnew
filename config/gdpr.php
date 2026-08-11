<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Consent Configuration
    |--------------------------------------------------------------------------
    */

    'consent_version' => env('CONSENT_VERSION', '1.0'),

    'consent_max_age_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    */

    'retention' => [
        'bookings_days' => 365 * 7,
        'activity_logs_days' => 365 * 2,
        'audit_logs_days' => 365 * 5,
        'exports_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Limits
    |--------------------------------------------------------------------------
    */

    'export' => [
        'rate_limit_per_day' => 3,
        'file_retention_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie Categories
    |--------------------------------------------------------------------------
    */

    'cookies' => [
        'necessary' => [
            'name' => 'Cookies nécessaires',
            'description' => 'Indispensables au fonctionnement du site.',
            'required' => true,
            'items' => [
                'laravel_session' => 'Session utilisateur',
                'XSRF-TOKEN' => 'Protection CSRF',
                'cookie_consent' => 'Mémorisation de vos choix',
            ],
        ],
        'analytics' => [
            'name' => 'Cookies analytiques',
            'description' => 'Nous aident à comprendre comment vous utilisez le site.',
            'required' => false,
            'items' => [
                '_ga' => 'Google Analytics',
                '_gid' => 'Google Analytics',
                '_gat' => 'Google Analytics',
            ],
        ],
        'marketing' => [
            'name' => 'Cookies marketing',
            'description' => 'Permettent de vous proposer des publicités personnalisées.',
            'required' => false,
            'items' => [
                '_fbp' => 'Facebook Pixel',
                '_gcl_au' => 'Google Ads',
            ],
        ],
        'preferences' => [
            'name' => 'Cookies de préférences',
            'description' => 'Mémorisent vos choix (langue, devise).',
            'required' => false,
            'items' => [
                'locale' => 'Langue préférée',
                'currency' => 'Devise préférée',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Processing Purposes
    |--------------------------------------------------------------------------
    */

    'purposes' => [
        'booking' => [
            'name' => 'Gestion des réservations',
            'legal_basis' => 'contract',
            'retention' => '7 ans (obligation légale)',
        ],
        'marketing' => [
            'name' => 'Communications marketing',
            'legal_basis' => 'consent',
            'retention' => 'Jusqu\'au retrait du consentement',
        ],
        'analytics' => [
            'name' => 'Analyse et amélioration',
            'legal_basis' => 'legitimate_interest',
            'retention' => '26 mois',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */

    'dpo' => [
        'name' => env('DPO_NAME', 'Responsable Protection des Données'),
        'email' => env('DPO_EMAIL', 'dpo@marrakechtours.net'),
    ],

    'supervisory_authority' => [
        'name' => 'CNIL',
        'url' => 'https://www.cnil.fr',
    ],
];
