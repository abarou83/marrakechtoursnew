<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class ExitIntentPopup extends Component
{
    public bool $show = false;
    public bool $dismissed = false;

    public function mount(): void
    {
        if (!config('marketing.exit_intent.enabled', true)) {
            $this->dismissed = true;
            return;
        }

        if (Cookie::get('exit_intent_dismissed')) {
            $this->dismissed = true;
        }
    }

    public function dismiss(): void
    {
        $this->show = false;
        $this->dismissed = true;
        Cookie::queue('exit_intent_dismissed', '1', 60 * 24);
    }

    public function render()
    {
        return view('livewire.exit-intent-popup', [
            'promoCode' => config('marketing.exit_intent.promo_code', 'BIENVENUE10'),
            'promoPercent' => config('marketing.exit_intent.promo_percent', 10),
        ]);
    }
}
