<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $textDirection ?? 'ltr' }}" @class(['rtl' => ($isRtl ?? false)])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @isset($cspNonce)
            <meta property="csp-nonce" nonce="{{ $cspNonce }}" content="{{ $cspNonce }}">
        @endisset
        <meta name="client-authenticated" content="{{ auth('client')->check() ? '1' : '0' }}">
        <meta name="app-version" content="{{ config('app.version', 'dev') }}">

        <!-- Favicon -->
        @php
            $favicon = site_setting('favicon_path');
        @endphp
        @if($favicon && \Storage::disk('public')->exists($favicon))
            <link rel="icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
            <link rel="apple-touch-icon" href="{{ \Storage::url($favicon) }}">
        @endif

    <!-- SEO Meta Tags -->
    @hasSection('seo_meta_tags')
        @yield('seo_meta_tags')
    @elseif(View::hasSection('title'))
        <title>@yield('title') - {{ config('app.name') }}</title>
        @hasSection('meta_description')
            <meta name="description" content="@yield('meta_description')">
        @endif
    @else
        <x-seo.meta-tags />
    @endif

    @stack('head')
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

        <!-- Flag Icons CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        
        <!-- Dynamic Colors -->
        <x-dynamic-colors />
        
        <!-- Structured Data (JSON-LD) -->
        @stack('structured_data')
        
        @stack('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-background {{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}" x-init="@if($errors->any() && old('email') && !auth('client')->check()) $store.loginModal.openModal() @endif">
        <div>
            @include('layouts.navigation')

        <!-- Page Content -->
        <main class="pt-16 nav:pt-16">
            <!-- Success Message -->
            @if(session('success'))
                <div id="success-message" 
                     class="fixed top-16 right-4 z-50 max-w-md animate-fade-in-down"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform translate-x-full"
                     x-init="setTimeout(() => { show = false; setTimeout(() => { $el.remove(); }, 300); }, 3000)">
                    <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div id="error-message" 
                     class="fixed top-16 right-4 z-50 max-w-md animate-fade-in-down"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform translate-x-full"
                     x-init="setTimeout(() => { show = false; setTimeout(() => { $el.remove(); }, 300); }, 3000)">
                    <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot }}
            @endif
        </main>
        @include('layouts.footer')
        </div>

        @livewire('cookie-consent')

        @livewire('exit-intent-popup')

        <x-marketing.analytics />
        
        <!-- Login Modal (Global) — masqué si client déjà connecté -->
        @guest('client')
            <x-login-modal />
        @endguest
        
        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset></script>

        <!-- CSRF Token Helper -->
        <script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
            // Fonction globale pour récupérer le token CSRF dynamiquement
            window.getCsrfToken = function() {
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                return metaTag ? metaTag.content : '';
            };

            // Mettre à jour le token CSRF après chaque requête AJAX réussie
            // pour éviter les problèmes de token expiré
            document.addEventListener('DOMContentLoaded', function() {
                // Intercepter les réponses fetch pour mettre à jour le token si nécessaire
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    return originalFetch.apply(this, args).then(response => {
                        // Si la réponse contient un nouveau token CSRF, le mettre à jour
                        const newToken = response.headers.get('X-CSRF-TOKEN');
                        if (newToken) {
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) {
                                metaTag.content = newToken;
                            }
                        }
                        return response;
                    });
                };
            });
        </script>
        
        @stack('scripts')

        @livewireScripts(isset($cspNonce) ? ['nonce' => $cspNonce] : [])

        @stack('scripts-after-livewire')
    </body>
</html>
