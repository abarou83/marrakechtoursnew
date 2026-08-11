<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $client = Auth::guard('client')->user();

        return view('frontend.contact.index', [
            'companyName' => site_setting('company_name', config('app.name')),
            'companyEmail' => site_setting('company_email', 'contact@example.com'),
            'companyPhone' => site_setting('company_phone', '+33 1 23 45 67 89'),
            'companyAddress' => site_setting('company_address', 'Paris, France'),
            'whatsappNumber' => site_setting('whatsapp_number'),
            'defaultName' => old('name', $client?->name),
            'defaultEmail' => old('email', $client?->email),
            'defaultPhone' => old('phone', $client?->phone),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = site_setting('company_email', config('mail.from.address'));

        if ($recipient) {
            try {
                Mail::to($recipient)->send(new ContactFormMail($validated));
            } catch (\Throwable $e) {
                Log::error('Contact form mail failed: ' . $e->getMessage(), [
                    'email' => $validated['email'],
                ]);

                return back()
                    ->withInput()
                    ->with('error', __('Unable to send your message. Please try again or contact us by phone.'));
            }
        }

        return redirect()
            ->route('contact')
            ->with('success', __('Your message has been sent successfully. We will get back to you as soon as possible.'));
    }
}
