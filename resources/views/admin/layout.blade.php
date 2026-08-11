<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Tourify') }}</title>
    
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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    
    <style>
        * { 
            font-family: 'Inter', sans-serif; 
            font-size: 14px;
        }
        body {
            font-size: 14px;
        }
        h1, h2, h3, h4, h5, h6 {
            font-size: inherit;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarCollapsed: false, mobileMenuOpen: false, personalizationOpen: {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.feature-blocks.*') ? 'true' : 'false' }} }" x-init="sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true'; $watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"></div>

        <!-- Sidebar Améliorée -->
        <aside class="bg-black border-r border-gray-800 flex flex-col shadow-sm transition-all duration-300 fixed lg:static inset-y-0 left-0 z-50 lg:z-auto lg:translate-x-0"
               :class="{
                   'w-20': sidebarCollapsed,
                   'w-72': !sidebarCollapsed,
                   'translate-x-0': mobileMenuOpen,
                   '-translate-x-full lg:!translate-x-0': !mobileMenuOpen
               }">
            <!-- Logo et Bouton Réduire -->
            <div class="h-16 md:h-20 flex items-center px-3 md:px-4 border-b border-gray-800" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                @php
                    // Utiliser le logo footer en priorité, sinon le logo principal
                    $footerLogoPath = site_setting('footer_logo_path');
                    $logoPath = $footerLogoPath ?: site_setting('logo_path');
                    $logoSmallPath = site_setting('logo_small_path');
                    $isSvg = $logoPath && strtolower(pathinfo($logoPath, PATHINFO_EXTENSION)) === 'svg';
                    $isSvgSmall = $logoSmallPath && strtolower(pathinfo($logoSmallPath, PATHINFO_EXTENSION)) === 'svg';
                @endphp
                @php
                    $hasLogoSmall = $logoSmallPath && Storage::disk('public')->exists($logoSmallPath);
                @endphp
                @if($logoPath && Storage::disk('public')->exists($logoPath))
                    {{-- Custom uploaded logo --}}
                    <div @click="sidebarCollapsed = !sidebarCollapsed" 
                         class="relative flex items-center justify-center flex-shrink-0 transition-all duration-300 cursor-pointer hover:opacity-80" 
                         :class="sidebarCollapsed ? 'w-10 h-10 md:w-12 md:h-12' : 'w-20 h-20 md:w-32 md:h-32'"
                         :title="sidebarCollapsed ? 'Agrandir le menu' : ''">
                        @if($hasLogoSmall)
                            {{-- Logo petit quand menu réduit --}}
                            <img x-show="sidebarCollapsed" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 src="{{ Storage::url($logoSmallPath) }}" 
                                 alt="Logo petit" 
                                 class="object-contain {{ $isSvgSmall ? '' : 'rounded-xl' }} w-10 h-10 md:w-12 md:h-12">
                            {{-- Logo normal quand menu étendu --}}
                            <img x-show="!sidebarCollapsed" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 src="{{ Storage::url($logoPath) }}" 
                                 alt="Logo" 
                                 class="object-contain {{ $isSvg ? '' : 'rounded-xl' }} w-20 h-20 md:w-32 md:h-32">
                        @else
                            {{-- Utiliser le logo normal dans les deux cas si pas de logo petit --}}
                            @if($isSvg)
                                <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="object-contain transition-all duration-300" :class="sidebarCollapsed ? 'w-10 h-10 md:w-12 md:h-12' : 'w-20 h-20 md:w-32 md:h-32'">
                            @else
                                <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="object-contain rounded-xl transition-all duration-300" :class="sidebarCollapsed ? 'w-10 h-10 md:w-12 md:h-12' : 'w-20 h-20 md:w-32 md:h-32'">
                            @endif
                        @endif
                    </div>
                @else
                    {{-- Default SVG icon --}}
                    <div @click="sidebarCollapsed = !sidebarCollapsed" 
                         class="bg-gray-800 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 transition-all duration-300 cursor-pointer hover:bg-gray-700" 
                         :class="sidebarCollapsed ? 'w-10 h-10 md:w-12 md:h-12' : 'w-20 h-20 md:w-32 md:h-32'"
                         :title="sidebarCollapsed ? 'Agrandir le menu' : ''">
                        <svg class="text-white transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="sidebarCollapsed ? 'w-6 h-6 md:w-8 md:h-8' : 'w-14 h-14 md:w-20 md:h-20'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @endif
                
                <!-- Toggle Button & Close Mobile -->
                <div class="flex items-center space-x-2">
                    <!-- Close Mobile Button -->
                    <button x-show="!sidebarCollapsed" 
                            @click="mobileMenuOpen = false"
                            class="lg:hidden flex items-center justify-center p-2 text-sm font-medium text-white hover:bg-gray-800 rounded-lg transition"
                            title="Fermer le menu">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                    <!-- Toggle Button Desktop -->
                    <button x-show="!sidebarCollapsed" 
                            x-transition
                            @click="sidebarCollapsed = !sidebarCollapsed" 
                            class="hidden lg:flex items-center justify-center p-2 text-sm font-medium text-white hover:bg-gray-800 rounded-lg transition"
                            title="Réduire le menu">
                        <i class="fas fa-chevron-left text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-3 md:p-4 space-y-1 overflow-y-auto" @click="if (window.innerWidth < 1024 && $event.target.closest('a')) mobileMenuOpen = false">
                <div class="mb-4">
                    <p x-show="!sidebarCollapsed" x-transition class="px-3 text-xs font-semibold text-white uppercase tracking-wider mb-2">Menu Principal</p>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Dashboard</span>
                    </a>
                </div>

                <div class="mb-4">
                    <p x-show="!sidebarCollapsed" x-transition class="px-3 text-xs font-semibold text-white uppercase tracking-wider mb-2">Contenu</p>
                    
                    <a href="{{ route('admin.languages.index') }}" 
                       :title="sidebarCollapsed ? 'Langues' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.languages.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.languages.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Langues</span>
                    </a>
                    
                    <!-- Menu Personnalisation (Déroulant) -->
                    <div class="relative">
                        <button @click="personalizationOpen = !personalizationOpen"
                                :title="sidebarCollapsed ? 'Personnalisation' : ''"
                                class="w-full group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.feature-blocks.*') || request()->routeIs('admin.menus.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.feature-blocks.*') || request()->routeIs('admin.menus.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                                <i class="fas fa-palette text-lg text-white"></i>
                            </div>
                            <span x-show="!sidebarCollapsed" x-transition class="flex-1 text-left text-white">Personnalisation</span>
                            <i x-show="!sidebarCollapsed" x-transition :class="personalizationOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs ml-2 text-white"></i>
                        </button>
                        
                        <!-- Sous-menu Personnalisation -->
                        <div x-show="personalizationOpen && !sidebarCollapsed" 
                             x-transition
                             class="ml-4 mt-1 space-y-1 border-l-2 border-gray-700 pl-4">
                            <a href="{{ route('admin.settings.index') }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : 'text-white hover:bg-gray-800' }}">
                                <i class="fas fa-cog w-5 text-center mr-2 text-white"></i>
                                <span class="text-white">Paramètres</span>
                            </a>
                            <a href="{{ route('admin.banners.index') }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.banners.*') ? 'bg-gray-800 text-white' : 'text-white hover:bg-gray-800' }}">
                                <i class="fas fa-images w-5 text-center mr-2 text-white"></i>
                                <span class="text-white">Bannières</span>
                            </a>
                            <a href="{{ route('admin.feature-blocks.index') }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.feature-blocks.*') ? 'bg-gray-800 text-white' : 'text-white hover:bg-gray-800' }}">
                                <i class="fas fa-th-large w-5 text-center mr-2 text-white"></i>
                                <span class="text-white">Blocs fonctionnalités</span>
                            </a>
                            <a href="{{ route('admin.menus.index') }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.menus.*') ? 'bg-gray-800 text-white' : 'text-white hover:bg-gray-800' }}">
                                <i class="fas fa-bars w-5 text-center mr-2 text-white"></i>
                                <span class="text-white">Menus</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       :title="sidebarCollapsed ? 'Catégories' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Catégories</span>
                    </a>
                    
                    <a href="{{ route('admin.tours.index') }}" 
                       :title="sidebarCollapsed ? 'Tours' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.tours.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.tours.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Tours</span>
                    </a>

                    <a href="{{ route('admin.faqs.index') }}" 
                       :title="sidebarCollapsed ? 'FAQs' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.faqs.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.faqs.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-question-circle text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">FAQs</span>
                    </a>
                    
                    <a href="{{ route('admin.pages.index') }}" 
                       :title="sidebarCollapsed ? 'Pages' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.pages.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Pages</span>
                    </a>

                    <a href="{{ route('admin.blog-posts.index') }}" 
                       :title="sidebarCollapsed ? 'Blog' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.blog-posts.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.blog-posts.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-blog text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Blog</span>
                    </a>

                    <a href="{{ route('admin.guides.index') }}" 
                       :title="sidebarCollapsed ? 'Guides' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.guides.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.guides.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-map-signs text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Guides SEO</span>
                    </a>
                </div>

                <div class="mb-4">
                    <p x-show="!sidebarCollapsed" x-transition class="px-3 text-xs font-semibold text-white uppercase tracking-wider mb-2">Gestion</p>
                    
                    <a href="{{ route('admin.bookings.index') }}" 
                       :title="sidebarCollapsed ? 'Réservations' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Réservations</span>
                    </a>

                    <a href="{{ route('admin.clients.index') }}" 
                       :title="sidebarCollapsed ? 'Clients' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.clients.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.clients.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-user-friends text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Clients</span>
                    </a>
                    
                    <a href="{{ route('admin.reviews.index') }}" 
                       :title="sidebarCollapsed ? 'Avis' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.reviews.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.reviews.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-star text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Avis</span>
                    </a>
                    
                    <a href="{{ route('admin.currencies.index') }}" 
                       :title="sidebarCollapsed ? 'Devises' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.currencies.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.currencies.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Devises</span>
                    </a>
                    
                    <a href="{{ route('admin.accommodations.index') }}" 
                       :title="sidebarCollapsed ? 'Hébergements' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.accommodations.*') || request()->routeIs('admin.tour-pricings.accommodations*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.accommodations.*') || request()->routeIs('admin.tour-pricings.accommodations*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-hotel text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Hébergements</span>
                    </a>
                    
                    <a href="{{ route('admin.addons.index') }}" 
                       :title="sidebarCollapsed ? 'Add-ons' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.addons.*') || request()->routeIs('admin.tours.addons*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.addons.*') || request()->routeIs('admin.tours.addons*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-plus-circle text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Add-ons</span>
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" 
                       :title="sidebarCollapsed ? 'Utilisateurs' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Utilisateurs</span>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}" 
                       :title="sidebarCollapsed ? 'Journal d\'audit' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.audit-logs.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Journal d'audit</span>
                    </a>

                    <a href="{{ route('admin.marketing.index') }}" 
                       :title="sidebarCollapsed ? 'Marketing' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.marketing.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.marketing.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Marketing</span>
                    </a>

                    <a href="{{ route('admin.channels.index') }}" 
                       :title="sidebarCollapsed ? 'Canaux OTA' : ''"
                       class="group flex items-center text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.channels.*') ? 'bg-gray-800 text-white shadow-sm' : 'text-white hover:bg-gray-800' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'px-3 py-2.5'">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0 {{ request()->routeIs('admin.channels.*') ? 'bg-gray-700' : 'bg-gray-800 group-hover:bg-gray-700' }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fas fa-store text-white"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-transition class="text-white">Canaux OTA</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Bar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 lg:px-8 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div>
                        <h1 class="text-base md:text-lg font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="lg:hidden p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition"
                            title="Ouvrir le menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Desktop Menu Items -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </button>
                        
                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center space-x-2 p-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-semibold">{{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}</span>
                            </div>
                            <div class="text-left hidden lg:block">
                                <p class="text-sm font-medium text-gray-900">{{ auth('admin')->user()->name }}</p>
                                <p class="text-xs text-gray-500">Administrateur</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                            <a href="{{ route('home') }}" target="_blank"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Voir le site
                            </a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-100">
                <!-- Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-green-800">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-red-800">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>

