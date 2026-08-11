<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        @php
            $favicon = site_setting('favicon_path');
        @endphp
        @if($favicon && \Storage::disk('public')->exists($favicon))
            <link rel="icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
            <link rel="apple-touch-icon" href="{{ \Storage::url($favicon) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Dynamic Colors -->
        <x-dynamic-colors />
    </head>
    <body class="font-sans text-gray-900 antialiased bg-background">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background">
            <div>
                <a href="/">
                    <x-application-logo class="w-48 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
