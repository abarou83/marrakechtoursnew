<nav x-data="{ 
        open: false, 
        userMenuOpen: false
    }" 
     class="fixed top-0 left-0 w-full z-50 bg-white shadow-md">
    @php
        // Récupérer le menu actif (premier menu actif par position)
        $activeMenu = \App\Models\Menu::where('is_active', true)
            ->where('location', 'header')
            ->orderBy('position')
            ->with(['activeItems.category', 'activeItems.page', 'activeItems.tour', 'activeItems.translations'])
            ->first();
        
        // Fallback vers les catégories si aucun menu n'est configuré
        $navCategories = $activeMenu 
            ? $activeMenu->activeItems 
            : \App\Models\Category::with('translations')->orderBy('name')->get();
        
        // Session client (frontend)
        $isClient = Auth::guard('client')->check();
        $currentUser = $isClient ? Auth::guard('client')->user() : null;

        // Panier session
        $cartCount = count(session('cart', []));
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Left Section: Logo (Desktop) / Menu (Mobile) -->
            <div class="flex items-center flex-shrink-0">
                <!-- Logo Desktop (visible on desktop, hidden on mobile/tablet) -->
                <a href="{{ route('home') }}" class="hidden nav:flex nav:items-center py-2">
                    <x-application-logo class="block h-14 md:h-16 w-auto" />
                </a>

                <!-- Hamburger Menu (Mobile and Tablet) -->
                <div class="nav:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Center Section: Categories (Desktop) / Logo (Mobile) -->
            <div class="flex items-center justify-center flex-1">
                <!-- Logo Mobile (centered, hidden on desktop) -->
                <div class="nav:hidden">
                    <a href="{{ route('home') }}" class="flex items-center py-2">
                        <x-application-logo class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Menu Items (Desktop only) -->
                <div class="hidden nav:flex items-center space-x-1">
                    @if($activeMenu)
                        @foreach($navCategories as $item)
                            @if($item->is_active)
                                <a href="{{ $item->getUrl() }}"
                                   class="px-5 py-2.5 rounded-full text-base font-semibold text-secondary hover:text-secondary/80 hover:bg-gray-50 transition duration-150 ease-in-out">
                                    @if($item->icon)
                                        <i class="{{ $item->icon }} mr-2"></i>
                                    @endif
                                    {{ $item->getDisplayLabel() }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        {{-- Fallback: Afficher les catégories si aucun menu n'est configuré --}}
                        @foreach($navCategories as $category)
                            <a href="{{ route('category.show', $category->url_key) }}"
                               class="px-5 py-2.5 rounded-full text-base font-semibold text-secondary hover:text-secondary/80 hover:bg-gray-50 transition duration-150 ease-in-out">
                                {{ translate_model($category, 'name') }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Right Section: Language + Auth -->
            <div class="flex items-center justify-end space-x-1">
                <!-- Language Selector (Desktop only) -->
                <div class="hidden nav:flex items-center">
                    <x-language-selector />
                </div>
                <!-- Currency Selector (Desktop only) -->
                <div class="hidden nav:flex items-center">
                    <x-currency-selector />
                </div>

                <!-- Cart Icon (visible when cart has items) -->
                @if($cartCount > 0)
                    <div class="flex items-center">
                        <x-cart-icon :count="$cartCount" />
                    </div>
                @endif
                
                <!-- User Icon (Mobile and Tablet) -->
                <div class="nav:hidden">
                    @if($isClient && $currentUser)
                        <!-- Client connecté : ouvre le menu -->
                        <button @click="userMenuOpen = !userMenuOpen" class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    @else
                        <!-- Non connecté : ouvre la modal de connexion -->
                        <button @click="$store.loginModal.openModal()" class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    @endif
                </div>
                
                <!-- Desktop: User Menu -->
                <div class="hidden nav:block nav:ml-2">
                    @if($isClient && $currentUser)
                        <!-- Client connecté : affiche le menu déroulant -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    type="button" 
                                    class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary transition ease-in-out duration-150">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                            
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <!-- Menu pour les clients -->
                                    <a href="{{ route('dashboard') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-calendar-check mr-2 text-gray-400"></i>
                                        {{ __('My Bookings') }}
                                    </a>
                                    <a href="{{ route('profile.edit') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                                        {{ __('Profile') }}
                                    </a>
                                    
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('client.logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i>
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Non connecté : ouvre la modal de connexion -->
                        <button @click="$store.loginModal.openModal()" 
                                type="button" 
                                class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile and Tablet) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden nav:hidden bg-white shadow-lg border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <!-- Menu Items Links (Mobile/Tablet) -->
            @if($activeMenu)
                @foreach($navCategories as $item)
                    @if($item->is_active)
                        <a href="{{ $item->getUrl() }}"
                           class="block px-3 py-2 text-base font-medium text-secondary hover:text-secondary/80 hover:bg-gray-50 rounded-md transition duration-150 ease-in-out"
                           @click="open = false">
                            @if($item->icon)
                                <i class="{{ $item->icon }} mr-2"></i>
                            @endif
                            {{ $item->getDisplayLabel() }}
                        </a>
                    @endif
                @endforeach
            @else
                {{-- Fallback: Afficher les catégories si aucun menu n'est configuré --}}
                @foreach($navCategories as $category)
                    <a href="{{ route('category.show', $category->url_key) }}"
                       class="block px-3 py-2 text-base font-medium text-secondary hover:text-secondary/80 hover:bg-gray-50 rounded-md transition duration-150 ease-in-out"
                       @click="open = false">
                        {{ translate_model($category, 'name') }}
                    </a>
                @endforeach
            @endif
        </div>

        <!-- Responsive Settings Options -->
        @if($isClient && $currentUser)
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 mb-3">
                    <div class="font-medium text-base text-gray-800">{{ $currentUser->name ?? '' }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ $currentUser->email ?? '' }}</div>
                </div>

                <div class="space-y-1 px-4">
                    <!-- Menu pour les clients -->
                    <a href="{{ route('dashboard') }}" 
                       class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition duration-150 ease-in-out"
                       @click="open = false">
                        {{ __('My Bookings') }}
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition duration-150 ease-in-out"
                       @click="open = false">
                        {{ __('Profile') }}
                    </a>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition duration-150 ease-in-out">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- User Menu Overlay (Mobile and Tablet) -->
    <div x-show="userMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="userMenuOpen = false"
         class="nav:hidden fixed inset-0 z-50 bg-black/50"
         style="display: none;">
        <!-- Menu Sidebar -->
        <div x-show="userMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed right-0 top-0 h-full w-80 max-w-[85vw] bg-white shadow-2xl overflow-y-auto"
             @click.stop>
            <!-- Header with Close Button -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Menu') }}</h2>
                <button @click="userMenuOpen = false" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Menu Content -->
            <div class="p-4 space-y-4">
                @if($isClient && $currentUser)
                    <!-- User Info -->
                    <div class="pb-4 border-b border-gray-200">
                        <div class="font-medium text-base text-gray-900">{{ $currentUser->name ?? '' }}</div>
                        <div class="text-sm text-gray-500">{{ $currentUser->email ?? '' }}</div>
                    </div>

                    <!-- User Menu Items -->
                    <div class="space-y-2">
                        <!-- Menu pour les clients -->
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition"
                           @click="userMenuOpen = false">
                            <i class="fas fa-calendar-check w-5 mr-3 text-gray-400"></i>
                            {{ __('My Bookings') }}
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition"
                           @click="userMenuOpen = false">
                            <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
                            {{ __('Profile') }}
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('client.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
                                    @click="userMenuOpen = false">
                                <i class="fas fa-sign-out-alt w-5 mr-3 text-gray-400"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Login/Sign Up Button - Ouvre la modal -->
                    <div class="pb-4 border-b border-gray-200">
                        <button @click="userMenuOpen = false; $store.loginModal.openModal()" 
                                type="button"
                                class="block w-full text-center px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-opacity-90 transition shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            {{ __('Login / Register') }}
                        </button>
                    </div>
                @endif
            </div>

                <!-- Currency Selector -->
                <div class="pt-2 border-t border-gray-200">
                    <div class="px-4 py-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Currency') }}</label>
                        @php
                            $currencies = \App\Models\Currency::where('is_active', true)
                                ->orderByDesc('is_default')->orderBy('code')->get();
                            $currentCurrency = \App\Helpers\CurrencyHelper::current();
                            $currentCurrencyCode = $currentCurrency?->code;
                        @endphp
                        <select 
                            onchange="const u = new URL(window.location.href); u.searchParams.set('currency', this.value); window.location.href = u.toString()"
                            class="w-full px-3 py-2 text-sm border-0 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}" {{ $currentCurrencyCode === $currency->code ? 'selected' : '' }}>
                                    {{ strtoupper($currency->code) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Language Selector -->
                <div class="pt-2 border-t border-gray-200">
                    <div class="px-4 py-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Language') }}</label>
                        @php
                            $currentLocale = app()->getLocale();
                            $languages = \App\Models\Language::active()->orderBy('is_default', 'desc')->orderBy('name')->get();
                            
                            
                            $currentLanguage = $languages->firstWhere('code', $currentLocale);
                            $currentLanguageName = $currentLanguage ? ($currentLanguage->native_name ?? $currentLanguage->name ?? strtoupper($currentLanguage->code)) : '';
                        @endphp
                        <div x-data="{ open: false, selected: '{{ $currentLocale }}' }" class="relative">
                            <button @click="open = !open" 
                                    type="button"
                                    class="w-full px-3 py-2 text-sm border-0 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden mr-2 flex-shrink-0">
                                        <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($currentLocale) }} fis" style="font-size: 1.25rem;"></span>
                                    </span>
                                    <span>{{ $currentLanguageName }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 ml-2"></i>
                            </button>
                            
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-auto"
                                 style="display: none;">
                                @foreach($languages as $language)
                                    @php
                                        $languageName = $language->native_name ?? $language->name ?? strtoupper($language->code);
                                    @endphp
                                    <button type="button"
                                            @click="selected = '{{ $language->code }}'; open = false; window.location.href = '{{ request()->fullUrlWithQuery(['lang' => $language->code]) }}'"
                                            class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ $currentLocale === $language->code ? 'bg-gray-50 font-semibold' : '' }}">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden mr-2 flex-shrink-0">
                                            <span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($language->code) }} fis" style="font-size: 1.25rem;"></span>
                                        </span>
                                        <span>{{ $languageName }}</span>
                                        @if($currentLocale === $language->code)
                                            <i class="fas fa-check text-primary ml-auto"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
