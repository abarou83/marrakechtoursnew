<!-- Login Modal (Desktop and Mobile/Tablet) -->
<div id="loginModal"
     x-show="$store.loginModal.open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4"
     @click.self="$store.loginModal.closeModal()"
     x-cloak>
    <div onclick="event.stopPropagation();"
         class="login-modal-dialog bg-white rounded-lg shadow-xl w-full max-w-lg sm:max-w-xl md:max-w-2xl lg:max-w-3xl max-h-[min(90vh,820px)] flex flex-col overflow-hidden p-4 md:p-5">
        <div class="shrink-0 flex items-center justify-between mb-4">
            <h3 class="text-xl md:text-2xl font-bold text-gray-900"
                x-text="$store.loginModal.activeTab === 'login' ? @js(__('Login')) : @js(__('Create an account'))"></h3>
            <button type="button"
                    @click="$store.loginModal.closeModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Tabs -->
        <div class="shrink-0 flex border-b border-gray-200 mb-4">
            <button @click="$store.loginModal.setActiveTab('login')" 
                    :class="$store.loginModal.activeTab === 'login' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 px-3 py-2.5 text-center transition-colors text-sm md:text-base">
                <i class="fas fa-sign-in-alt mr-2"></i>
                {{ __('Log in') }}
            </button>
            <button @click="$store.loginModal.setActiveTab('register')" 
                    :class="$store.loginModal.activeTab === 'register' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 px-3 py-2.5 text-center transition-colors text-sm md:text-base">
                <i class="fas fa-user-plus mr-2"></i>
                {{ __('Create an account') }}
            </button>
        </div>

        <!-- Session Status -->
        @if(session('status'))
            <div class="shrink-0 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="shrink-0 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-red-800 mb-1">{{ __('Erreur') }}</h4>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="login-modal-panels min-h-0 flex-1 overflow-y-auto overflow-x-hidden overscroll-contain">
        <!-- Login Form -->
        <div x-show="$store.loginModal.activeTab === 'login'"
             x-cloak
             style="display: none;"
             class="login-modal-panel">
            <form method="POST" action="{{ route('client.login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="modal_email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                    <input id="modal_email" 
                           class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="modal_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                    <input id="modal_password" 
                           class="w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               class="rounded border-gray-300 text-primary focus:ring-primary" 
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" 
                        class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 font-semibold transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ __('Log in') }}
                </button>
            </form>

            <!-- Google Login Button -->
            <div class="mt-4 pt-4 border-t border-gray-200 pb-2">
                <a href="{{ route('client.google.redirect') }}" 
                   class="w-full flex items-center justify-center px-4 py-2.5 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
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
        <div x-show="$store.loginModal.activeTab === 'register'"
             x-cloak
             style="display: none;"
             class="login-modal-panel">
            <form method="POST" action="{{ route('client.register') }}" class="space-y-3">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- First name -->
                    <div class="min-w-0">
                        <label for="modal_register_first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('First name') }}</label>
                        <input id="modal_register_first_name"
                               class="w-full px-3 py-2 border {{ $errors->has('name') || $errors->has('first_name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="text"
                               name="first_name"
                               value="{{ old('first_name', explode(' ', old('name', ''), 2)[0] ?? '') }}"
                               required
                               autofocus
                               autocomplete="given-name" />
                    </div>

                    <!-- Last name -->
                    <div class="min-w-0">
                        <label for="modal_register_last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Last name') }}</label>
                        <input id="modal_register_last_name"
                               class="w-full px-3 py-2 border {{ $errors->has('name') || $errors->has('last_name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="text"
                               name="last_name"
                               value="{{ old('last_name', explode(' ', old('name', ''), 2)[1] ?? '') }}"
                               required
                               autocomplete="family-name" />
                    </div>
                    @error('name')
                        <p class="sm:col-span-2 mt-0 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Email Address -->
                    <div class="min-w-0">
                        <label for="modal_register_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                        <input id="modal_register_email"
                               class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username" />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="min-w-0">
                        <label for="modal_register_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                        <input id="modal_register_phone"
                               class="w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="tel"
                               name="phone"
                               value="{{ old('phone') }}"
                               required
                               autocomplete="tel" />
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Password -->
                    <div class="min-w-0">
                        <label for="modal_register_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                        <input id="modal_register_password"
                               class="w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="password"
                               name="password"
                               required
                               autocomplete="new-password" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="min-w-0">
                        <label for="modal_register_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm password') }}</label>
                        <input id="modal_register_password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               type="password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password" />
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 font-semibold transition">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('Create my account') }}
                </button>
            </form>

            <!-- Google Register Button -->
            <div class="mt-3 pt-3 border-t border-gray-200">
                <a href="{{ route('client.google.redirect') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition text-sm md:text-base">
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
    </div>
</div>

