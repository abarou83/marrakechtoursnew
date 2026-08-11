<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Afficher le panier
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $key => $item) {
            $tour = Tour::with(['images', 'primaryImage'])->find($item['tour_id']);
            if ($tour) {
                // Recalculer le prix pour s'assurer qu'il est à jour
                try {
                    $priceData = $this->pricingService->calculatePrice(
                        $tour,
                        $item['pricing_mode'],
                        $item['date'],
                        $item['adults'],
                        $item['children'] ?? 0,
                        $item['infants'] ?? 0,
                        $item['selected_addons'] ?? [],
                        $item['pricing_id'] ?? null
                    );

                    $item['total_price'] = $priceData['total_price'];
                    $item['price_data'] = $priceData;
                    $totalAmount += $priceData['total_price'];
                    
                    // Récupérer le nom de la formule
                    if (isset($item['pricing_id'])) {
                        $pricing = \App\Models\TourPricing::find($item['pricing_id']);
                        $pricingTranslation = $pricing ? $pricing->translate() : null;
                        $translatedTitle = ($pricingTranslation ? $pricingTranslation->title : null) ?? $pricing->title ?? null;
                        $item['pricing_title'] = $pricing ? ($translatedTitle ?? ($pricing->pricing_mode === 'group' ? __('Tarif Groupe') : __('Tarif Privé'))) : null;
                    }
                    
                    // Récupérer l'heure de départ
                    if (isset($item['tour_date_id'])) {
                        $tourDate = \App\Models\TourDate::find($item['tour_date_id']);
                        $item['departure_time'] = $tourDate ? $tourDate->start_at->format('H:i') : null;
                    }
                } catch (\Exception $e) {
                    // Si le calcul échoue, utiliser le prix sauvegardé
                    $item['total_price'] = $item['total_price'] ?? 0;
                    $totalAmount += $item['total_price'];
                }

                $item['tour'] = $tour;
                $cartItems[$key] = $item;
            }
        }

        return view('frontend.cart.index', compact('cartItems', 'totalAmount'));
    }

    /**
     * Ajouter un tour au panier
     */
    public function add(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'pricing_mode' => 'required|in:group,private',
            'pricing_id' => 'required|exists:tour_pricings,id',
            'tour_date_id' => 'nullable|exists:tour_dates,id',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'selected_addons' => 'nullable|array',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Calculer le nombre total de participants
        $participants = $validated['pricing_mode'] === 'group' 
            ? ($validated['adults'] + ($validated['children'] ?? 0) + ($validated['infants'] ?? 0))
            : $validated['adults'];

        // Créer un identifiant unique pour cet item du panier
        $cartItemId = uniqid('cart_', true);

        // Récupérer le panier actuel
        $cart = Session::get('cart', []);

        // Ajouter l'item au panier
        $cart[$cartItemId] = [
            'tour_id' => $tour->id,
            'date' => $validated['date'],
            'pricing_mode' => $validated['pricing_mode'],
            'pricing_id' => $validated['pricing_id'],
            'tour_date_id' => $validated['tour_date_id'] ?? null,
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'infants' => $validated['infants'] ?? 0,
            'participants' => $participants,
            'selected_addons' => $validated['selected_addons'] ?? [],
            'total_price' => $validated['total_price'],
            'added_at' => now()->toDateTimeString(),
        ];

        // Sauvegarder le panier dans la session
        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Tour ajouté au panier avec succès',
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Retirer un item du panier
     */
    public function remove($itemId)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            Session::put('cart', $cart);

            return redirect()->route('cart.index')
                ->with('success', 'Item retiré du panier');
        }

        return redirect()->route('cart.index')
            ->with('error', 'Item introuvable dans le panier');
    }

    /**
     * Vider le panier
     */
    public function clear()
    {
        Session::forget('cart');

        return redirect()->route('cart.index')
            ->with('success', 'Panier vidé avec succès');
    }

    /**
     * Obtenir le nombre d'items dans le panier (API)
     */
    public function count()
    {
        $cart = Session::get('cart', []);
        return response()->json(['count' => count($cart)]);
    }

    /**
     * Mettre à jour un item du panier
     */
    public function update(Request $request, $itemId)
    {
        $cart = Session::get('cart', []);

        if (!isset($cart[$itemId])) {
            return redirect()->route('cart.index')
                ->with('error', 'Item introuvable dans le panier');
        }

        $validated = $request->validate([
            'date' => 'nullable|date',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'selected_addons' => 'nullable|array',
        ]);

        // Mettre à jour les champs fournis
        if (isset($validated['date'])) {
            $cart[$itemId]['date'] = $validated['date'];
        }
        if (isset($validated['adults'])) {
            $cart[$itemId]['adults'] = $validated['adults'];
        }
        if (isset($validated['children'])) {
            $cart[$itemId]['children'] = $validated['children'];
        }
        if (isset($validated['infants'])) {
            $cart[$itemId]['infants'] = $validated['infants'];
        }
        if (isset($validated['selected_addons'])) {
            $cart[$itemId]['selected_addons'] = $validated['selected_addons'];
        }

        // Recalculer le nombre total de participants
        $cart[$itemId]['participants'] = $cart[$itemId]['pricing_mode'] === 'group'
            ? ($cart[$itemId]['adults'] + ($cart[$itemId]['children'] ?? 0) + ($cart[$itemId]['infants'] ?? 0))
            : $cart[$itemId]['adults'];

        // Recalculer le prix total
        try {
            $tour = Tour::find($cart[$itemId]['tour_id']);
            $priceData = $this->pricingService->calculatePrice(
                $tour,
                $cart[$itemId]['pricing_mode'],
                $cart[$itemId]['date'],
                $cart[$itemId]['adults'],
                $cart[$itemId]['children'] ?? 0,
                $cart[$itemId]['infants'] ?? 0,
                $cart[$itemId]['selected_addons'] ?? [],
                $cart[$itemId]['pricing_id'] ?? null
            );
            $cart[$itemId]['total_price'] = $priceData['total_price'];
        } catch (\Exception $e) {
            // Si le calcul échoue, garder l'ancien prix
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')
            ->with('success', 'Panier mis à jour avec succès');
    }

    /**
     * Page de checkout (finalisation de la commande)
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide');
        }

        // Vérifier si le client est connecté
        if (!auth('client')->check()) {
            // Sauvegarder l'URL de checkout dans la session pour redirection après connexion
            Session::put('checkout_intended', route('cart.checkout'));
            return view('frontend.cart.checkout-auth');
        }

        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $key => $item) {
            $tour = Tour::with(['images', 'primaryImage'])->find($item['tour_id']);
            if ($tour) {
                try {
                    $priceData = $this->pricingService->calculatePrice(
                        $tour,
                        $item['pricing_mode'],
                        $item['date'],
                        $item['adults'],
                        $item['children'] ?? 0,
                        $item['infants'] ?? 0,
                        $item['selected_addons'] ?? [],
                        $item['pricing_id'] ?? null
                    );

                    $item['total_price'] = $priceData['total_price'];
                    $item['price_data'] = $priceData;
                    $totalAmount += $priceData['total_price'];
                    
                    // Récupérer le nom de la formule
                    if (isset($item['pricing_id'])) {
                        $pricing = \App\Models\TourPricing::find($item['pricing_id']);
                        $pricingTranslation = $pricing ? $pricing->translate() : null;
                        $translatedTitle = ($pricingTranslation ? $pricingTranslation->title : null) ?? $pricing->title ?? null;
                        $item['pricing_title'] = $pricing ? ($translatedTitle ?? ($pricing->pricing_mode === 'group' ? __('Tarif Groupe') : __('Tarif Privé'))) : null;
                    }
                    
                    // Récupérer l'heure de départ
                    if (isset($item['tour_date_id'])) {
                        $tourDate = \App\Models\TourDate::find($item['tour_date_id']);
                        $item['departure_time'] = $tourDate ? $tourDate->start_at->format('H:i') : null;
                    }
                } catch (\Exception $e) {
                    $item['total_price'] = $item['total_price'] ?? 0;
                    $totalAmount += $item['total_price'];
                }

                $item['tour'] = $tour;
                $cartItems[$key] = $item;
            }
        }

        return view('frontend.cart.checkout', compact('cartItems', 'totalAmount'));
    }

    /**
     * Traiter la commande (créer les réservations pour tous les tours du panier)
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide');
        }

        \DB::beginTransaction();

        try {
            $bookingsCreated = [];

            foreach ($cart as $item) {
                $tour = Tour::find($item['tour_id']);
                if (!$tour) {
                    continue;
                }

                // Recalculer le prix
                $priceData = $this->pricingService->calculatePrice(
                    $tour,
                    $item['pricing_mode'],
                    $item['date'],
                    $item['adults'],
                    $item['children'] ?? 0,
                    $item['infants'] ?? 0,
                    $item['selected_addons'] ?? [],
                    $item['pricing_id'] ?? null
                );

                // Créer la réservation
                $booking = \App\Models\Booking::create([
                    'client_id' => auth('client')->check() ? auth('client')->id() : null,
                    'tour_id' => $tour->id,
                    'tour_date_id' => $item['tour_date_id'] ?? null,
                    'preferred_date' => $item['date'],
                    'seats' => $item['participants'],
                    'adults' => $item['adults'],
                    'children' => $item['children'] ?? 0,
                    'infants' => $item['infants'] ?? 0,
                    'pricing_mode' => $item['pricing_mode'],
                    'base_price' => $priceData['base_price'],
                    'addons_total' => $priceData['addons_total'] ?? 0,
                    'total_amount' => $priceData['total_price'],
                    'total_price' => $priceData['total_price'],
                    'status' => 'pending',
                    'guest_name' => $validated['guest_name'],
                    'guest_email' => $validated['guest_email'],
                    'guest_phone' => $validated['guest_phone'],
                ]);

                // Sauvegarder les addons sélectionnés
                if (isset($priceData['addons']) && is_array($priceData['addons'])) {
                    foreach ($priceData['addons'] as $addonData) {
                        \App\Models\BookingAddon::create([
                            'booking_id' => $booking->id,
                            'addon_id' => $addonData['addon_id'],
                            'quantity' => $addonData['quantity'],
                            'unit_price' => $addonData['unit_price'],
                            'total_price' => $addonData['total_price'],
                        ]);
                    }
                }

                // Créer le paiement
                \App\Models\Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $priceData['total_price'],
                    'status' => 'pending',
                    'method' => 'email',
                ]);

                $bookingsCreated[] = $booking;
            }

            \DB::commit();

            // Préparer les données pour la page de confirmation
            $confirmationData = [
                'bookings' => $bookingsCreated,
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'total_amount' => array_sum(array_column($bookingsCreated, 'total_price')),
            ];

            // Stocker les données dans la session pour la page de confirmation
            Session::put('confirmation_data', $confirmationData);

            // Vider le panier
            Session::forget('cart');

            // Rediriger vers la page de confirmation
            return redirect()->route('cart.confirmation');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la commande. Veuillez réessayer ou contacter le support.']);
        }
    }

    /**
     * Afficher la page de confirmation de commande
     */
    public function confirmation()
    {
        $confirmationData = Session::get('confirmation_data');

        if (!$confirmationData) {
            return redirect()->route('cart.index')
                ->with('error', 'Aucune donnée de confirmation trouvée.');
        }

        // Charger les tours et autres données nécessaires pour l'affichage
        $bookings = [];
        foreach ($confirmationData['bookings'] as $booking) {
            $booking->load(['tour.images', 'tour.primaryImage', 'tourDate', 'bookingAddons.addon']);
            $bookings[] = $booking;
        }

        return view('frontend.cart.confirmation', [
            'bookings' => $bookings,
            'guest_name' => $confirmationData['guest_name'],
            'guest_email' => $confirmationData['guest_email'],
            'guest_phone' => $confirmationData['guest_phone'],
            'total_amount' => $confirmationData['total_amount'],
        ]);
    }
}

