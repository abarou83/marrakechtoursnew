<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion Admin - {{ config('app.name', 'Tourify') }}</title>
    
    <!-- Favicon -->
    @php
        $favicon = site_setting('favicon_path');
    @endphp
    @if($favicon && \Storage::disk('public')->exists($favicon))
        <link rel="icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
        <link rel="apple-touch-icon" href="{{ \Storage::url($favicon) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-10">
            <!-- Header -->
            <div class="text-center">
                @php
                    $logoPath = site_setting('logo_path');
                    $isSvg = $logoPath && strtolower(pathinfo($logoPath, PATHINFO_EXTENSION)) === 'svg';
                @endphp
                @if($logoPath && Storage::disk('public')->exists($logoPath))
                    {{-- Custom uploaded logo --}}
                    <div class="mx-auto mb-4 flex items-center justify-center">
                        @if($isSvg)
                            <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="w-48 object-contain">
                        @else
                            <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="w-48 object-contain rounded-xl">
                        @endif
                    </div>
                @else
                    {{-- Default icon --}}
                    <div class="mx-auto h-48 w-48 flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 mb-4">
                        <i class="fas fa-shield-halved text-white text-7xl"></i>
                    </div>
                @endif
                <h2 class="text-3xl font-extrabold text-gray-900">Espace Administrateur</h2>
                <p class="mt-2 text-sm text-gray-600">Connectez-vous pour accéder au panneau d'administration</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-800 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-indigo-600"></i>Email
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-indigo-600"></i>Mot de passe
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="current-password"
                           class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105 shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-indigo-300"></i>
                        </span>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>Retour au site client
                </a>
            </div>
        </div>
    </div>
</body>
</html>









