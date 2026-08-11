<x-layouts.app>
    <x-slot:title>Styleguide - Design System</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-bold font-display text-gray-900">Styleguide</h1>
            <p class="mt-2 text-lg text-gray-600">Design System - Marrakech Tours V2</p>
        </div>

        <div class="space-y-16">
            {{-- Colors --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Couleurs</h2>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div>
                        <div class="h-20 rounded-lg bg-primary-500 mb-2"></div>
                        <p class="text-sm font-medium">Primary (Terracotta)</p>
                        <p class="text-xs text-gray-500">#C1440E</p>
                    </div>
                    <div>
                        <div class="h-20 rounded-lg bg-secondary-500 mb-2"></div>
                        <p class="text-sm font-medium">Secondary (Majorelle)</p>
                        <p class="text-xs text-gray-500">#4355BE</p>
                    </div>
                    <div>
                        <div class="h-20 rounded-lg bg-accent-500 mb-2"></div>
                        <p class="text-sm font-medium">Accent (Ocre)</p>
                        <p class="text-xs text-gray-500">#D4A843</p>
                    </div>
                    <div>
                        <div class="h-20 rounded-lg bg-sand-100 border border-sand-200 mb-2"></div>
                        <p class="text-sm font-medium">Sand</p>
                        <p class="text-xs text-gray-500">#F5F0E8</p>
                    </div>
                    <div>
                        <div class="h-20 rounded-lg bg-success-500 mb-2"></div>
                        <p class="text-sm font-medium">Success</p>
                        <p class="text-xs text-gray-500">#22C55E</p>
                    </div>
                    <div>
                        <div class="h-20 rounded-lg bg-danger-500 mb-2"></div>
                        <p class="text-sm font-medium">Danger</p>
                        <p class="text-xs text-gray-500">#EF4444</p>
                    </div>
                </div>
            </section>

            {{-- Typography --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Typographie</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Display / Fraunces</p>
                        <h1 class="text-5xl font-bold font-display">Découvrez le Maroc authentique</h1>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">H2 / Fraunces</p>
                        <h2 class="text-3xl font-semibold font-display">Excursions depuis Marrakech</h2>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Body / Inter</p>
                        <p class="text-base">Explorez les merveilles du désert d'Agafay, les cascades d'Ouzoud et les villes impériales avec nos guides experts.</p>
                    </div>
                </div>
            </section>

            {{-- Buttons --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Boutons</h2>

                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Variants</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.button variant="primary">Primary</x-ui.button>
                            <x-ui.button variant="secondary">Secondary</x-ui.button>
                            <x-ui.button variant="accent">Accent</x-ui.button>
                            <x-ui.button variant="outline">Outline</x-ui.button>
                            <x-ui.button variant="ghost">Ghost</x-ui.button>
                            <x-ui.button variant="danger">Danger</x-ui.button>
                            <x-ui.button variant="white">White</x-ui.button>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Sizes</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button size="xs">Extra Small</x-ui.button>
                            <x-ui.button size="sm">Small</x-ui.button>
                            <x-ui.button size="md">Medium</x-ui.button>
                            <x-ui.button size="lg">Large</x-ui.button>
                            <x-ui.button size="xl">Extra Large</x-ui.button>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">States</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button>Normal</x-ui.button>
                            <x-ui.button :disabled="true">Disabled</x-ui.button>
                            <x-ui.button :loading="true">Loading...</x-ui.button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Cards --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Cards</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-ui.card>
                        <x-slot:header>
                            <h3 class="font-semibold">Card avec Header</h3>
                        </x-slot:header>
                        <p class="text-gray-600">Contenu de la card avec un header et un footer optionnels.</p>
                        <x-slot:footer>
                            <x-ui.button size="sm">Action</x-ui.button>
                        </x-slot:footer>
                    </x-ui.card>

                    <x-ui.card :hover="true">
                        <h3 class="font-semibold mb-2">Card Hover</h3>
                        <p class="text-gray-600">Cette card a un effet hover avec shadow et translation.</p>
                    </x-ui.card>

                    <x-ui.card>
                        <x-slot:image>
                            <img src="https://images.unsplash.com/photo-1489749798305-4fea3ae63d43?w=400&h=300&fit=crop" alt="Desert" class="w-full h-full object-cover">
                        </x-slot:image>
                        <h3 class="font-semibold mb-2">Card avec Image</h3>
                        <p class="text-gray-600">Card avec une image en haut.</p>
                    </x-ui.card>
                </div>
            </section>

            {{-- Badges --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Badges</h2>

                <div class="flex flex-wrap gap-3">
                    <x-ui.badge>Default</x-ui.badge>
                    <x-ui.badge variant="primary">Primary</x-ui.badge>
                    <x-ui.badge variant="secondary">Secondary</x-ui.badge>
                    <x-ui.badge variant="accent">Accent</x-ui.badge>
                    <x-ui.badge variant="success">Success</x-ui.badge>
                    <x-ui.badge variant="warning">Warning</x-ui.badge>
                    <x-ui.badge variant="danger">Danger</x-ui.badge>
                    <x-ui.badge variant="success" :dot="true">Avec dot</x-ui.badge>
                    <x-ui.badge variant="primary" :rounded="true">Rounded</x-ui.badge>
                </div>
            </section>

            {{-- Alerts --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Alerts</h2>

                <div class="space-y-4">
                    <x-ui.alert variant="info" title="Information">
                        Votre réservation a bien été enregistrée.
                    </x-ui.alert>

                    <x-ui.alert variant="success" title="Succès" :dismissible="true">
                        Paiement confirmé ! Vous recevrez votre voucher par email.
                    </x-ui.alert>

                    <x-ui.alert variant="warning">
                        Attention : Places limitées pour cette date.
                    </x-ui.alert>

                    <x-ui.alert variant="danger" title="Erreur">
                        Une erreur est survenue lors du paiement.
                    </x-ui.alert>
                </div>
            </section>

            {{-- Inputs --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Inputs</h2>

                <div class="max-w-md space-y-4">
                    <x-ui.input
                        label="Nom complet"
                        name="name"
                        placeholder="John Doe"
                        :required="true"
                    />

                    <x-ui.input
                        type="email"
                        label="Email"
                        name="email"
                        placeholder="john@example.com"
                        hint="Nous ne partagerons jamais votre email."
                    />

                    <x-ui.input
                        label="Avec erreur"
                        name="error_example"
                        value="invalid"
                        error="Ce champ est invalide."
                    />

                    <x-ui.select
                        label="Nombre de personnes"
                        name="persons"
                        :required="true"
                        :options="[1 => '1 personne', 2 => '2 personnes', 3 => '3 personnes', 4 => '4+ personnes']"
                        placeholder="Sélectionnez..."
                    />
                </div>
            </section>

            {{-- Dialog --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Dialog</h2>

                <x-ui.dialog id="demo-dialog" title="Confirmer la réservation" description="Vérifiez les détails avant de continuer.">
                    <x-slot:trigger>
                        <x-ui.button>Ouvrir Dialog</x-ui.button>
                    </x-slot:trigger>

                    <p class="text-gray-600">
                        Contenu du dialog. Vous pouvez mettre n'importe quel contenu ici : formulaires, texte, images, etc.
                    </p>

                    <x-slot:footer>
                        <div class="flex justify-end gap-3">
                            <x-ui.button variant="ghost" @click="open = false">Annuler</x-ui.button>
                            <x-ui.button>Confirmer</x-ui.button>
                        </div>
                    </x-slot:footer>
                </x-ui.dialog>
            </section>

            {{-- Dropdown --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Dropdown</h2>

                <x-ui.dropdown>
                    <x-slot:trigger>
                        <x-ui.button variant="outline">
                            Options
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </x-ui.button>
                    </x-slot:trigger>

                    <x-ui.dropdown-item href="#">Voir les détails</x-ui.dropdown-item>
                    <x-ui.dropdown-item href="#">Modifier</x-ui.dropdown-item>
                    <x-ui.dropdown-item href="#">Dupliquer</x-ui.dropdown-item>
                    <div class="border-t border-gray-100 my-1"></div>
                    <x-ui.dropdown-item :danger="true">Supprimer</x-ui.dropdown-item>
                </x-ui.dropdown>
            </section>

            {{-- Tabs --}}
            <section>
                <h2 class="text-2xl font-semibold font-display text-gray-900 mb-6">Tabs</h2>

                <x-ui.tabs default-tab="description">
                    <x-slot:list>
                        <x-ui.tab name="description" label="Description" />
                        <x-ui.tab name="itinerary" label="Itinéraire" />
                        <x-ui.tab name="reviews" label="Avis (24)" />
                        <x-ui.tab name="faq" label="FAQ" />
                    </x-slot:list>

                    <x-ui.tab-panel name="description">
                        <p class="text-gray-600">Contenu de l'onglet Description. Explorez le désert d'Agafay à seulement 30 minutes de Marrakech...</p>
                    </x-ui.tab-panel>

                    <x-ui.tab-panel name="itinerary">
                        <p class="text-gray-600">Contenu de l'onglet Itinéraire. 09:00 - Départ de Marrakech...</p>
                    </x-ui.tab-panel>

                    <x-ui.tab-panel name="reviews">
                        <p class="text-gray-600">Contenu de l'onglet Avis. Note moyenne : 4.8/5...</p>
                    </x-ui.tab-panel>

                    <x-ui.tab-panel name="faq">
                        <p class="text-gray-600">Contenu de l'onglet FAQ. Questions fréquentes...</p>
                    </x-ui.tab-panel>
                </x-ui.tabs>
            </section>
        </div>
    </div>
</x-layouts.app>
