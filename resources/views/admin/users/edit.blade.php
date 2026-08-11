@extends('admin.layout')

@section('title', 'Modifier ' . $user->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
        <i class="fas fa-arrow-left mr-1"></i>Retour à la liste
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <p class="text-red-800">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Informations --}}
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6"><i class="fas fa-user-edit mr-2"></i>Informations</h2>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border-gray-300 rounded-lg px-4 py-3 @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border-gray-300 rounded-lg px-4 py-3 @error('email') border-red-500 @enderror">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Rôle *</label>
                <select name="role" id="role" required class="w-full border-gray-300 rounded-lg px-4 py-3"
                        @if($user->id === auth('admin')->id()) disabled @endif>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Utilisateur</option>
                </select>
                @if($user->id === auth('admin')->id())
                    <input type="hidden" name="role" value="admin">
                    <p class="mt-1 text-xs text-gray-500">Vous ne pouvez pas modifier votre propre rôle.</p>
                @endif
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                       class="w-5 h-5 text-green-600 border-gray-300 rounded"
                       @if($user->id === auth('admin')->id()) disabled checked @endif>
                <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">Compte actif</label>
                @if($user->id === auth('admin')->id())
                    <input type="hidden" name="is_active" value="1">
                @endif
            </div>

            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </form>
    </div>

    {{-- Mot de passe --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6"><i class="fas fa-key mr-2"></i>Changer le mot de passe</h2>

            <form method="POST" action="{{ route('admin.users.updatePassword', $user) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe *</label>
                    <input type="password" name="password" id="password" required
                           class="w-full border-gray-300 rounded-lg px-4 py-3 @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmer *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                </div>

                <button type="submit" class="px-6 py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-900">
                    <i class="fas fa-lock mr-2"></i>Mettre à jour le mot de passe
                </button>
            </form>
        </div>

        @if($user->id !== auth('admin')->id())
            <div class="bg-white rounded-xl shadow-lg p-8 border border-red-100">
                <h2 class="text-xl font-bold text-red-700 mb-4"><i class="fas fa-exclamation-triangle mr-2"></i>Zone dangereuse</h2>
                <p class="text-sm text-gray-600 mb-4">La suppression est définitive.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Supprimer l'utilisateur
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
