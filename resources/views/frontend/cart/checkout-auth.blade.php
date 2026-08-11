<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
    @endpush

    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ __('Login required') }}
                </h1>
                <p class="text-gray-600">{{ __('Log in or create an account to complete your order') }}</p>
            </div>

            <div x-data="{ activeTab: 'login' }" class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button @click="activeTab = 'login'" 
                            :class="activeTab === 'login' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-600 hover:text-gray-900'"
                            class="px-6 py-3 text-lg transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {{ __('Log in') }}
                    </button>
                    <button @click="activeTab = 'register'" 
                            :class="activeTab === 'register' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-600 hover:text-gray-900'"
                            class="px-6 py-3 text-lg transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        {{ __('Create an account') }}
                    </button>
                </div>

                <!-- Login Form -->
                <div x-show="activeTab === 'login'" x-transition>
                    <form method="POST" action="{{ route('client.login') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="redirect_to_checkout" value="1">

                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label for="login_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Email') }}
                            </label>
                            <input id="login_email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus
                                   autocomplete="email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="login_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Password') }}
                            </label>
                            <input id="login_password" 
                                   type="password" 
                                   name="password" 
                                   required
                                   autocomplete="current-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember" class="flex items-center">
                                <input id="remember" 
                                       type="checkbox" 
                                       name="remember" 
                                       class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg text-center text-lg"
                                style="background-color: {{ primary_color() }};">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            {{ __('Log in') }}
                        </button>
                    </form>

                    <!-- Google Login Button -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('client.google.redirect', ['redirect_to' => 'checkout']) }}" 
                           class="w-full flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm md:text-base">{{ __('Continue with Google') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Register Form -->
                <div x-show="activeTab === 'register'" x-transition>
                    <form method="POST" action="{{ route('client.register') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="redirect_to_checkout" value="1">

                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label for="register_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Full name') }}
                            </label>
                            <input id="register_name" 
                                   type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required 
                                   autofocus
                                   autocomplete="name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="register_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Email') }}
                            </label>
                            <input id="register_email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="register_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Phone') }}
                            </label>
                            <input id="register_phone" 
                                   type="tel" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   required
                                   autocomplete="tel"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="register_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Password') }}
                            </label>
                            <input id="register_password" 
                                   type="password" 
                                   name="password" 
                                   required
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="register_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> {{ __('Confirm password') }}
                            </label>
                            <input id="register_password_confirmation" 
                                   type="password" 
                                   name="password_confirmation" 
                                   required
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-primary focus:outline-none">
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg text-center text-lg"
                                style="background-color: {{ primary_color() }};">
                            <i class="fas fa-user-plus mr-2"></i>
                            {{ __('Create my account') }}
                        </button>
                    </form>

                    <!-- Google Register Button -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('client.google.redirect', ['redirect_to' => 'checkout']) }}" 
                           class="w-full flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm md:text-base">{{ __('Sign up with Google') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('cart.index') }}" 
                   class="text-gray-600 hover:text-primary transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    {{ __('Back to cart') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

