<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\TourController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\GuideController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\TourDateController;
use App\Http\Controllers\Admin\TourPricingController;
use App\Http\Controllers\Admin\TourPromotionController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\FeatureBlockController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Frontend\GdprController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\GiftCardController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\GuideController as AdminGuideController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/health', \App\Http\Controllers\HealthController::class)->name('health');

// Stripe Webhook (no CSRF)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware(['web', \App\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.stripe');

// PayPal Webhook (no CSRF)
Route::post('/webhooks/paypal', [\App\Http\Controllers\PayPalWebhookController::class, 'handle'])
    ->withoutMiddleware(['web', \App\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.paypal');

// Maintenance Preview (accessible aux admins connectés)
Route::get('/maintenance-preview', function () {
    return view('maintenance');
})->name('maintenance.preview')->middleware('auth.admin');

// Frontend Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Category listing
Route::get('/categories/{slug}', [HomeController::class, 'category'])->name('category.show');

// Tour pages
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/location/{location}', [TourController::class, 'byLocation'])->name('tours.location');
Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show');
Route::get('/tours/{tour}/select-formula', [TourController::class, 'selectFormula'])->name('tours.select-formula');
Route::get('/tours/{tour}/booking', [TourController::class, 'booking'])->name('tours.booking');
Route::get('/tours/{tour}/reserver', [TourController::class, 'bookingWizard'])->name('tours.booking.wizard');

// Booking routes
Route::get('/tours/{tour}/book', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/confirmation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/voucher/{reference}', [BookingController::class, 'voucher'])->name('booking.voucher');
Route::get('/booking/payment/success/{reference}', [\App\Http\Controllers\Frontend\BookingPaymentController::class, 'success'])->name('booking.payment.success');
Route::get('/booking/payment/cancel/{reference}', [\App\Http\Controllers\Frontend\BookingPaymentController::class, 'cancel'])->name('booking.payment.cancel');
Route::middleware(['auth:client'])->group(function () {
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
});

// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\CartController::class, 'index'])->name('index');
    Route::post('/add/{tour}', [\App\Http\Controllers\Frontend\CartController::class, 'add'])->name('add');
    Route::delete('/remove/{itemId}', [\App\Http\Controllers\Frontend\CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [\App\Http\Controllers\Frontend\CartController::class, 'clear'])->name('clear');
    Route::put('/update/{itemId}', [\App\Http\Controllers\Frontend\CartController::class, 'update'])->name('update');
    Route::get('/count', [\App\Http\Controllers\Frontend\CartController::class, 'count'])->name('count');
    Route::get('/checkout', [\App\Http\Controllers\Frontend\CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\Frontend\CartController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/confirmation', [\App\Http\Controllers\Frontend\CartController::class, 'confirmation'])->name('confirmation');
});

// Client Login/Register
Route::post('/client/login', [\App\Http\Controllers\Auth\ClientLoginController::class, 'store'])->name('client.login')->middleware('throttle:login');
Route::post('/client/register', [\App\Http\Controllers\Auth\ClientRegisterController::class, 'store'])->name('client.register')->middleware('throttle:register');
Route::post('/client/logout', [\App\Http\Controllers\Auth\ClientLoginController::class, 'destroy'])->name('client.logout');

// Google OAuth (à implémenter avec Laravel Socialite)
Route::get('/client/google/redirect', [\App\Http\Controllers\Auth\ClientGoogleController::class, 'redirect'])->name('client.google.redirect');
Route::get('/client/google/callback', [\App\Http\Controllers\Auth\ClientGoogleController::class, 'callback'])->name('client.google.callback');

// Pages statiques
Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('pages.show');

// Landing pages SEO
Route::get('/destination/{slug}', [\App\Http\Controllers\Frontend\LandingPageController::class, 'destination'])->name('landing.destination');
Route::get('/activite/{slug}', [\App\Http\Controllers\Frontend\LandingPageController::class, 'category'])->name('landing.category');
Route::get('/decouvrir/{slug}', [\App\Http\Controllers\Frontend\LandingPageController::class, 'show'])->name('landing.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact');

// RGPD / Privacy
Route::get('/politique-confidentialite', [GdprController::class, 'privacy'])->name('privacy');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe')->middleware('throttle:register');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Cartes cadeaux
Route::get('/cartes-cadeaux', [GiftCardController::class, 'index'])->name('gift-cards.index');
Route::get('/cartes-cadeaux/confirmation/{code}', [GiftCardController::class, 'confirmation'])->name('gift-cards.confirmation');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Guides SEO
Route::get('/guide', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guide/{slug}', [GuideController::class, 'show'])->name('guides.show');

// UI Examples (pour développement/demo)
Route::get('/ui-examples', [\App\Http\Controllers\Frontend\UIExampleController::class, 'index'])->name('ui-examples');

// Dashboard client (réservé aux clients connectés)
Route::middleware(['auth:client'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\DashboardController::class, 'index'])->name('index');
    Route::get('/bookings', [\App\Http\Controllers\Frontend\DashboardController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Frontend\DashboardController::class, 'bookingShow'])->name('bookings.show');
    Route::get('/wishlist', [\App\Http\Controllers\Frontend\DashboardController::class, 'wishlist'])->name('wishlist');
    Route::get('/reviews', [\App\Http\Controllers\Frontend\DashboardController::class, 'reviews'])->name('reviews');
    Route::get('/profile', [\App\Http\Controllers\Frontend\DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Frontend\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [\App\Http\Controllers\Frontend\DashboardController::class, 'updatePassword'])->name('password.update');
    Route::get('/notifications', [\App\Http\Controllers\Frontend\DashboardController::class, 'notifications'])->name('notifications');
    Route::put('/notifications', [\App\Http\Controllers\Frontend\DashboardController::class, 'updateNotifications'])->name('notifications.update');
    Route::get('/parrainage', [\App\Http\Controllers\Frontend\DashboardController::class, 'referral'])->name('referral');
    
    // Reviews
    Route::get('/reviews/create/{booking}', [\App\Http\Controllers\Frontend\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews/{booking}', [\App\Http\Controllers\Frontend\ReviewController::class, 'store'])->name('reviews.store')->middleware('throttle:review');
    Route::get('/reviews/{review}/edit', [\App\Http\Controllers\Frontend\ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [\App\Http\Controllers\Frontend\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Frontend\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Client GDPR Routes (Rate Limited)
Route::middleware(['auth:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('dashboard.index'))->name('dashboard');
    Route::get('/notifications', fn() => redirect()->route('dashboard.notifications'))->name('notifications');
    Route::get('/gdpr/export', [GdprController::class, 'exportRequest'])->name('gdpr.export.request');
    Route::post('/gdpr/export', [GdprController::class, 'export'])->name('gdpr.export')->middleware('throttle:export');
    Route::get('/gdpr/delete', [GdprController::class, 'deleteRequest'])->name('gdpr.delete.request');
    Route::delete('/gdpr/delete', [GdprController::class, 'delete'])->name('gdpr.delete');
});

// API Wishlist (toggle public — auth gérée dans le contrôleur pour UI optimiste)
Route::prefix('api')->group(function () {
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Api\WishlistController::class, 'toggle']);
    Route::middleware(['auth:client'])->group(function () {
        Route::get('/wishlist', [\App\Http\Controllers\Api\WishlistController::class, 'index']);
        Route::post('/wishlist/sync', [\App\Http\Controllers\Api\WishlistController::class, 'sync']);
        Route::post('/wishlist/remove', [\App\Http\Controllers\Api\WishlistController::class, 'remove']);
    });
});

// Profile routes (pour les clients)
Route::middleware('auth:client')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});

// Admin Login (accessible uniquement si admin non connecté)
Route::middleware('guest.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->name('login.post')->middleware('throttle:login');
});

// Admin Logout (accessible uniquement aux admins connectés)
Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\LogoutController::class, 'destroy'])->name('logout');
});

// Admin Routes - Utiliser le guard 'admin' spécifiquement
Route::middleware(['auth.admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Tours
    Route::resource('tours', AdminTourController::class);
    Route::post('/tours/import-example', [AdminTourController::class, 'importExample'])->name('tours.import-example');
    Route::post('/tours/import-json', [AdminTourController::class, 'importJson'])->name('tours.import-json');
    Route::delete('/tours/images/{image}', [AdminTourController::class, 'deleteImage'])->name('tours.images.delete');
    Route::post('/tours/images/{image}/set-primary', [AdminTourController::class, 'setPrimaryImage'])->name('tours.images.set-primary');
    
    // Tour Dates
    Route::get('/tours/{tour}/dates', [TourDateController::class, 'index'])->name('tour-dates.index');
    Route::get('/tours/{tour}/dates/create', [TourDateController::class, 'create'])->name('tour-dates.create');
    Route::post('/tours/{tour}/dates', [TourDateController::class, 'store'])->name('tour-dates.store');
    Route::get('/tours/{tour}/dates/{tourDate}/edit', [TourDateController::class, 'edit'])->name('tour-dates.edit');
    Route::put('/tours/{tour}/dates/{tourDate}', [TourDateController::class, 'update'])->name('tour-dates.update');
    Route::delete('/tours/{tour}/dates/{tourDate}', [TourDateController::class, 'destroy'])->name('tour-dates.destroy');
    
    // Tour Pricings
    Route::get('/tours/{tour}/pricings', [TourPricingController::class, 'index'])->name('tour-pricings.index');
    Route::get('/tours/{tour}/pricings/create', [TourPricingController::class, 'create'])->name('tour-pricings.create');
    Route::post('/tours/{tour}/pricings', [TourPricingController::class, 'store'])->name('tour-pricings.store');
    Route::get('/tours/{tour}/pricings/{pricing}/edit', [TourPricingController::class, 'edit'])->name('tour-pricings.edit');
    Route::put('/tours/{tour}/pricings/{pricing}', [TourPricingController::class, 'update'])->name('tour-pricings.update');
    Route::delete('/tours/{tour}/pricings/{pricing}', [TourPricingController::class, 'destroy'])->name('tour-pricings.destroy');
    
    // Addons
    Route::resource('addons', AddonController::class);
    Route::get('/tours/{tour}/addons', [AddonController::class, 'manageTourAddons'])->name('tours.addons');
    Route::post('/tours/{tour}/addons/attach', [AddonController::class, 'attachToTour'])->name('tours.addons.attach');
    Route::delete('/tours/{tour}/addons/{addon}', [AddonController::class, 'detachFromTour'])->name('tours.addons.detach');
    
    // Tour Promotions
    Route::get('/tours/{tour}/promotions', [TourPromotionController::class, 'index'])->name('tour-promotions.index');
    Route::get('/tours/{tour}/promotions/create', [TourPromotionController::class, 'create'])->name('tour-promotions.create');
    Route::post('/tours/{tour}/promotions', [TourPromotionController::class, 'store'])->name('tour-promotions.store');
    Route::get('/tours/{tour}/promotions/{promotion}/edit', [TourPromotionController::class, 'edit'])->name('tour-promotions.edit');
    Route::put('/tours/{tour}/promotions/{promotion}', [TourPromotionController::class, 'update'])->name('tour-promotions.update');
    Route::delete('/tours/{tour}/promotions/{promotion}', [TourPromotionController::class, 'destroy'])->name('tour-promotions.destroy');
    
    // Accommodations
    Route::resource('accommodations', \App\Http\Controllers\Admin\AccommodationController::class);
    Route::get('/tour-pricings/{tourPricing}/accommodations', [\App\Http\Controllers\Admin\AccommodationController::class, 'managePricingAccommodations'])->name('tour-pricings.accommodations');
    Route::post('/tour-pricings/{tourPricing}/accommodations/attach', [\App\Http\Controllers\Admin\AccommodationController::class, 'attachToPricing'])->name('tour-pricings.accommodations.attach');
    Route::delete('/tour-pricings/{tourPricing}/accommodations/{accommodation}', [\App\Http\Controllers\Admin\AccommodationController::class, 'detachFromPricing'])->name('tour-pricings.accommodations.detach');
    Route::put('/tour-pricings/{tourPricing}/accommodations/{accommodation}', [\App\Http\Controllers\Admin\AccommodationController::class, 'updatePricingAccommodation'])->name('tour-pricings.accommodations.update');
    
    // Bookings
    Route::resource('bookings', AdminBookingController::class);
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::patch('/bookings/{booking}/channel', [AdminBookingController::class, 'updateChannel'])->name('bookings.updateChannel');
    Route::post('/bookings/{booking}/refund', [AdminBookingController::class, 'refund'])->name('bookings.refund');
    Route::get('/bookings-export', [AdminBookingController::class, 'export'])->name('bookings.export');
    
    // Clients (comptes frontend)
    Route::resource('clients', ClientController::class)->only(['index', 'show', 'destroy']);

    // Users (admins)
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');
    
    // Translations (only for categories, tours translations are now in edit/create forms)
    Route::get('/categories/{category}/translations', [TranslationController::class, 'editCategory'])->name('categories.translations');
    Route::post('/categories/{category}/translations', [TranslationController::class, 'updateCategory'])->name('categories.translations.update');
    
    // Addon Translations
    Route::get('/addons/{addon}/translations', [TranslationController::class, 'editAddon'])->name('addons.translations');
    Route::post('/addons/{addon}/translations', [TranslationController::class, 'updateAddon'])->name('addons.translations.update');
    
    // Accommodation Translations
    Route::get('/accommodations/{accommodation}/translations', [TranslationController::class, 'editAccommodation'])->name('accommodations.translations');
    Route::post('/accommodations/{accommodation}/translations', [TranslationController::class, 'updateAccommodation'])->name('accommodations.translations.update');
    
    // Tour Pricing Translations
    Route::get('/tour-pricings/{pricing}/translations', [TranslationController::class, 'editTourPricing'])->name('tour-pricings.translations');
    Route::post('/tour-pricings/{pricing}/translations', [TranslationController::class, 'updateTourPricing'])->name('tour-pricings.translations.update');
    
    // Languages Management
    Route::resource('languages', LanguageController::class);
    Route::post('/languages/{language}/toggle-active', [LanguageController::class, 'toggleActive'])->name('languages.toggle-active');
    Route::post('/languages/{language}/set-default', [LanguageController::class, 'setDefault'])->name('languages.set-default');
    
    // Site Settings (Colors)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Quotes (désactivé)

    // Currencies
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::get('/currencies/create', [CurrencyController::class, 'create'])->name('currencies.create');
    Route::post('/currencies', [CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('/currencies/{currency}/edit', [CurrencyController::class, 'edit'])->name('currencies.edit');
    Route::put('/currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
    Route::post('/currencies/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('currencies.toggle-active');
    Route::post('/currencies/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('currencies.set-default');

    // Feature Blocks
    Route::resource('feature-blocks', FeatureBlockController::class);
    
    // Reviews (Avis)
    Route::resource('reviews', ReviewController::class);
    Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('/feature-blocks/section-settings', [FeatureBlockController::class, 'updateSectionSettings'])->name('feature-blocks.section-settings');
    Route::post('/feature-blocks/{featureBlock}/toggle-active', [FeatureBlockController::class, 'toggleActive'])->name('feature-blocks.toggle-active');

    // FAQs
    Route::resource('faqs', FAQController::class);
    
    // Pages
    Route::resource('pages', PageController::class);

    // Blog
    Route::get('/blog-posts/import/example/download', [BlogPostController::class, 'downloadExample'])->name('blog-posts.import.example.download');
    Route::post('/blog-posts/import/example', [BlogPostController::class, 'importExample'])->name('blog-posts.import.example');
    Route::post('/blog-posts/import/json', [BlogPostController::class, 'importJson'])->name('blog-posts.import.json');
    Route::resource('blog-posts', BlogPostController::class)->except(['show']);

    // Guides SEO
    Route::resource('guides', AdminGuideController::class)->except(['show']);

    // Canaux OTA
    Route::get('/channels', [ChannelController::class, 'index'])->name('channels.index');
    Route::get('/channels/create', [ChannelController::class, 'create'])->name('channels.create');
    Route::post('/channels', [ChannelController::class, 'store'])->name('channels.store');
    
    // Menus
    Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
    Route::post('/menus/{menu}/toggle-active', [\App\Http\Controllers\Admin\MenuController::class, 'toggleActive'])->name('menus.toggle-active');
    
    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // Marketing
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
});

require __DIR__.'/auth.php';
