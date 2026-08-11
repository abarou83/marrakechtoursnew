<x-layouts.app>
    <x-slot:title>{{ __('Mentions légales | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto prose prose-lg">
                <h1>{{ __('Mentions légales') }}</h1>
                
                <h2>{{ __('Éditeur du site') }}</h2>
                <p>Marrakech Tours<br>Marrakech, Maroc</p>
                
                <h2>{{ __('Hébergement') }}</h2>
                <p>o2switch<br>222 Boulevard Gustave Flaubert<br>63000 Clermont-Ferrand, France</p>
                
                <h2>{{ __('Propriété intellectuelle') }}</h2>
                <p>{{ __('L\'ensemble du contenu de ce site est protégé par le droit d\'auteur.') }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
