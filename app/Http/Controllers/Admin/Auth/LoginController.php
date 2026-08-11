<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Display the admin login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Utiliser le guard 'admin' spécifiquement
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $user = Auth::guard('admin')->user();
            
            // Vérifier que l'utilisateur est admin
            if (!$user || !$user->isAdmin()) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Accès refusé. Seuls les administrateurs peuvent se connecter ici.',
                ])->onlyInput('email');
            }

            if (!$user->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Ce compte administrateur est désactivé.',
                ])->onlyInput('email');
            }

            // Régénérer la session pour éviter les attaques de fixation de session
            // IMPORTANT: Régénérer APRÈS l'authentification pour préserver l'état d'authentification
            $request->session()->regenerate();

            // Vérifier que l'authentification est toujours active après régénération
            if (!Auth::guard('admin')->check()) {
                // Si l'authentification a été perdue, reconnecter l'utilisateur
                Auth::guard('admin')->login($user, $remember);
            }

            $this->auditService->logLogin('admin');

            // Rediriger directement vers le dashboard
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }
}









