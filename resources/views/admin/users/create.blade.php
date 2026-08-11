@extends('admin.layout')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
        <i class="fas fa-arrow-left mr-1"></i>Retour à la liste
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-user-plus mr-2"></i>Nouvel utilisateur admin</h2>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="w-full border-gray-300 rounded-lg px-4 py-3 @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="w-full border-gray-300 rounded-lg px-4 py-3 @error('email') border-red-500 @enderror">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe *</label>
            <input type="password" name="password" id="password" required
                   class="w-full border-gray-300 rounded-lg px-4 py-3 @error('password') border-red-500 @enderror">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe *</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   class="w-full border-gray-300 rounded-lg px-4 py-3">
        </div>

        <div>
            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Rôle *</label>
            <select name="role" id="role" required class="w-full border-gray-300 rounded-lg px-4 py-3">
                <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
            </select>
            @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', true) ? 'checked' : '' }}
                   class="w-5 h-5 text-green-600 border-gray-300 rounded">
            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">Compte actif</label>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
                <i class="fas fa-save mr-2"></i>Créer
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">Annuler</a>
        </div>
    </form>
</div>
@endsection
