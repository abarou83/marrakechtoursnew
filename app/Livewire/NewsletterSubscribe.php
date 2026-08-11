<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class NewsletterSubscribe extends Component
{
    public string $email = '';

    public string $name = '';

    public bool $subscribed = false;

    public string $message = '';

    /** Affichage compact (colonne footer). */
    public bool $compact = false;

    protected function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ];
    }

    public function subscribe(): void
    {
        $this->validate();

        $existing = \App\Models\NewsletterSubscriber::where('email', $this->email)->first();

        if ($existing && $existing->isActive()) {
            $this->message = __('Vous êtes déjà inscrit.');
            $this->subscribed = true;
            return;
        }

        if ($existing) {
            $existing->update([
                'unsubscribed_at' => null,
                'subscribed_at' => now(),
                'name' => $this->name ?: $existing->name,
            ]);
        } else {
            \App\Models\NewsletterSubscriber::create([
                'email' => $this->email,
                'name' => $this->name ?: null,
                'locale' => app()->getLocale(),
                'source' => 'footer',
                'client_id' => auth('client')->id(),
            ]);
        }

        $this->subscribed = true;
        $this->message = __('Merci ! Vous êtes inscrit à notre newsletter.');
        $this->reset(['email', 'name']);
    }

    public function render()
    {
        return view('livewire.newsletter-subscribe');
    }
}
