<x-layouts.app>
    <x-slot:title>{{ __('Catégorie | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-display font-bold mb-8">{{ __('Catégorie') }}</h1>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <p class="text-gray-600 col-span-full">{{ __('Chargement des tours...') }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
