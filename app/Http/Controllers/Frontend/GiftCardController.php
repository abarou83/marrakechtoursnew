<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\GiftCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftCardController extends Controller
{
    public function __construct(
        protected GiftCardService $giftCardService
    ) {}

    public function index()
    {
        $amounts = config('marketing.gift_card.amounts', [50, 75, 100, 150, 200]);

        return view('frontend.gift-cards.index', compact('amounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . config('marketing.gift_card.min_amount', 25)
                . '|max:' . config('marketing.gift_card.max_amount', 500),
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        $giftCard = $this->giftCardService->purchase(
            $validated,
            Auth::guard('client')->user()
        );

        return redirect()
            ->route('gift-cards.confirmation', $giftCard->code)
            ->with('success', __('Votre carte cadeau a été créée avec succès !'));
    }

    public function confirmation(string $code)
    {
        $giftCard = \App\Models\GiftCard::where('code', $code)->firstOrFail();

        return view('frontend.gift-cards.confirmation', compact('giftCard'));
    }
}
