<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientRegisterController extends Controller
{
    /**
     * Display the client registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('first_name') || $request->filled('last_name')) {
            $request->merge([
                'name' => trim($request->string('first_name').' '.$request->string('last_name')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:clients'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
        ]);

        event(new Registered($client));

        Auth::guard('client')->login($client);
        $request->session()->regenerate();

        // Rediriger vers le checkout si c'était l'intention
        if ($request->has('redirect_to_checkout') && $request->redirect_to_checkout === '1') {
            return redirect()->route('cart.checkout')
                ->with('success', 'Compte créé avec succès ! Vous pouvez maintenant finaliser votre commande.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Compte créé avec succès !');
    }
}

