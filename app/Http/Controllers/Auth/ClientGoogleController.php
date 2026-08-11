<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class ClientGoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect(Request $request)
    {
        try {
            // Si redirect_to=checkout, enregistrer l'intention dans la session
            if ($request->get('redirect_to') === 'checkout') {
                Session::put('checkout_intended', route('cart.checkout'));
            }
            
            return Socialite::driver('google')
                ->redirect();
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la redirection vers Google. Veuillez vérifier la configuration.');
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Vérifier d'abord si un client existe déjà avec ce google_id
            $client = Client::where('google_id', $googleUser->id)->first();
            
            // Si pas trouvé par google_id, chercher par email
            if (!$client) {
                $client = Client::where('email', $googleUser->email)->first();
            }
            
            if (!$client) {
                // Créer un nouveau client
                $client = Client::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire (non utilisé pour Google OAuth)
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(), // Email vérifié par Google
                    'phone' => null, // Téléphone non fourni par Google
                ]);
            } else {
                // Mettre à jour le google_id si nécessaire
                if (!$client->google_id) {
                    $client->update(['google_id' => $googleUser->id]);
                }
                // Mettre à jour le nom si nécessaire
                if ($client->name !== $googleUser->name) {
                    $client->update(['name' => $googleUser->name]);
                }
                // Mettre à jour l'email si nécessaire (au cas où l'email Google a changé)
                if ($client->email !== $googleUser->email) {
                    $client->update(['email' => $googleUser->email]);
                }
            }
            
            // Connecter le client
            Auth::guard('client')->login($client, true); // true = remember me
            request()->session()->regenerate();
            
            // Rediriger vers le checkout si c'était l'intention
            if (Session::has('checkout_intended')) {
                $checkoutUrl = Session::pull('checkout_intended');
                return redirect($checkoutUrl)
                    ->with('success', 'Connexion réussie avec Google ! Vous pouvez maintenant finaliser votre commande.');
            }
            
            return redirect()->route('dashboard')
                ->with('success', 'Connexion réussie avec Google !');
                
        } catch (Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Erreur lors de la connexion avec Google. Veuillez réessayer.');
        }
    }
}

