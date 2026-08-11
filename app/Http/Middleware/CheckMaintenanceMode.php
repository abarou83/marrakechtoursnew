<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Routes exclues du mode maintenance
     */
    protected array $excludedRoutes = [
        'admin/*',
        'admin',
        'login',
        'logout',
        'maintenance',
        // Connexion / inscription client (formulaires, Google OAuth) pendant la maintenance
        'client/*',
        'contact',
        'sitemap.xml',
        'robots.txt',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le mode maintenance est activé
        try {
            $maintenanceMode = site_setting('maintenance_mode', false);
        } catch (\Exception $e) {
            $maintenanceMode = false;
        }

        // Si le mode maintenance n'est pas activé, continuer normalement
        if (!$maintenanceMode || $maintenanceMode === '0' || $maintenanceMode === 'false') {
            return $next($request);
        }

        // Administrateurs : accès complet
        if (auth('admin')->check()) {
            return $next($request);
        }

        // Clients connectés (réservations / compte) : accès au site public
        if (auth('client')->check()) {
            return $next($request);
        }

        // Vérifier si la route actuelle est exclue
        foreach ($this->excludedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Token de bypass : ?bypass=... (priorité .env puis paramètres site)
        $bypassToken = trim((string) config('maintenance.bypass_token', ''));
        if ($bypassToken === '') {
            try {
                $bypassToken = trim((string) site_setting('maintenance_bypass_token', ''));
            } catch (\Exception $e) {
                $bypassToken = '';
            }
        }

        if ($bypassToken !== '' && hash_equals($bypassToken, (string) $request->query('bypass', ''))) {
            session(['maintenance_bypass' => true]);

            return $next($request);
        }

        // Vérifier si l'utilisateur a déjà un bypass en session
        if (session('maintenance_bypass')) {
            return $next($request);
        }

        // Afficher la page de maintenance
        return response()->view('maintenance', [], 503);
    }
}
