<?php

namespace App\Livewire;

use App\Models\Tour;
use App\Models\Booking;
use App\Models\Client;
use App\Models\PromoCode;
use App\Models\TourAvailability;
use App\Services\PricingService;
use App\Services\BookingService;
use App\Services\AvailabilityService;
use App\Services\GeoService;
use App\Services\PaymentService;
use App\Services\GiftCardService;
use App\Enums\PaymentProvider;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingWizard extends Component
{
    public Tour $tour;

    public int $currentStep = 1;
    public int $totalSteps = 5;

    #[Url]
    public ?string $date = null;

    public string $pricingMode = 'group';
    public ?int $pricingId = null;
    public int $adults = 2;
    public int $children = 0;
    public int $infants = 0;

    public array $selectedAddons = [];

    public bool $isGuest = true;
    public ?int $clientId = null;
    public string $customerName = '';
    public string $customerEmail = '';
    public string $customerPhone = '';
    public string $specialRequests = '';

    public string $promoCode = '';
    public ?array $appliedPromo = null;
    public string $promoError = '';

    public string $referralCode = '';
    public string $giftCardCode = '';
    public bool $marketingOptIn = false;

    public string $paymentProvider = 'stripe';
    public string $paymentType = 'full';
    public ?string $paypalApproveUrl = null;
    public string $giftCardError = '';

    public ?array $priceCalculation = null;
    public ?string $stripeClientSecret = null;
    public ?Booking $booking = null;

    public bool $isProcessing = false;
    public string $errorMessage = '';

    protected PricingService $pricingService;
    protected BookingService $bookingService;
    protected AvailabilityService $availabilityService;

    protected $rules = [
        'date' => 'required|date|after:today',
        'pricingMode' => 'required|in:group,private',
        'adults' => 'required|integer|min:1|max:50',
        'children' => 'integer|min:0|max:20',
        'infants' => 'integer|min:0|max:10',
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'required|email|max:255',
        'customerPhone' => 'nullable|string|max:50',
    ];

    public function boot(
        PricingService $pricingService,
        BookingService $bookingService,
        AvailabilityService $availabilityService
    ): void {
        $this->pricingService = $pricingService;
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
    }

    public function mount(Tour $tour): void
    {
        $this->tour = $tour->load(['pricings.groupPrices', 'pricings.privatePrices', 'pricings.addons']);

        if (!$this->date) {
            $this->date = now()->addDay()->format('Y-m-d');
        }

        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $this->isGuest = false;
            $this->clientId = $client->id;
            $this->customerName = $client->name ?? '';
            $this->customerEmail = $client->email ?? '';
            $this->customerPhone = $client->phone ?? '';
        }

        if ($ref = session('referral_code')) {
            $this->referralCode = $ref;
        }

        $this->calculatePrice();
        $this->syncPricingIdFromCalculation();
    }

    protected function syncPricingIdFromCalculation(): void
    {
        if (! empty($this->priceCalculation['pricing_id'])) {
            $this->pricingId = (int) $this->priceCalculation['pricing_id'];
        }
    }

    public function updatedCustomerEmail(): void
    {
        if (filter_var($this->customerEmail, FILTER_VALIDATE_EMAIL)) {
            $this->trackAbandonedCart();
        }
    }

    protected function trackAbandonedCart(): void
    {
        if (!$this->customerEmail) {
            return;
        }

        app(\App\Services\AbandonedCartService::class)->track([
            'tour_id' => $this->tour->id,
            'client_id' => $this->clientId,
            'email' => $this->customerEmail,
            'customer_name' => $this->customerName,
            'travel_date' => $this->date,
            'adults' => $this->adults,
            'children' => $this->children,
            'total_amount' => $this->priceCalculation['total_price'] ?? null,
            'currency' => session('currency', 'EUR'),
            'marketing_opt_in' => $this->marketingOptIn,
        ]);
    }

    public function updatedDate(): void
    {
        $this->calculatePrice();
    }

    public function updatedPricingMode(): void
    {
        $this->selectedAddons = [];
        $this->calculatePrice();
    }

    public function updatedAdults(): void
    {
        $this->calculatePrice();
    }

    public function updatedChildren(): void
    {
        $this->calculatePrice();
    }

    public function updatedInfants(): void
    {
        $this->calculatePrice();
    }

    public function updatedSelectedAddons(): void
    {
        $this->calculatePrice();
    }

    public function selectPricing(int $pricingId): void
    {
        $this->pricingId = $pricingId;
        $this->calculatePrice();
    }

    public function toggleAddon(int $addonId): void
    {
        if (isset($this->selectedAddons[$addonId])) {
            unset($this->selectedAddons[$addonId]);
        } else {
            $this->selectedAddons[$addonId] = 1;
        }
        $this->calculatePrice();
    }

    public function calculatePrice(): void
    {
        if (!$this->date) {
            return;
        }

        try {
            $this->priceCalculation = $this->pricingService->calculatePrice(
                $this->tour,
                $this->pricingMode,
                $this->date,
                $this->adults,
                $this->children,
                $this->infants,
                $this->selectedAddons,
                $this->pricingId
            );

            if ($this->appliedPromo) {
                $this->applyPromoDiscount();
            }

            $this->errorMessage = '';
            $this->syncPricingIdFromCalculation();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->priceCalculation = null;
        }
    }

    public function applyPromoCode(): void
    {
        $this->promoError = '';
        $this->appliedPromo = null;

        if (empty($this->promoCode)) {
            return;
        }

        try {
            $promo = PromoCode::where('code', strtoupper($this->promoCode))
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
                ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
                ->first();

            if (!$promo) {
                $this->promoError = __('Code promo invalide ou expiré.');
                return;
            }

            if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
                $this->promoError = __('Ce code promo a atteint sa limite d\'utilisation.');
                return;
            }

            if ($promo->min_amount && $this->priceCalculation && $this->priceCalculation['total_price'] < $promo->min_amount) {
                $this->promoError = __('Montant minimum requis : :amount €', ['amount' => $promo->min_amount]);
                return;
            }

            $this->appliedPromo = [
                'id' => $promo->id,
                'code' => $promo->code,
                'type' => $promo->type,
                'value' => $promo->value,
            ];

            $this->applyPromoDiscount();

        } catch (\Exception $e) {
            $this->promoError = __('Erreur lors de l\'application du code promo.');
        }
    }

    protected function applyPromoDiscount(): void
    {
        if (!$this->appliedPromo || !$this->priceCalculation) {
            return;
        }

        $subtotal = $this->priceCalculation['total_price'];

        if ($this->appliedPromo['type'] === 'percent') {
            $discount = round($subtotal * ($this->appliedPromo['value'] / 100), 2);
        } else {
            $discount = min($this->appliedPromo['value'], $subtotal);
        }

        $this->priceCalculation['discount'] = $discount;
        $this->priceCalculation['total_after_discount'] = $subtotal - $discount;
    }

    public function removePromoCode(): void
    {
        $this->promoCode = '';
        $this->appliedPromo = null;
        $this->promoError = '';

        if ($this->priceCalculation) {
            unset($this->priceCalculation['discount']);
            unset($this->priceCalculation['total_after_discount']);
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }

        if ($this->currentStep === 4) {
            $this->calculatePrice();
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => true,
            3 => $this->validateStep3(),
            4 => $this->validateStep4(),
            default => true,
        };
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'date' => 'required|date|after:today',
            'pricingMode' => 'required|in:group,private',
            'adults' => 'required|integer|min:1',
        ]);

        $totalPeople = $this->adults + $this->children + $this->infants;
        $travelDate = Carbon::parse($this->date);

        if (!$this->availabilityService->isAvailable($this->tour, $travelDate, $totalPeople)) {
            throw new \Exception(__('Pas assez de places disponibles pour cette date.'));
        }
    }

    protected function validateStep3(): void
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'required|email|max:255',
        ]);
    }

    protected function validateStep4(): void
    {
        if (!$this->priceCalculation) {
            throw new \Exception(__('Erreur de calcul du prix. Veuillez réessayer.'));
        }
    }

    public function createBooking(): void
    {
        $this->isProcessing = true;
        $this->errorMessage = '';

        try {
            $this->validateStep3();
            $this->validateStep4();

            $geoService = app(GeoService::class);
            $geoData = $geoService->detectFromIp();

            $bookingData = [
                'tour_id' => $this->tour->id,
                'client_id' => $this->clientId,
                'pricing_id' => $this->priceCalculation['pricing_id'] ?? null,
                'pricing_mode' => $this->pricingMode,
                'travel_date' => $this->date,
                'adults' => $this->adults,
                'children' => $this->children,
                'infants' => $this->infants,
                'addons' => $this->selectedAddons,
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'special_requests' => $this->specialRequests,
                'promo_code' => $this->appliedPromo ? $this->appliedPromo['code'] : null,
                'referral_code' => $this->referralCode ?: null,
                'gift_card_code' => $this->giftCardCode ?: null,
                'payment_type' => config('marketing.deposit.enabled') && $this->paymentType === 'deposit' ? 'deposit' : 'full',
                'currency' => session('currency', 'EUR'),
                'exchange_rate' => 1.0,
                'country_code' => $geoData['country_code'] ?? null,
            ];

            $this->booking = $this->bookingService->createBooking($bookingData);

            $this->initPayment();

            $this->currentStep = 4;

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            Log::error('Booking creation failed', [
                'tour_id' => $this->tour->id,
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    protected function initPayment(): void
    {
        if (!$this->booking) {
            return;
        }

        $provider = PaymentProvider::tryFrom($this->paymentProvider) ?? PaymentProvider::Stripe;

        if ($provider === PaymentProvider::PayPal && !config('services.paypal.client_id')) {
            $provider = PaymentProvider::Stripe;
            $this->paymentProvider = 'stripe';
        }

        try {
            $result = app(PaymentService::class)->createPaymentIntent($this->booking->fresh(), $provider);

            if ($result['provider'] === 'stripe') {
                $this->stripeClientSecret = $result['client_secret'];
                $this->paypalApproveUrl = null;
            } else {
                $this->paypalApproveUrl = $result['approve_url'] ?? null;
                $this->stripeClientSecret = null;
            }
        } catch (\Exception $e) {
            Log::error('Payment init failed', ['booking_id' => $this->booking->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function switchPaymentProvider(string $provider): void
    {
        $this->paymentProvider = $provider;
        if ($this->booking) {
            $this->initPayment();
        }
    }

    public function getPayableAmountProperty(): float
    {
        if ($this->booking) {
            return app(PaymentService::class)->getPayableAmount($this->booking);
        }

        $total = $this->priceCalculation['total_after_discount'] ?? $this->priceCalculation['total_price'] ?? 0;

        if (config('marketing.deposit.enabled') && $this->paymentType === 'deposit') {
            return round($total * (config('marketing.deposit.percent', 20) / 100), 2);
        }

        return (float) $total;
    }

    public function updatedPaymentType(): void
    {
        if ($this->booking) {
            $total = $this->booking->total_ttc ?? $this->booking->total_price;
            $deposit = $this->paymentType === 'deposit' && config('marketing.deposit.enabled')
                ? round($total * (config('marketing.deposit.percent', 20) / 100), 2)
                : null;

            $this->booking->update([
                'payment_type' => $this->paymentType,
                'deposit_amount' => $deposit,
            ]);

            $this->initPayment();
        }
    }

    #[On('payment-succeeded')]
    public function handlePaymentSuccess(string $paymentIntentId): void
    {
        if (!$this->booking) {
            return;
        }

        try {
            $this->booking->update([
                'status' => BookingStatus::Confirmed,
                'payment_status' => PaymentStatus::Paid,
                'confirmed_at' => now(),
            ]);

            app(\App\Services\AbandonedCartService::class)->markConverted(
                $this->customerEmail,
                $this->tour->id,
                $this->booking->id
            );

            $this->currentStep = 5;

            $this->dispatch('booking-confirmed', bookingId: $this->booking->id);
            $this->dispatch('analytics-purchase', booking: [
                'reference' => $this->booking->reference,
                'value' => $this->booking->total_ttc ?? $this->booking->total_price,
                'currency' => $this->booking->currency ?? 'EUR',
                'tour_id' => $this->tour->id,
                'tour_name' => $this->tour->translate()?->title ?? $this->tour->title,
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = __('Erreur lors de la confirmation. Contactez-nous avec votre référence : ') . $this->booking->reference;
        }
    }

    #[On('payment-failed')]
    public function handlePaymentFailed(string $error): void
    {
        $this->errorMessage = __('Le paiement a échoué : ') . $error;
    }

    public function getAvailablePricingsProperty(): array
    {
        return $this->tour->pricings()
            ->where('is_active', true)
            ->where('pricing_mode', $this->pricingMode)
            ->with(['translations', 'groupPrices', 'privatePrices', 'addons'])
            ->get()
            ->toArray();
    }

    public function getAvailableAddonsProperty(): array
    {
        if (!$this->pricingId) {
            return [];
        }

        $pricing = $this->tour->pricings()->find($this->pricingId);
        if (!$pricing) {
            return [];
        }

        return $pricing->addons()
            ->where('is_active', true)
            ->with('translations')
            ->get()
            ->toArray();
    }

    public function getProgressPercentProperty(): int
    {
        return (int) (($this->currentStep / $this->totalSteps) * 100);
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
