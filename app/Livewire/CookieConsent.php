<?php

namespace App\Livewire;

use App\Models\Consent;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class CookieConsent extends Component
{
    public bool $show = true;
    public bool $showDetails = false;

    public bool $necessary = true;
    public bool $analytics = false;
    public bool $marketing = false;
    public bool $preferences = false;

    protected string $cookieName = 'cookie_consent';
    protected int $cookieDuration = 365;

    public function mount(): void
    {
        $consent = request()->cookie($this->cookieName);

        if ($consent) {
            $this->show = false;
            $this->loadFromCookie($consent);
        }
    }

    public function acceptAll(): void
    {
        $this->necessary = true;
        $this->analytics = true;
        $this->marketing = true;
        $this->preferences = true;

        $this->saveConsent();
    }

    public function acceptNecessary(): void
    {
        $this->necessary = true;
        $this->analytics = false;
        $this->marketing = false;
        $this->preferences = false;

        $this->saveConsent();
    }

    public function saveCustom(): void
    {
        $this->necessary = true;
        $this->saveConsent();
    }

    public function toggleDetails(): void
    {
        $this->showDetails = !$this->showDetails;
    }

    protected function saveConsent(): void
    {
        $consentData = [
            'necessary' => $this->necessary,
            'analytics' => $this->analytics,
            'marketing' => $this->marketing,
            'preferences' => $this->preferences,
            'timestamp' => now()->toIso8601String(),
            'version' => config('gdpr.consent_version', config('app.consent_version', '1.0')),
        ];

        Cookie::queue(
            $this->cookieName,
            json_encode($consentData),
            $this->cookieDuration * 24 * 60
        );

        Consent::recordConsent(
            $consentData,
            Auth::guard('client')->id()
        );

        $this->show = false;

        $this->dispatch('cookie-consent-saved', consent: $consentData);
    }

    protected function loadFromCookie(string $consent): void
    {
        $data = json_decode($consent, true);

        if (is_array($data)) {
            $this->necessary = $data['necessary'] ?? true;
            $this->analytics = $data['analytics'] ?? false;
            $this->marketing = $data['marketing'] ?? false;
            $this->preferences = $data['preferences'] ?? false;
        }
    }

    public function render()
    {
        return view('livewire.cookie-consent');
    }
}
