{{-- Exemple d'utilisation des composants UI inspirés de shadcn/ui --}}

<div class="p-8 space-y-8">
    {{-- Exemple de Card --}}
    <x-ui.card>
        <x-ui.card-header>
            <x-ui.card-title>Exemple de Carte</x-ui.card-title>
            <p class="text-sm text-gray-500">Un exemple d'utilisation des composants UI</p>
        </x-ui.card-header>
        <x-ui.card-content>
            <div class="space-y-4">
                {{-- Boutons avec différentes variantes --}}
                <div class="flex flex-wrap gap-4">
                    <x-ui.button>Par défaut</x-ui.button>
                    <x-ui.button variant="destructive">Destructif</x-ui.button>
                    <x-ui.button variant="outline">Outline</x-ui.button>
                    <x-ui.button variant="secondary">Secondaire</x-ui.button>
                    <x-ui.button variant="ghost">Ghost</x-ui.button>
                    <x-ui.button variant="link">Lien</x-ui.button>
                </div>
                
                {{-- Boutons avec différentes tailles --}}
                <div class="flex items-center gap-4">
                    <x-ui.button size="sm">Petit</x-ui.button>
                    <x-ui.button size="default">Normal</x-ui.button>
                    <x-ui.button size="lg">Grand</x-ui.button>
                    <x-ui.button size="icon">
                        <i class="fas fa-heart"></i>
                    </x-ui.button>
                </div>
                
                {{-- Bouton avec Alpine.js --}}
                <div x-data="{ count: 0 }">
                    <x-ui.button @click="count++">
                        Clics: <span x-text="count"></span>
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
</div>

