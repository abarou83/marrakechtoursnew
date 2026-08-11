@push('seo_meta_tags')
    <title>{{ __('Contact us') }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('Have a question? Send us a message and our team will get back to you quickly.') }}">
    <link rel="canonical" href="{{ route('contact') }}">
@endpush

<x-app-layout>
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-teal-500 via-cyan-500 to-blue-500 py-14 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ __('Contact us') }}</h1>
            <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                {{ __('Have a question? Send us a message and our team will get back to you quickly.') }}
            </p>
        </div>
    </div>

    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Coordonnées --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Our contact details') }}</h2>
                        <ul class="space-y-5 text-gray-700">
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ __('Address') }}</p>
                                    <p class="text-sm">{{ $companyAddress }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ __('Email') }}</p>
                                    <a href="mailto:{{ $companyEmail }}" class="text-sm text-primary hover:underline">{{ $companyEmail }}</a>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ __('Phone') }}</p>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $companyPhone) }}" class="text-sm text-primary hover:underline">{{ $companyPhone }}</a>
                                </div>
                            </li>
                        </ul>

                        @if($whatsappNumber)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ urlencode(__('Hello, I would like to get information about your services.')) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="mt-6 inline-flex items-center justify-center w-full px-4 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp mr-2 text-lg"></i>
                                {{ __('Contact us on WhatsApp') }}
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Formulaire --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Send us a message') }}</h2>

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Full name') }} <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ $defaultName }}"
                                           required
                                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ $defaultEmail }}"
                                           required
                                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone') }}</label>
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           value="{{ $defaultPhone }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subject') }} <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           id="subject"
                                           name="subject"
                                           value="{{ old('subject') }}"
                                           required
                                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary @error('subject') border-red-500 @enderror">
                                    @error('subject')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Message') }} <span class="text-red-500">*</span></label>
                                <textarea id="message"
                                          name="message"
                                          rows="6"
                                          required
                                          class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition shadow-sm">
                                <i class="fas fa-paper-plane mr-2"></i>
                                {{ __('Send message') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
