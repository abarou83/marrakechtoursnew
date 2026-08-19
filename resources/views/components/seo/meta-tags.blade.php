@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => url()->current(),
    'ogImage' => null,
    'focusKeyword' => null,
])

@php
    // Détecter si on est sur la page d'accueil
    $isHomePage = request()->routeIs('home') || request()->path() === '/';
    $locale = app()->getLocale();

    // Valeurs par défaut depuis les paramètres du site (clés multilingues admin → seo_home_title_fr, etc.)
    if ($isHomePage) {
        $seoTitle = $title ?? site_setting(
            'seo_home_title_' . $locale,
            site_setting('seo_home_title', config('app.name', 'Tourify') . ' - Découvrez nos tours et excursions')
        );
        $seoDescription = $description ?? site_setting(
            'seo_home_description_' . $locale,
            site_setting('seo_home_description', 'Découvrez nos tours et excursions uniques. Réservez votre prochaine aventure avec nous et créez des souvenirs inoubliables.')
        );
        $seoKeywords = $keywords ?? site_setting(
            'seo_home_keywords_' . $locale,
            site_setting('seo_home_keywords', 'tours, excursions, voyages, aventures, réservation')
        );
        $seoOgImage = $ogImage ?? (site_setting('seo_home_og_image') ? Storage::url(site_setting('seo_home_og_image')) : asset('images/og-default.jpg'));
    } else {
        $seoTitle = $title ?? config('app.name', 'Tourify');
        $seoDescription = $description ?? 'Réservez vos tours et excursions en toute simplicité';
        $seoKeywords = $keywords ?? 'tours, excursions, réservation, voyages, aventures';
        $seoOgImage = $ogImage ?? asset('images/og-default.jpg');
    }
@endphp

{{-- Basic Meta Tags --}}
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
@if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoOgImage }}">
<meta property="og:site_name" content="{{ site_setting('company_name', config('app.name', 'Tourify')) }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoOgImage }}">

{{-- Robots --}}
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

{{-- Additional SEO Tags --}}
<meta name="author" content="{{ site_setting('company_name', config('app.name', 'Tourify')) }}">
<meta name="language" content="{{ str_replace('_', '-', $locale) }}">
<meta http-equiv="content-language" content="{{ str_replace('_', '-', $locale) }}">




