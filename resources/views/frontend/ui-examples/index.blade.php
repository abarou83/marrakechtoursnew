<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="mb-12 text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🎨 Composants UI - Exemples
                </h1>
                <p class="text-lg text-gray-600">
                    Composants inspirés de shadcn/ui pour Laravel Blade + Alpine.js
                </p>
            </div>

            {{-- Buttons Section --}}
            <section class="mb-12">
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>Boutons (Buttons)</x-ui.card-title>
                        <p class="text-sm text-gray-500 mt-2">
                            Différentes variantes et tailles de boutons
                        </p>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        {{-- Variantes --}}
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Variantes</h3>
                            <div class="flex flex-wrap gap-4">
                                <x-ui.button>Par défaut</x-ui.button>
                                <x-ui.button variant="destructive">Destructif</x-ui.button>
                                <x-ui.button variant="outline">Outline</x-ui.button>
                                <x-ui.button variant="secondary">Secondaire</x-ui.button>
                                <x-ui.button variant="ghost">Ghost</x-ui.button>
                                <x-ui.button variant="link">Lien</x-ui.button>
                            </div>
                        </div>

                        {{-- Tailles --}}
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Tailles</h3>
                            <div class="flex items-center gap-4">
                                <x-ui.button size="sm">Petit (sm)</x-ui.button>
                                <x-ui.button size="default">Normal (default)</x-ui.button>
                                <x-ui.button size="lg">Grand (lg)</x-ui.button>
                                <x-ui.button size="icon">
                                    <i class="fas fa-heart"></i>
                                </x-ui.button>
                            </div>
                        </div>

                        {{-- Avec Alpine.js --}}
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-4">Avec Alpine.js (Interactif)</h3>
                            <div x-data="{ count: 0, message: '' }" class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <x-ui.button @click="count++">
                                        Clics: <span x-text="count" class="font-bold"></span>
                                    </x-ui.button>
                                    <x-ui.button variant="outline" @click="count = 0">
                                        Réinitialiser
                                    </x-ui.button>
                                </div>
                                <div>
                                    <x-ui.button variant="secondary" @click="message = message ? '' : 'Bonjour ! 👋'">
                                        <span x-show="!message">Afficher message</span>
                                        <span x-show="message">Masquer message</span>
                                    </x-ui.button>
                                    <p x-show="message" x-text="message" class="mt-2 text-gray-700 font-medium"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Code Example --}}
                        <div class="mt-8 bg-gray-100 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">Exemple de code :</h4>
                            <pre class="text-sm overflow-x-auto"><code>&lt;x-ui.button&gt;Par défaut&lt;/x-ui.button&gt;
&lt;x-ui.button variant="destructive"&gt;Supprimer&lt;/x-ui.button&gt;
&lt;x-ui.button variant="outline"&gt;Annuler&lt;/x-ui.button&gt;
&lt;x-ui.button size="sm"&gt;Petit&lt;/x-ui.button&gt;
&lt;x-ui.button @click="open = true"&gt;Ouvrir&lt;/x-ui.button&gt;</code></pre>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- Cards Section --}}
            <section class="mb-12">
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>Cartes (Cards)</x-ui.card-title>
                        <p class="text-sm text-gray-500 mt-2">
                            Composants de carte avec header et content
                        </p>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Card 1 --}}
                            <x-ui.card>
                                <x-ui.card-header>
                                    <x-ui.card-title>Carte Simple</x-ui.card-title>
                                </x-ui.card-header>
                                <x-ui.card-content>
                                    <p class="text-gray-600">
                                        Ceci est un exemple de carte avec un titre et du contenu.
                                    </p>
                                </x-ui.card-content>
                            </x-ui.card>

                            {{-- Card 2 avec bouton --}}
                            <x-ui.card>
                                <x-ui.card-header>
                                    <x-ui.card-title>Carte avec Actions</x-ui.card-title>
                                </x-ui.card-header>
                                <x-ui.card-content>
                                    <p class="text-gray-600 mb-4">
                                        Une carte peut contenir des boutons et autres éléments interactifs.
                                    </p>
                                    <div class="flex gap-2">
                                        <x-ui.button size="sm">Action 1</x-ui.button>
                                        <x-ui.button variant="outline" size="sm">Action 2</x-ui.button>
                                    </div>
                                </x-ui.card-content>
                            </x-ui.card>
                        </div>

                        {{-- Code Example --}}
                        <div class="mt-8 bg-gray-100 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">Exemple de code :</h4>
                            <pre class="text-sm overflow-x-auto"><code>&lt;x-ui.card&gt;
    &lt;x-ui.card-header&gt;
        &lt;x-ui.card-title&gt;Mon Titre&lt;/x-ui.card-title&gt;
    &lt;/x-ui.card-header&gt;
    &lt;x-ui.card-content&gt;
        Contenu de la carte
    &lt;/x-ui.card-content&gt;
&lt;/x-ui.card&gt;</code></pre>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- Combined Example --}}
            <section class="mb-12">
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>Exemple Complet</x-ui.card-title>
                        <p class="text-sm text-gray-500 mt-2">
                            Combinaison de différents composants
                        </p>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div x-data="{ showDetails: false }">
                            <p class="text-gray-600 mb-6">
                                Voici un exemple combinant plusieurs composants avec Alpine.js pour créer une interface interactive.
                            </p>
                            
                            <div class="space-y-4">
                                <x-ui.button @click="showDetails = !showDetails">
                                    <span x-show="!showDetails">Afficher les détails</span>
                                    <span x-show="showDetails">Masquer les détails</span>
                                    <i :class="showDetails ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="ml-2"></i>
                                </x-ui.button>

                                <div x-show="showDetails" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="mt-4">
                                    <x-ui.card>
                                        <x-ui.card-content class="pt-6">
                                            <h4 class="font-semibold text-lg mb-2">Détails supplémentaires</h4>
                                            <p class="text-gray-600 mb-4">
                                                Cette section utilise Alpine.js pour l'animation et l'affichage conditionnel.
                                            </p>
                                            <div class="flex gap-2">
                                                <x-ui.button variant="secondary" size="sm">En savoir plus</x-ui.button>
                                                <x-ui.button variant="outline" size="sm">Fermer</x-ui.button>
                                            </div>
                                        </x-ui.card-content>
                                    </x-ui.card>
                                </div>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>

            {{-- Info Section --}}
            <section>
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>ℹ️ Informations</x-ui.card-title>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold mb-2">📍 Emplacement des composants</h4>
                                <p class="text-gray-600">
                                    Les composants sont dans <code class="bg-gray-100 px-2 py-1 rounded">resources/views/components/ui/</code>
                                </p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">📚 Documentation</h4>
                                <p class="text-gray-600">
                                    Consultez <code class="bg-gray-100 px-2 py-1 rounded">SHADCN_UI_INTEGRATION.md</code> pour plus de détails.
                                </p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">🎨 Personnalisation</h4>
                                <p class="text-gray-600">
                                    Les couleurs sont définies dans <code class="bg-gray-100 px-2 py-1 rounded">tailwind.config.js</code>
                                </p>
                            </div>
                            <div class="pt-4 border-t">
                                <x-ui.button variant="link" href="{{ route('home') }}">
                                    ← Retour à l'accueil
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </section>
        </div>
    </div>
</x-app-layout>

