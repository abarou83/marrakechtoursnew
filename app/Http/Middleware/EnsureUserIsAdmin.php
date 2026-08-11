<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Le middleware auth.admin a déjà vérifié l'authentification
        // On vérifie seulement le rôle admin
        $user = auth('admin')->user();
        
        if (!$user || !$user->isAdmin()) {
            auth('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Accès refusé. Administrateur requis.',
            ]);
        }

        return $next($request);
    }
}
