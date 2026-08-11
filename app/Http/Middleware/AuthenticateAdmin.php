<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Éviter la redirection infinie si on est déjà sur la page de login
        // Vérifier à la fois le nom de route et le chemin
        if ($request->routeIs('admin.login') || $request->is('admin/login')) {
            return $next($request);
        }

        // Vérifier spécifiquement le guard 'admin'
        if (!Auth::guard('admin')->check()) {
            // Sauvegarder l'URL demandée pour redirection après connexion
            if (!$request->expectsJson()) {
                $request->session()->put('url.intended', $request->fullUrl());
            }
            // Rediriger vers la page de login admin
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}

