<x-layouts.app>
    <x-slot:title>{{ __('Destinations | Marrakech Tours') }}</x-slot:title>

    <div class="bg-sand-50 min-h-screen py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-display font-bold text-center mb-4">
                {{ __('Nos destinations') }}
            </h1>
            <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                {{ __('Explorez les plus belles destinations accessibles depuis Marrakech') }}
            </p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach([
                    ['name' => 'Désert d\'Agafay', 'slug' => 'agafay', 'desc' => 'À 45 min de Marrakech'],
                    ['name' => 'Vallée de l\'Ourika', 'slug' => 'ourika', 'desc' => 'À 1h de Marrakech'],
                    ['name' => 'Essaouira', 'slug' => 'essaouira', 'desc' => 'À 2h30 de Marrakech'],
                    ['name' => 'Cascades d\'Ouzoud', 'slug' => 'ouzoud', 'desc' => 'À 2h30 de Marrakech'],
                    ['name' => 'Désert de Merzouga', 'slug' => 'merzouga', 'desc' => 'À 9h de Marrakech'],
                    ['name' => 'Aït Ben Haddou', 'slug' => 'ait-ben-haddou', 'desc' => 'À 3h de Marrakech'],
                ] as $destination)
                    <a href="{{ route('destinations.show', ['locale' => app()->getLocale(), 'slug' => $destination['slug']]) }}" 
                       class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="aspect-video bg-sand-200"></div>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold group-hover:text-terracotta-600 transition-colors">
                                {{ $destination['name'] }}
                            </h2>
                            <p class="text-gray-600 text-sm">{{ $destination['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
