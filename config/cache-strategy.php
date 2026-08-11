<?php

return [
    'ttl' => [
        'short' => 300,
        'default' => 3600,
        'long' => 86400,
        'forever' => null,
    ],

    'keys' => [
        'home' => 'home_{locale}',
        'tours_list' => 'tours_list_{locale}_{hash}',
        'tour' => 'tour_{id}_{locale}',
        'category' => 'category_{id}_{locale}',
        'page' => 'page_{slug}_{locale}',
        'landing' => 'landing_{slug}_{locale}',
        'blog_list' => 'blog_list_{locale}_{page}',
        'blog_post' => 'blog_{slug}_{locale}',
        'settings' => 'settings_{group}',
        'menu' => 'menu_{location}_{locale}',
        'footer' => 'footer_{locale}',
        'currencies' => 'currencies_active',
        'languages' => 'languages_active',
    ],

    'tags' => [
        'catalog' => ['tours', 'categories', 'pricing'],
        'content' => ['pages', 'blog', 'faqs'],
        'config' => ['settings', 'menus', 'languages', 'currencies'],
        'seo' => ['landing', 'sitemap'],
    ],

    'auto_invalidate' => [
        'Tour' => ['catalog'],
        'Category' => ['catalog'],
        'TourPricing' => ['catalog'],
        'Page' => ['content'],
        'BlogPost' => ['content'],
        'LandingPage' => ['seo'],
        'Setting' => ['config'],
        'Menu' => ['config'],
    ],
];
