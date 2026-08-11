@php
    use App\Models\Menu;

    $footerCategories = \App\Models\Category::with('translations')->orderBy('name')->take(5)->get();
    $year = date('Y');
    $appName = site_setting('company_name', config('app.name', 'Marrakech Tours'));
    $footerLogoPath = site_setting('footer_logo_path');

    $footerMenus = Menu::activeForLocation(Menu::LOCATION_FOOTER)->get();
    $footerBottomMenu = Menu::activeForLocation(Menu::LOCATION_FOOTER_BOTTOM)->first();

    $companyEmail = site_setting('company_email', 'contact@example.com');
    $companyPhone = site_setting('company_phone', '+212 6 00 00 00 00');
    $companyAddress = site_setting('company_address', 'Marrakech, Maroc');

    $socialLinks = array_filter([
        'facebook' => site_setting('social_facebook'),
        'instagram' => site_setting('social_instagram'),
        'twitter' => site_setting('social_twitter'),
        'youtube' => site_setting('social_youtube'),
        'linkedin' => site_setting('social_linkedin'),
        'tiktok' => site_setting('social_tiktok'),
    ]);

    $socialIcons = [
        'facebook' => 'fab fa-facebook-f',
        'instagram' => 'fab fa-instagram',
        'twitter' => 'fab fa-x-twitter',
        'youtube' => 'fab fa-youtube',
        'linkedin' => 'fab fa-linkedin-in',
        'tiktok' => 'fab fa-tiktok',
    ];

    $footerMenuPrimary = $footerMenus->first();
    $footerMenuSecondary = $footerMenus->skip(1)->first();
@endphp

<footer id="footer" class="site-footer on-dark mt-auto" role="contentinfo">
    <div class="site-footer__main">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8 lg:items-start">

                {{-- Marque --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('home') }}" class="inline-block mb-3">
                        @if($footerLogoPath)
                            <img src="{{ public_storage_url($footerLogoPath) }}" alt="{{ $appName }}" class="h-10 w-auto" width="140" height="40" loading="lazy" decoding="async">
                        @else
                            <x-application-logo class="h-10 w-auto" />
                        @endif
                    </a>
                    <p class="text-sm text-white/55 leading-relaxed">
                        {{ __('Explore unique experiences and create unforgettable memories.') }}
                    </p>
                    @if(! empty($socialLinks))
                        <div class="flex flex-wrap items-center gap-1.5 mt-4" aria-label="{{ __('Social networks') }}">
                            @foreach($socialLinks as $network => $url)
                                <a href="{{ $url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   aria-label="{{ ucfirst($network) }}"
                                   class="site-footer__social">
                                    <i class="{{ $socialIcons[$network] ?? 'fas fa-link' }}" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Navigation --}}
                <div>
                    @if($footerMenuPrimary)
                        <h3 class="footer-heading">{{ $footerMenuPrimary->getDisplayName() }}</h3>
                        <ul class="footer-link-list">
                            @foreach($footerMenuPrimary->activeItems as $item)
                                <li><a href="{{ $item->getUrl() }}" class="footer-link">{{ $item->getDisplayLabel() }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <h3 class="footer-heading">{{ __('Navigation') }}</h3>
                        <nav aria-label="{{ __('Footer navigation') }}">
                            <ul class="footer-link-list">
                                <li><a href="{{ route('home') }}" class="footer-link">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('tours.index') }}" class="footer-link">{{ __('All Tours') }}</a></li>
                                <li><a href="{{ route('guides.index') }}" class="footer-link">{{ __('Guides') }}</a></li>
                                <li><a href="{{ route('blog.index') }}" class="footer-link">{{ __('Blog') }}</a></li>
                                <li><a href="{{ route('contact') }}" class="footer-link">{{ __('Contact us') }}</a></li>
                                @auth('client')
                                    <li><a href="{{ route('dashboard.index') }}" class="footer-link">{{ __('My Bookings') }}</a></li>
                                @endauth
                            </ul>
                        </nav>
                    @endif
                </div>

                {{-- Catégories --}}
                <div>
                    @if($footerMenuSecondary)
                        <h3 class="footer-heading">{{ $footerMenuSecondary->getDisplayName() }}</h3>
                        <ul class="footer-link-list">
                            @foreach($footerMenuSecondary->activeItems as $item)
                                <li><a href="{{ $item->getUrl() }}" class="footer-link">{{ $item->getDisplayLabel() }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <h3 class="footer-heading">{{ __('Categories') }}</h3>
                        <ul class="footer-link-list">
                            @forelse($footerCategories as $cat)
                                <li>
                                    <a href="{{ route('category.show', $cat->url_key) }}" class="footer-link">
                                        {{ translate_model($cat, 'name') }}
                                    </a>
                                </li>
                            @empty
                                <li class="text-white/40 text-sm">{{ __('No categories yet') }}</li>
                            @endforelse
                            @if($footerCategories->isNotEmpty())
                                <li class="pt-0.5">
                                    <a href="{{ route('tours.index') }}" class="text-primary-300/90 hover:text-white text-sm font-medium transition-colors">
                                        {{ __('View all tours') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="footer-heading">{{ __('Contact') }}</h3>
                    <ul class="footer-link-list space-y-3">
                        @if($companyAddress)
                            <li class="flex gap-2.5 text-sm text-white/65">
                                <i class="fas fa-location-dot mt-0.5 text-primary-300/90 text-xs shrink-0" aria-hidden="true"></i>
                                <span>{{ $companyAddress }}</span>
                            </li>
                        @endif
                        @if($companyEmail)
                            <li class="flex gap-2.5 text-sm">
                                <i class="fas fa-envelope mt-0.5 text-primary-300/90 text-xs shrink-0" aria-hidden="true"></i>
                                <a href="mailto:{{ $companyEmail }}" class="footer-link break-all">{{ $companyEmail }}</a>
                            </li>
                        @endif
                        @if($companyPhone)
                            <li class="flex gap-2.5 text-sm">
                                <i class="fas fa-phone mt-0.5 text-primary-300/90 text-xs shrink-0" aria-hidden="true"></i>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $companyPhone) }}" class="footer-link">{{ $companyPhone }}</a>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- Newsletter (même colonne que les autres blocs) --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <h3 class="footer-heading">{{ __('Newsletter') }}</h3>
                    <p class="text-sm text-white/55 leading-snug mb-3">
                        {{ __('Recevez nos offres et inspirations voyage.') }}
                    </p>
                    @livewire('newsletter-subscribe', ['compact' => true])
                </div>
            </div>
        </div>
    </div>

    {{-- Barre inférieure --}}
    <div class="site-footer__bar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-white/45 text-center lg:text-start order-2 lg:order-1">
                    &copy; {{ $year }} {{ $appName }}. {{ __('All rights reserved.') }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 order-1 lg:order-2">
                    <div class="flex items-center gap-2.5 text-white/40 text-lg" aria-label="{{ __('Paiements sécurisés') }}">
                        <i class="fab fa-cc-visa" title="Visa"></i>
                        <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                        <i class="fab fa-cc-amex" title="Amex"></i>
                        <i class="fab fa-cc-paypal" title="PayPal"></i>
                    </div>
                    @if($footerBottomMenu && $footerBottomMenu->activeItems->isNotEmpty())
                        <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm" aria-label="{{ __('Legal links') }}">
                            @foreach($footerBottomMenu->activeItems as $item)
                                <a href="{{ $item->getUrl() }}" class="footer-link-muted">{{ $item->getDisplayLabel() }}</a>
                            @endforeach
                        </nav>
                    @else
                        <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm" aria-label="{{ __('Legal links') }}">
                            <a href="{{ route('privacy') }}" class="footer-link-muted">{{ __('Privacy Policy') }}</a>
                            <a href="{{ route('pages.show', 'mentions-legales') }}" class="footer-link-muted">{{ __('Legal notice') }}</a>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
