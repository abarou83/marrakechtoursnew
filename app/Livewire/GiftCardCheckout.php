<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\GiftCard;
use App\Services\GiftCardService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GiftCardCheckout extends Component
{
    public float $amount = 50;
    public string $recipientName = '';
    public string $recipientEmail = '';
    public string $message = '';

    public ?GiftCard $giftCard = null;
    public ?string $stripeClientSecret = null;
    public bool $paid = false;
    public string $errorMessage = '';

    public function mount(): void
    {
        $amounts = config('marketing.gift_card.amounts', [50, 75, 100]);
        $this->amount = (float) ($amounts[0] ?? 50);
    }

    public function startCheckout(): void
    {
        $this->errorMessage = '';
        $this->validate([
            'amount' => 'required|numeric|min:' . config('marketing.gift_card.min_amount', 25)
                . '|max:' . config('marketing.gift_card.max_amount', 500),
            'recipientEmail' => 'nullable|email|max:255',
            'recipientName' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            $this->giftCard = GiftCard::create([
                'initial_amount' => $this->amount,
                'remaining_amount' => $this->amount,
                'currency' => session('currency', 'EUR'),
                'purchaser_client_id' => Auth::guard('client')->id(),
                'recipient_name' => $this->recipientName ?: null,
                'recipient_email' => $this->recipientEmail ?: null,
                'message' => $this->message ?: null,
                'expires_at' => now()->addMonths(config('marketing.gift_card.validity_months', 12)),
                'is_active' => false,
                'payment_status' => 'pending',
            ]);

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $intent = $stripe->paymentIntents->create([
                'amount' => (int) round($this->amount * 100),
                'currency' => strtolower(session('currency', 'eur')),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'gift_card_id' => $this->giftCard->id,
                    'type' => 'gift_card',
                ],
                'receipt_email' => Auth::guard('client')->user()?->email ?? $this->recipientEmail,
            ]);

            $this->giftCard->update(['payment_intent_id' => $intent->id]);
            $this->stripeClientSecret = $intent->client_secret;
        } catch (\Exception $e) {
            Log::error('Gift card checkout failed', ['error' => $e->getMessage()]);
            $this->errorMessage = __('Erreur lors de l\'initialisation du paiement.');
        }
    }

    #[On('gift-card-payment-succeeded')]
    public function handlePaymentSuccess(): void
    {
        if (!$this->giftCard) {
            return;
        }

        $this->giftCard->update([
            'is_active' => true,
            'payment_status' => 'paid',
        ]);

        $this->paid = true;
    }

    public function render()
    {
        return view('livewire.gift-card-checkout', [
            'amounts' => config('marketing.gift_card.amounts', [50, 75, 100, 150, 200]),
        ]);
    }
}
