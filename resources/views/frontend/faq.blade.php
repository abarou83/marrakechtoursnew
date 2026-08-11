<x-layouts.app>
    <x-slot:title>{{ __('FAQ | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-display font-bold text-center mb-12">
                    {{ __('Questions fréquentes') }}
                </h1>

                <div class="space-y-4">
                    @foreach([
                        ['q' => 'Comment réserver une excursion ?', 'a' => 'Vous pouvez réserver directement en ligne via notre site ou nous contacter par WhatsApp pour un devis personnalisé.'],
                        ['q' => 'Quelle est la politique d\'annulation ?', 'a' => 'Annulation gratuite jusqu\'à 24h avant le départ. Consultez les conditions spécifiques de chaque tour.'],
                        ['q' => 'Les enfants peuvent-ils participer ?', 'a' => 'La plupart de nos excursions acceptent les enfants. L\'âge minimum est indiqué sur chaque fiche tour.'],
                        ['q' => 'Le transport est-il inclus ?', 'a' => 'Oui, le transport aller-retour depuis Marrakech est inclus dans tous nos tours.'],
                        ['q' => 'Quels moyens de paiement acceptez-vous ?', 'a' => 'Nous acceptons les cartes bancaires (Visa, Mastercard) et PayPal.'],
                    ] as $faq)
                        <details class="bg-white rounded-xl shadow-sm group">
                            <summary class="flex items-center justify-between p-6 cursor-pointer font-medium">
                                {{ __($faq['q']) }}
                                <span class="text-terracotta-600 group-open:rotate-180 transition-transform">▼</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-600">
                                {{ __($faq['a']) }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
