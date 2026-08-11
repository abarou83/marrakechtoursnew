<x-layouts.app>
    <x-slot:title>{{ __('À propos | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen">
        {{-- Hero --}}
        <section class="bg-terracotta-600 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-display font-bold mb-6">
                    {{ __('À propos de Marrakech Tours') }}
                </h1>
                <p class="text-xl opacity-90 max-w-3xl mx-auto">
                    {{ __('Votre partenaire local pour découvrir le Maroc authentique depuis Marrakech') }}
                </p>
            </div>
        </section>

        {{-- Content --}}
        <section class="py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto prose prose-lg">
                    <h2>{{ __('Notre histoire') }}</h2>
                    <p>{{ __('Fondée par des passionnés du Maroc, Marrakech Tours vous propose des excursions authentiques et des expériences uniques au cœur du pays.') }}</p>
                    
                    <h2>{{ __('Nos valeurs') }}</h2>
                    <ul>
                        <li>{{ __('Authenticité : Des expériences locales et vraies') }}</li>
                        <li>{{ __('Qualité : Des services premium à prix justes') }}</li>
                        <li>{{ __('Durabilité : Un tourisme responsable') }}</li>
                        <li>{{ __('Proximité : Une équipe à votre écoute') }}</li>
                    </ul>
                    
                    <h2>{{ __('Pourquoi nous choisir ?') }}</h2>
                    <p>{{ __('Avec des années d\'expérience et une connaissance approfondie du terrain, nous vous garantissons des excursions mémorables en toute sécurité.') }}</p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
