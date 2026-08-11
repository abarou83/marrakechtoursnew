<x-layouts.app>
    <x-slot:title>{{ __('Contact | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-display font-bold text-center mb-4">
                    {{ __('Contactez-nous') }}
                </h1>
                <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                    {{ __('Une question ? Besoin d\'un devis personnalisé ? Notre équipe est là pour vous aider.') }}
                </p>

                <div class="grid md:grid-cols-2 gap-12">
                    {{-- Contact Info --}}
                    <div>
                        <h2 class="text-xl font-semibold mb-6">{{ __('Informations de contact') }}</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <span class="text-2xl">📍</span>
                                <div>
                                    <h3 class="font-medium">{{ __('Adresse') }}</h3>
                                    <p class="text-gray-600">Marrakech, Maroc</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <span class="text-2xl">📞</span>
                                <div>
                                    <h3 class="font-medium">{{ __('Téléphone') }}</h3>
                                    <p class="text-gray-600">+212 6 00 00 00 00</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <span class="text-2xl">✉️</span>
                                <div>
                                    <h3 class="font-medium">{{ __('Email') }}</h3>
                                    <p class="text-gray-600">contact@marrakechtours.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <span class="text-2xl">💬</span>
                                <div>
                                    <h3 class="font-medium">{{ __('WhatsApp') }}</h3>
                                    <p class="text-gray-600">+212 6 00 00 00 00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Form --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <form class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nom complet') }}</label>
                                <input type="text" class="w-full rounded-lg border-sand-300 focus:border-terracotta-500 focus:ring-terracotta-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                                <input type="email" class="w-full rounded-lg border-sand-300 focus:border-terracotta-500 focus:ring-terracotta-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sujet') }}</label>
                                <select class="w-full rounded-lg border-sand-300 focus:border-terracotta-500 focus:ring-terracotta-500">
                                    <option>{{ __('Question générale') }}</option>
                                    <option>{{ __('Demande de devis') }}</option>
                                    <option>{{ __('Réservation existante') }}</option>
                                    <option>{{ __('Partenariat') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Message') }}</label>
                                <textarea rows="4" class="w-full rounded-lg border-sand-300 focus:border-terracotta-500 focus:ring-terracotta-500"></textarea>
                            </div>
                            
                            <button type="submit" class="w-full py-3 bg-terracotta-600 text-white font-semibold rounded-lg hover:bg-terracotta-700 transition-colors">
                                {{ __('Envoyer') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
