<x-layouts.app>
    <x-slot:title>{{ __('Politique de confidentialité | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto prose prose-lg">
                <h1>{{ __('Politique de confidentialité') }}</h1>
                
                <p>{{ __('Dernière mise à jour :') }} {{ now()->format('d/m/Y') }}</p>
                
                <h2>{{ __('Collecte des données') }}</h2>
                <p>{{ __('Nous collectons les informations que vous nous fournissez lors de la réservation ou du contact.') }}</p>
                
                <h2>{{ __('Utilisation des données') }}</h2>
                <p>{{ __('Vos données sont utilisées pour traiter vos réservations et améliorer nos services.') }}</p>
                
                <h2>{{ __('Vos droits') }}</h2>
                <p>{{ __('Vous disposez d\'un droit d\'accès, de rectification et de suppression de vos données.') }}</p>
                
                <h2>{{ __('Contact') }}</h2>
                <p>{{ __('Pour toute question : contact@marrakechtours.com') }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
