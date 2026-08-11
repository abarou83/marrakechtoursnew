<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ $textDirection ?? 'ltr' }}"
    class="scroll-smooth"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Marrakech Tours') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('Excursions et tours depuis Marrakech - Désert, Mer, Montagnes') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet">

    @if(isset($canonicalUrl))
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    @stack('head')
</head>
<body class="min-h-screen bg-sand-50 font-sans text-gray-900 antialiased">
    @if(isset($header))
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-sand-200">
            {{ $header }}
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>

    @if(isset($footer))
        {{ $footer }}
    @endif

    @livewireScripts

    @stack('scripts')
</body>
</html>
