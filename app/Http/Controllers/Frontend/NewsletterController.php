<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->isActive()) {
                return back()->with('success', __('Vous êtes déjà inscrit à notre newsletter.'));
            }

            $existing->update([
                'unsubscribed_at' => null,
                'subscribed_at' => now(),
                'name' => $validated['name'] ?? $existing->name,
                'locale' => app()->getLocale(),
            ]);

            return back()->with('success', __('Merci ! Votre inscription à la newsletter a été réactivée.'));
        }

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'locale' => app()->getLocale(),
            'source' => 'footer',
            'client_id' => auth('client')->id(),
        ]);

        return back()->with('success', __('Merci ! Vous êtes inscrit à notre newsletter.'));
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->unsubscribe();

        return view('frontend.newsletter.unsubscribed');
    }
}
