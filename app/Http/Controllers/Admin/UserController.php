<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $users = User::withCount('bookings')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user'],
            'is_active' => ['boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,user'],
            'is_active' => ['boolean'],
        ]);

        if ($user->id === Auth::guard('admin')->id()) {
            if ($validated['role'] !== 'admin') {
                return back()->withInput()->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
            }
            if (!$request->boolean('is_active', true)) {
                return back()->withInput()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        if ($user->id === Auth::guard('admin')->id() && $request->role !== 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === Auth::guard('admin')->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::guard('admin')->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
