<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;
use App\Services\GooglePlaceReviewsService;
use App\Services\SeoService;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function index()
    {
        $query = Tour::where('status', 'published')
            ->with(['categories', 'category', 'images', 'primaryImage', 'translations', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);
        
        // Filters
        if (request('category')) {
            $categoryParam = request('category');
            $category = is_numeric($categoryParam)
                ? Category::find($categoryParam)
                : Category::where('slug', $categoryParam)->first();

            if ($category) {
                // Filter by many-to-many relationship
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });
            }
        }
        
        if (request('location')) {
            $query->where('location', 'like', '%' . request('location') . '%');
        }

        if (request('q')) {
            $search = request('q');
            $locale = app()->getLocale();
            $query->where(function ($q) use ($search, $locale) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($tq) use ($search, $locale) {
                        $tq->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }
        
        // Filtrer par prix via les formules (tour_pricings)
        // Note: price_min is now a computed attribute, so we need to filter differently
        if (request('min_price')) {
            $min = (float) request('min_price');
            $query->whereHas('pricings', function ($q) use ($min) {
                $q->where('is_active', true)
                  ->where('pricing_mode', 'group')
                  ->where('season', 'normal')
                  ->whereHas('groupPrices', function ($subQ) use ($min) {
                      $subQ->where('category', 'adult')
                           ->where('price', '>=', $min);
                  });
            });
        }
        
        if (request('max_price')) {
            $max = (float) request('max_price');
            $query->whereHas('pricings', function ($q) use ($max) {
                $q->where('is_active', true)
                  ->where('pricing_mode', 'group')
                  ->where('season', 'normal')
                  ->whereHas('groupPrices', function ($subQ) use ($max) {
                      $subQ->where('category', 'adult')
                           ->where('price', '<=', $max);
                  });
            });
        }
        
        $tours = $query->latest()->paginate(12);
        $categories = Category::with('translations')->get();
        
        // Generate SEO-friendly title and description based on filters
        $location = request('location');
        $categoryParam = request('category');
        $category = null;
        
        if ($categoryParam) {
            if (is_numeric($categoryParam)) {
                $category = Category::with('translations')->find($categoryParam);
            } else {
                $category = Category::with('translations')->where('slug', $categoryParam)->first();
            }
        }
        
        // Build page title (location takes priority over category for SEO)
        $pageTitle = 'Tours & Activities';
        $metaDescription = 'Discover amazing tours and activities. Book your next adventure today!';
        
        if ($location) {
            $locationName = ucfirst(trim($location));
            $pageTitle = $locationName . ' Tours & Activities';
            
            // If both location and category, include category in description for better SEO
            if ($category) {
                $categoryName = translate_model($category, 'name');
                $metaDescription = 'Explore the best ' . strtolower($categoryName) . ' tours and activities in ' . $locationName . '. Book your adventure today with instant confirmation and free cancellation.';
            } else {
                $metaDescription = 'Explore the best tours and activities in ' . $locationName . '. Book your adventure today with instant confirmation and free cancellation.';
            }
        } elseif ($category) {
            $categoryName = translate_model($category, 'name');
            $pageTitle = $categoryName . ' Tours & Activities';
            $metaDescription = 'Discover the best ' . strtolower($categoryName) . ' tours and activities. Book your next adventure today!';
        }
        
        // Add site name to title
        $fullPageTitle = $pageTitle . ' - ' . config('app.name', 'Tourify');
        
        return view('frontend.tours.index', compact('tours', 'categories', 'pageTitle', 'metaDescription', 'fullPageTitle', 'location', 'category'));
    }

    /**
     * Display tours filtered by location (SEO-friendly URL)
     */
    public function byLocation($location)
    {
        $locationDecoded = urldecode($location);

        $query = Tour::where('status', 'published')
            ->where('location', 'like', '%' . $locationDecoded . '%')
            ->with(['categories', 'category', 'images', 'primaryImage', 'translations', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);

        $tours = $query->latest()->paginate(12);
        $categories = Category::with('translations')->get();

        $locationName = ucfirst(trim($locationDecoded));
        $pageTitle = $locationName . ' Tours & Activities';
        $metaDescription = 'Explore the best tours and activities in ' . $locationName . '. Book your adventure today with instant confirmation and free cancellation.';
        $fullPageTitle = $pageTitle . ' - ' . config('app.name', 'Tourify');
        $category = null;

        return view('frontend.tours.index', compact('tours', 'categories', 'pageTitle', 'metaDescription', 'fullPageTitle', 'category'))->with('location', $locationDecoded);
    }

    public function show($slugOrId)
    {
        $tourQuery = Tour::query()
            ->where('status', 'published')
            ->with(['categories', 'category', 'images', 'primaryImage', 'tourDates', 'reviews.user', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);

        // Supporter slug OU ID numérique
        if (is_numeric($slugOrId)) {
            $tour = $tourQuery->where('id', (int) $slugOrId)->firstOrFail();
        } else {
            $tour = $tourQuery->where('slug', $slugOrId)->firstOrFail();
        }
        
        // Get active promotion
        $activePromotion = $tour->activePromotion();
        
        // Get default pricing
        $defaultPricing = $tour->defaultPricing();
        
        // Get related tours (from any of the tour's categories)
        $relatedTours = Tour::whereHas('categories', function ($q) use ($tour) {
                $q->whereIn('categories.id', $tour->categories->pluck('id'));
            })
            ->where('id', '!=', $tour->id)
            ->where('status', 'published')
            ->with(['images', 'pricings'])
            ->take(4)
            ->get();

        $placeIdSetting = site_setting('reviews_home_place_id', '');
        $apiKey = config('services.google.places_api_key');
        $tourGoogleReviewsRequested = is_string($placeIdSetting)
            && trim($placeIdSetting) !== ''
            && is_string($apiKey)
            && $apiKey !== '';
        $tourGooglePlaceData = null;
        if ($tourGoogleReviewsRequested) {
            $tourGooglePlaceData = app(GooglePlaceReviewsService::class)->fetch(
                GooglePlaceReviewsService::normalizePlaceId($placeIdSetting),
                app()->getLocale()
            );
        }

        $translation = $tour->translate(app()->getLocale()) ?? $tour->translate('fr');
        $slug = $translation?->slug ?? $tour->slug;

        $seo = $this->seoService->generateMetaTags([
            'title' => ($translation?->meta_title ?? $translation?->title ?? $tour->title) . ' | MarrakechTours',
            'description' => $translation?->meta_description ?? $this->seoService->truncateDescription(strip_tags($translation?->description ?? $tour->description ?? '')),
            'image' => $tour->primaryImage
                ? public_storage_url($tour->primaryImage->path)
                : ($tour->images->first() ? public_storage_url($tour->images->first()->path) : null),
            'type' => 'product',
        ]);

        $hreflang = $this->seoService->generateHreflangTags('tours.show', ['slug' => $slug]);

        return view('frontend.tours.show', compact(
            'tour',
            'relatedTours',
            'activePromotion',
            'defaultPricing',
            'tourGoogleReviewsRequested',
            'tourGooglePlaceData',
            'seo',
            'hreflang',
        ));
    }

    public function booking(Tour $tour, Request $request)
    {
        $tour->load(['categories', 'category', 'images', 'primaryImage', 'tourDates', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);
        
        // Get active promotion
        $activePromotion = $tour->activePromotion();
        
        // Get default pricing
        $defaultPricing = $tour->defaultPricing();
        
        // Obtenir tous les tarifs actifs
        $allPricings = $tour->getActivePricings();
        
        // Préparer les données des tarifs pour JavaScript
        $pricingData = [];
        foreach ($allPricings as $pricing) {
            $basePrice = $pricing->price_min ? (float)$pricing->price_min : null;
            $finalPrice = null;
            $originalPriceForTier = null;
            
            if ($basePrice) {
                $originalPriceForTier = $basePrice;
                // Calculer le prix avec promotion si active
                if ($activePromotion) {
                    $finalPrice = (float)$activePromotion->calculateDiscountedPrice($basePrice);
                } else {
                    $finalPrice = $basePrice;
                }
                // Convertir le prix
                $finalPrice = \App\Helpers\CurrencyHelper::convert($finalPrice);
                $originalPriceForTier = \App\Helpers\CurrencyHelper::convert($originalPriceForTier);
            }
            
            // Calculer les prix enfants et bébés
            $childPrice = null;
            $infantPrice = null;
            if ($basePrice) {
                $childPrice = $pricing->getChildPrice();
                $infantPrice = $pricing->getInfantPrice();
                
                // Appliquer la promotion si active
                if ($activePromotion) {
                    $childPrice = (float)$activePromotion->calculateDiscountedPrice($childPrice);
                    if ($infantPrice > 0) {
                        $infantPrice = (float)$activePromotion->calculateDiscountedPrice($infantPrice);
                    }
                }
                
                // Convertir les prix
                $childPrice = \App\Helpers\CurrencyHelper::convert($childPrice);
                $infantPrice = \App\Helpers\CurrencyHelper::convert($infantPrice);
            }
            
            // Pour le pricing private, inclure les tiers de prix
            $privatePriceTiers = [];
            if ($pricing->pricing_mode === 'private') {
                // Charger les privatePrices si pas déjà chargé
                if (!$pricing->relationLoaded('privatePrices')) {
                    $pricing->load('privatePrices');
                }
                
                foreach ($pricing->privatePrices as $tier) {
                    $tierPrice = (float)$tier->price;
                    // Appliquer la promotion si active
                    if ($activePromotion) {
                        $tierPrice = (float)$activePromotion->calculateDiscountedPrice($tierPrice);
                    }
                    // Convertir le prix
                    $tierPrice = \App\Helpers\CurrencyHelper::convert($tierPrice);
                    
                    $privatePriceTiers[] = [
                        'min_people' => $tier->min_people,
                        'max_people' => $tier->max_people,
                        'price' => $tierPrice,
                    ];
                }
            }
            
            $pricingTranslation = $pricing->translate();
            $pricingData[] = [
                'id' => $pricing->id,
                'title' => $pricingTranslation ? $pricingTranslation->title : $pricing->title,
                'pricing_mode' => $pricing->pricing_mode,
                'season' => $pricing->season,
                'min_participants' => $pricing->min_participants,
                'max_participants' => $pricing->max_participants ?? 999,
                'price' => $finalPrice,
                'child_price' => $childPrice,
                'infant_price' => $infantPrice,
                'child_discount_percentage' => $pricing->child_discount_percentage,
                'original_price' => $originalPriceForTier,
                'requires_consultation' => $pricing->requiresConsultation(),
                'label' => $pricing->participants_label,
                'name' => $pricing->name,
                'description' => $pricing->description,
                'private_price_tiers' => $privatePriceTiers,
            ];
        }
        
        // Grouper les tarifs par mode pour l'affichage
        $groupPricings = collect($pricingData)->where('pricing_mode', 'group')->values();
        $privatePricings = collect($pricingData)->where('pricing_mode', 'private')->values();
        
        // Get initial values from request (if coming from form)
        $initialDate = $request->input('date');
        $initialParticipants = $request->input('participants', 1);
        $initialAdults = $request->input('adults', 1);
        $initialChildren = $request->input('children', 0);
        $initialInfants = $request->input('infants', 0);
        
        return view('frontend.tours.booking', compact(
            'tour', 
            'activePromotion', 
            'defaultPricing', 
            'groupPricings', 
            'privatePricings',
            'initialDate',
            'initialParticipants',
            'initialAdults',
            'initialChildren',
            'initialInfants'
        ));
    }

    public function selectFormula(Tour $tour, Request $request)
    {
        // Validate required parameters
        if (!$request->has('date') || !$request->has('participants')) {
            return redirect()->route('tours.show', $tour->url_key)
                ->with('error', 'Veuillez sélectionner une date et le nombre de participants.');
        }

        // Ensure date is today or in the future
        $requestedDate = \Carbon\Carbon::parse($request->input('date'));
        $today = \Carbon\Carbon::today();
        if ($requestedDate->isBefore($today)) {
            // Use today's date if the requested date is in the past
            $requestedDate = $today;
        }

        $tour->load(['categories', 'category', 'images', 'primaryImage', 'tourDates', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);
        
        $activePromotion = $tour->activePromotion();
        $defaultPricing = $tour->defaultPricing();
        
        // Obtenir tous les tarifs actifs
        $allPricings = $tour->getActivePricings();
        
        // Préparer les données des tarifs
        $pricingData = [];
        foreach ($allPricings as $pricing) {
            $basePrice = $pricing->price_min ? (float)$pricing->price_min : null;
            $finalPrice = null;
            $originalPriceForTier = null;
            
            if ($basePrice) {
                $originalPriceForTier = $basePrice;
                // Calculer le prix avec promotion si active
                if ($activePromotion) {
                    $finalPrice = (float)$activePromotion->calculateDiscountedPrice($basePrice);
                } else {
                    $finalPrice = $basePrice;
                }
                // Convertir le prix
                $finalPrice = \App\Helpers\CurrencyHelper::convert($finalPrice);
                $originalPriceForTier = \App\Helpers\CurrencyHelper::convert($originalPriceForTier);
            }
            
            // Calculer les prix enfants et bébés
            $childPrice = null;
            $infantPrice = null;
            if ($basePrice) {
                $childPrice = $pricing->getChildPrice();
                $infantPrice = $pricing->getInfantPrice();
                
                // Appliquer la promotion si active
                if ($activePromotion) {
                    $childPrice = (float)$activePromotion->calculateDiscountedPrice($childPrice);
                    if ($infantPrice > 0) {
                        $infantPrice = (float)$activePromotion->calculateDiscountedPrice($infantPrice);
                    }
                }
                
                // Convertir les prix
                $childPrice = \App\Helpers\CurrencyHelper::convert($childPrice);
                $infantPrice = \App\Helpers\CurrencyHelper::convert($infantPrice);
            }
            
            // Pour le pricing private, inclure les tiers de prix
            $privatePriceTiers = [];
            if ($pricing->pricing_mode === 'private') {
                if (!$pricing->relationLoaded('privatePrices')) {
                    $pricing->load('privatePrices');
                }
                
                foreach ($pricing->privatePrices as $tier) {
                    $tierPrice = (float)$tier->price;
                    if ($activePromotion) {
                        $tierPrice = (float)$activePromotion->calculateDiscountedPrice($tierPrice);
                    }
                    $tierPrice = \App\Helpers\CurrencyHelper::convert($tierPrice);
                    
                    $privatePriceTiers[] = [
                        'min_people' => $tier->min_people,
                        'max_people' => $tier->max_people,
                        'price' => $tierPrice,
                    ];
                }
            }
            
            $pricingTranslation = $pricing->translate();
            $pricingData[] = [
                'id' => $pricing->id,
                'title' => $pricingTranslation ? $pricingTranslation->title : $pricing->title,
                'pricing_mode' => $pricing->pricing_mode,
                'season' => $pricing->season,
                'min_participants' => $pricing->min_participants,
                'max_participants' => $pricing->max_participants ?? 999,
                'price' => $finalPrice,
                'child_price' => $childPrice,
                'infant_price' => $infantPrice,
                'child_discount_percentage' => $pricing->child_discount_percentage,
                'original_price' => $originalPriceForTier,
                'requires_consultation' => $pricing->requiresConsultation(),
                'label' => $pricing->participants_label,
                'name' => $pricing->name,
                'description' => $pricing->description,
                'private_price_tiers' => $privatePriceTiers,
            ];
        }
        
        // Grouper les tarifs par mode pour l'affichage
        $groupPricings = collect($pricingData)->where('pricing_mode', 'group')->values();
        $privatePricings = collect($pricingData)->where('pricing_mode', 'private')->values();
        
        // Récupérer les paramètres de la requête
        $selectedDate = $requestedDate->format('Y-m-d');
        $participants = (int)$request->input('participants', 1);
        $adults = (int)$request->input('adults', $participants);
        $children = (int)$request->input('children', 0);
        $infants = (int)$request->input('infants', 0);
        
        return view('frontend.tours.select-formula', compact(
            'tour',
            'activePromotion',
            'defaultPricing',
            'groupPricings',
            'privatePricings',
            'selectedDate',
            'participants',
            'adults',
            'children',
            'infants'
        ));
    }

    public function bookingWizard(Tour $tour, Request $request)
    {
        $tour->load(['categories', 'category', 'images', 'primaryImage', 'tourDates', 'pricings.groupPrices', 'pricings.privatePrices', 'pricings.translations', 'promotions']);
        
        $activePromotion = $tour->activePromotion();
        
        // Validate required parameters
        if (!$request->has('date') || !$request->has('pricing_mode') || !$request->has('total_people')) {
            return redirect()->route('tours.show', $tour->url_key)
                ->with('error', 'Informations de réservation manquantes. Veuillez recommencer.');
        }
        
        return view('frontend.tours.booking-wizard-new', compact('tour', 'activePromotion'));
    }
}
