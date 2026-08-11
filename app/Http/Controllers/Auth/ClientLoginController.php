<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;

class ClientLoginController extends Controller
{
    /**
     * Handle an incoming client authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $remember = $request->boolean('remember');

        if (Auth::guard('client')->attempt($request->only('email', 'password'), $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($this->throttleKey($request));

            // Rediriger vers le checkout si c'était l'intention
            if (Session::has('checkout_intended')) {
                $checkoutUrl = Session::pull('checkout_intended');
                return redirect($checkoutUrl)
                    ->with('success', 'Connexion réussie ! Vous pouvez maintenant finaliser votre commande.');
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        RateLimiter::hit($this->throttleKey($request));

        // Vérifier si l'email existe
        $clientExists = \App\Models\Client::where('email', $request->email)->exists();
        
        if (!$clientExists) {
            throw ValidationException::withMessages([
                'email' => __('Cet email n\'existe pas dans notre système.'),
            ]);
        }

        // Si l'email existe mais le mot de passe est incorrect
        throw ValidationException::withMessages([
            'email' => __('Les identifiants fournis sont incorrects. Veuillez vérifier votre email et votre mot de passe.'),
        ]);
    }

    /**
     * Destroy an authenticated client session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($key),
                    'minutes' => ceil(RateLimiter::availableIn($key) / 60),
                ]),
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return 'client_login:' . $request->ip() . ':' . $request->email;
    }
}
