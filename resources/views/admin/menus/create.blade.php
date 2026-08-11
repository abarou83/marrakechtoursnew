@extends('admin.layout')

@section('title', 'Créer un Menu')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux menus
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl">
        <div class="mb-8 pb-6 border-b border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Créer un Nouveau Menu</h1>
            <p class="text-gray-600">Configurez un menu de navigation multilingue pour votre site</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-red-800 mb-2">Erreurs de validation</h4>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.menus.store') }}" id="menu-form">
            @csrf
            
            <!-- Section 1: Informations de Base -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-3">1</span>
                    Informations de Base
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Emplacement <span class="text-red-500">*</span>
                        </label>
                        <select name="location" id="menu-location"
                                onchange="toggleMenuTitleTranslations()"
                                class="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
                            @foreach(\App\Models\Menu::locationLabels() as $value => $label)
                                <option value="{{ $value }}" {{ old('location', 'header') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Header = barre de navigation · Footer colonne = bloc du pied de page · Footer bas = liens sous le copyright</p>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Position <span class="text-gray-400 font-normal">(Ordre d'affichage)</span>
                        </label>
                        <input type="number" name="position" value="{{ old('position', 0) }}" 
                               class="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary"
                               min="0" placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Les menus avec une position plus basse apparaissent en premier</p>
                        @error('position')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 w-full cursor-pointer hover:bg-gray-50 transition">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary focus:ring-primary w-5 h-5">
                            <div class="ml-3">
                                <span class="text-sm font-semibold text-gray-700 block">Menu Actif</span>
                                <span class="text-xs text-gray-500" id="menu-active-help">Afficher ce menu sur le site</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Nom du Menu -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-3">2</span>
                    Nom du Menu
                </h2>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nom du Menu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary" 
                           required
                           placeholder="Ex: Menu Principal">
                    <p class="text-xs text-gray-500 mt-1">Nom interne pour l'administration (ex: Menu Footer Navigation)</p>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 2b: Titre affiché (footer) -->
            <div id="menu-title-translations" class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200 hidden">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-3">2b</span>
                    Titre affiché dans le footer
                </h2>
                <p class="text-sm text-gray-500 mb-4">Ce titre apparaît comme en-tête de colonne (ex: Navigation, Catégories, Informations légales)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($locales as $locale => $localeInfo)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $localeInfo['name'] ?? $locale }} ({{ strtoupper($locale) }})
                            </label>
                            <input type="hidden" name="menu_translations[{{ $locale }}][locale]" value="{{ $locale }}">
                            <input type="text"
                                   name="menu_translations[{{ $locale }}][name]"
                                   value="{{ old("menu_translations.$locale.name") }}"
                                   class="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="Ex: Navigation">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Items du Menu (Multilingues) -->
            <div class="mb-8 bg-gray-50 rounded-lg border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-3">3</span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Items du Menu</h2>
                            <p class="text-sm text-gray-500 mt-1">Ajoutez les liens de navigation (multilingues)</p>
                        </div>
                    </div>
                    <button type="button" onclick="addMenuItem()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-plus mr-2"></i> Ajouter un Item
                    </button>
                </div>

                <div id="menu-items-container" class="space-y-4">
                    <!-- Items will be added here dynamically -->
                </div>
                
                <p class="text-xs text-gray-500 mt-2 text-center">
                    <i class="fas fa-info-circle mr-1"></i> Glissez-déposez les items pour réorganiser leur ordre
                </p>

                <div id="no-items-message" class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-list text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 font-medium mb-2">Aucun item ajouté</p>
                    <p class="text-sm text-gray-500">Cliquez sur "Ajouter un Item" pour commencer</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.menus.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                    <i class="fas fa-times mr-2"></i> Annuler
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-check mr-2"></i> Créer le Menu
                </button>
            </div>
        </form>
    </div>

    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <script>
        let itemIndex = 0;
        const availableLocales = @json($availableLocales);
        const locales = @json($locales);
        const categories = @json($categories->map(function($cat) {
            return ['id' => $cat->id, 'name' => translate_model($cat, 'name')];
        }));
        const pages = @json($pages->map(function($page) {
            return ['id' => $page->id, 'name' => translate_model($page, 'title')];
        }));
        const tours = @json($tours->map(function($tour) {
            return ['id' => $tour->id, 'name' => translate_model($tour, 'title')];
        }));

        function isCustomLinkType(type) {
            return ['custom', 'internal', 'external'].includes(type);
        }

        function isEntityLinkType(type) {
            return ['category', 'page', 'tour'].includes(type);
        }

        function normalizeLinkType(type) {
            return ['internal', 'external'].includes(type) ? 'custom' : (type || 'custom');
        }

        function updateItemDisplayName(index) {
            const item = document.querySelector(`.menu-item[data-index="${index}"]`);
            if (!item) return;

            const titleEl = item.querySelector('.item-display-name');
            const typeEl = item.querySelector('.item-display-type');
            const linkTypeSelect = item.querySelector('.link-type-select');
            if (!titleEl || !linkTypeSelect) return;

            const linkType = linkTypeSelect.value;
            let name = '';

            if (isEntityLinkType(linkType)) {
                const selector = linkType === 'category'
                    ? 'select[name*="[category_id]"]'
                    : linkType === 'page'
                        ? 'select[name*="[page_id]"]'
                        : 'select[name*="[tour_id]"]';
                const select = item.querySelector(selector);
                if (select && select.value) {
                    name = select.options[select.selectedIndex]?.text?.trim() || '';
                }
            } else {
                const defaultLocale = availableLocales?.[0] || 'fr';
                const input = item.querySelector(`.item-translation-input[data-locale="${defaultLocale}"]:not([disabled])`)
                    || item.querySelector('.item-translation-input:not([disabled])');
                name = input?.value?.trim() || '';
            }

            titleEl.textContent = name || `Item #${parseInt(index, 10) + 1}`;

            if (typeEl) {
                const labels = { custom: 'Personnalisé', category: 'Catégorie', page: 'Page', tour: 'Tour' };
                typeEl.textContent = labels[linkType] || linkType;
            }
        }

        function bindMenuItemEvents() {
            const container = document.getElementById('menu-items-container');
            if (!container || container.dataset.nameEventsBound === '1') return;
            container.dataset.nameEventsBound = '1';

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-translation-input')) {
                    const item = e.target.closest('.menu-item');
                    if (item) updateItemDisplayName(item.getAttribute('data-index'));
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.matches('.link-type-select, select[name*="[category_id]"], select[name*="[page_id]"], select[name*="[tour_id]"]')) {
                    const item = e.target.closest('.menu-item');
                    if (item) updateItemDisplayName(item.getAttribute('data-index'));
                }
            });
        }
        
        // Initialize SortableJS
        let sortableInstance = null;
        
        function initSortable() {
            const container = document.getElementById('menu-items-container');
            if (container && typeof Sortable !== 'undefined') {
                sortableInstance = new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function(evt) {
                        updateItemOrders();
                    }
                });
            }
        }
        
        function updateItemOrders() {
            const container = document.getElementById('menu-items-container');
            const items = container.querySelectorAll('.menu-item');
            items.forEach((item, index) => {
                const orderInput = item.querySelector('input[name*="[order]"]');
                if (orderInput) {
                    orderInput.value = index;
                }
                // Update the order badge
                const orderBadge = item.querySelector('.order-badge');
                if (orderBadge) {
                    orderBadge.textContent = index + 1;
                }
            });
        }

        function generateItemTranslationsHTML(index) {
            if (!availableLocales || !Array.isArray(availableLocales) || availableLocales.length === 0) {
                return '';
            }
            
            const defaultLocale = availableLocales[0] || 'fr';
            let html = '<div class="item-translations-section border border-gray-200 rounded-lg bg-white">';
            html += '<div class="border-b border-gray-200 p-3 bg-gray-50 rounded-t-lg">';
            html += '<label class="item-translations-label block text-xs font-semibold text-gray-600 mb-2">Label Multilingue <span class="text-red-500 translation-required-star">*</span></label>';
            html += '<p class="item-translations-hint hidden text-xs text-blue-600"><i class="fas fa-info-circle mr-1"></i>Le label est récupéré automatiquement depuis la catégorie, la page ou le tour sélectionné.</p>';
            html += '</div><div class="p-3 item-translations-inputs space-y-3">';
            
            availableLocales.forEach((locale, localeIndex) => {
                html += '<div class="translation-locale-panel" data-locale="' + locale + '">';
                html += '<label class="block text-xs text-gray-500 mb-1">' + locale.toUpperCase() + '</label>';
                html += '<input type="hidden" name="items[' + index + '][translations][' + localeIndex + '][locale]" value="' + locale + '">';
                html += '<input type="text" name="items[' + index + '][translations][' + localeIndex + '][label]" ';
                html += 'class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary item-translation-input" ';
                html += 'data-item-index="' + index + '" data-locale="' + locale + '" ';
                html += 'placeholder="Label en ' + locale + '">';
                html += '</div>';
            });
            
            html += '</div></div>';
            return html;
        }

        function addMenuItem(itemData = null) {
            const container = document.getElementById('menu-items-container');
            const noItemsMsg = document.getElementById('no-items-message');
            if (!container) return;
            
            if (noItemsMsg) noItemsMsg.style.display = 'none';
            
            const index = itemIndex++;
            
            let categoriesOptions = '<option value="">Sélectionner une catégorie</option>';
            if (categories && Array.isArray(categories)) {
                categories.forEach(cat => {
                    categoriesOptions += '<option value="' + cat.id + '" ' + (itemData?.category_id == cat.id ? 'selected' : '') + '>' + cat.name + '</option>';
                });
            }
            
            let pagesOptions = '<option value="">Sélectionner une page</option>';
            if (pages && Array.isArray(pages)) {
                pages.forEach(page => {
                    pagesOptions += '<option value="' + page.id + '" ' + (itemData?.page_id == page.id ? 'selected' : '') + '>' + page.name + '</option>';
                });
            }

            let toursOptions = '<option value="">Sélectionner un tour</option>';
            if (tours && Array.isArray(tours)) {
                tours.forEach(tour => {
                    toursOptions += '<option value="' + tour.id + '" ' + (itemData?.tour_id == tour.id ? 'selected' : '') + '>' + tour.name + '</option>';
                });
            }

            const linkType = normalizeLinkType(itemData?.link_type);
            const itemHtml = `
                <div class="menu-item bg-white border-2 border-gray-200 rounded-lg hover:border-primary/50 transition-all" data-index="${index}" x-data="{ isOpen: true }">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 transition-colors" title="Glisser pour réorganiser">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary font-bold order-badge">
                                ${itemData?.order ?? index + 1}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 item-display-name">${itemData?.label || itemData?.name || 'Item #' + (index + 1)}</h4>
                                <p class="text-xs text-gray-500 flex items-center mt-1 item-display-type">Personnalisé</p>
                            </div>
                            <div class="flex-1 cursor-pointer" @click="isOpen = !isOpen">
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    @click.stop="isOpen = !isOpen">
                                <i class="fas" :class="isOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <button type="button" onclick="removeMenuItem(${index})" 
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                    @click.stop>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div x-show="isOpen" x-transition class="p-6">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Type de Lien <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${index}][link_type]" 
                                    class="w-full border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary link-type-select" 
                                    onchange="updateLinkFields(${index})" required>
                                <option value="custom" ${linkType === 'custom' ? 'selected' : ''}>Personnalisé (URL)</option>
                                <option value="category" ${linkType === 'category' ? 'selected' : ''}>Catégorie</option>
                                <option value="page" ${linkType === 'page' ? 'selected' : ''}>Page</option>
                                <option value="tour" ${linkType === 'tour' ? 'selected' : ''}>Tour</option>
                            </select>
                        </div>
                        
                        <div>
                            ${generateItemTranslationsHTML(index)}
                        </div>
                    </div>

                    <div class="link-fields mb-4" id="link-fields-${index}">
                        <div class="custom-link-fields" style="display: ${isCustomLinkType(linkType) ? 'block' : 'none'};">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                URL <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="items[${index}][link_url]" 
                                   value="${itemData?.link_url || ''}" 
                                   class="w-full border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="/tours ou https://example.com">
                            <p class="text-xs text-gray-500 mt-1">Chemin interne (/page) ou URL externe complète</p>
                        </div>
                        
                        <div class="category-field" style="display: ${linkType === 'category' ? 'block' : 'none'};">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catégorie <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${index}][category_id]" 
                                    class="w-full border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
                                ${categoriesOptions}
                            </select>
                        </div>
                        
                        <div class="page-field" style="display: ${linkType === 'page' ? 'block' : 'none'};">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Page <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${index}][page_id]" 
                                    class="w-full border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
                                ${pagesOptions}
                            </select>
                        </div>

                        <div class="tour-field" style="display: ${linkType === 'tour' ? 'block' : 'none'};">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tour <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${index}][tour_id]" 
                                    class="w-full border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
                                ${toursOptions}
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Ordre d'affichage
                            </label>
                            <input type="number" name="items[${index}][order]" 
                                   value="${itemData?.order ?? index}" 
                                   class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary" 
                                   min="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Icône (Font Awesome)
                            </label>
                            <input type="text" name="items[${index}][icon]" 
                                   value="${itemData?.icon || ''}" 
                                   class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                   placeholder="fas fa-home">
                            <p class="text-xs text-gray-500 mt-1">Ex: fas fa-home, fas fa-map</p>
                        </div>
                        
                        <div class="flex items-end">
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 w-full cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="items[${index}][is_active]" value="1" 
                                       ${itemData?.is_active !== false ? 'checked' : ''}
                                       class="rounded border-gray-300 text-primary focus:ring-primary w-5 h-5">
                                <span class="ml-3 text-sm font-semibold text-gray-700">Item Actif</span>
                            </label>
                        </div>
                    </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            updateLinkFields(index);
            
            // Réinitialiser Alpine.js
            if (window.Alpine) {
                const newItem = container.querySelector(`.menu-item[data-index="${index}"]`);
                if (newItem) {
                    Alpine.initTree(newItem);
                }
            }
            
            // Réinitialiser SortableJS
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            initSortable();
            updateItemOrders();
        }

        function removeMenuItem(index) {
            const item = document.querySelector(`.menu-item[data-index="${index}"]`);
            if (item && confirm('Êtes-vous sûr de vouloir supprimer cet item ?')) {
                item.remove();
                
                // Afficher le message si plus d'items
                const container = document.getElementById('menu-items-container');
                const noItemsMsg = document.getElementById('no-items-message');
                if (container && noItemsMsg && container.children.length === 0) {
                    noItemsMsg.style.display = 'block';
                }
                
                // Mettre à jour les ordres
                updateItemOrders();
            }
        }
        
        // Initialize SortableJS on page load
        document.addEventListener('DOMContentLoaded', function() {
            initSortable();
            toggleMenuTitleTranslations();
            bindMenuItemEvents();
        });

        function toggleMenuTitleTranslations() {
            const locationSelect = document.getElementById('menu-location');
            const titleSection = document.getElementById('menu-title-translations');
            const activeHelp = document.getElementById('menu-active-help');

            if (!locationSelect || !titleSection) {
                return;
            }

            const isFooter = locationSelect.value === 'footer' || locationSelect.value === 'footer_bottom';
            titleSection.classList.toggle('hidden', !isFooter);

            if (activeHelp) {
                if (locationSelect.value === 'header') {
                    activeHelp.textContent = 'Afficher ce menu dans la barre de navigation';
                } else if (locationSelect.value === 'footer') {
                    activeHelp.textContent = 'Afficher ce menu comme colonne dans le footer';
                } else {
                    activeHelp.textContent = 'Afficher ces liens dans la barre du bas du footer';
                }
            }
        }

        function updateLinkFields(index) {
            const select = document.querySelector(`.menu-item[data-index="${index}"] .link-type-select`);
            const item = document.querySelector(`.menu-item[data-index="${index}"]`);
            if (!select || !item) return;

            const customFields = item.querySelector('.custom-link-fields');
            const categoryField = item.querySelector('.category-field');
            const pageField = item.querySelector('.page-field');
            const tourField = item.querySelector('.tour-field');
            const translationsSection = item.querySelector('.item-translations-section');
            const translationsHint = item.querySelector('.item-translations-hint');
            const translationsInputs = item.querySelector('.item-translations-inputs');
            const requiredStar = item.querySelector('.translation-required-star');

            [customFields, categoryField, pageField, tourField].forEach(el => {
                if (el) el.style.display = 'none';
            });

            const urlInput = item.querySelector('input[name*="[link_url]"]');
            const categorySelect = item.querySelector('select[name*="[category_id]"]');
            const pageSelect = item.querySelector('select[name*="[page_id]"]');
            const tourSelect = item.querySelector('select[name*="[tour_id]"]');

            [urlInput, categorySelect, pageSelect, tourSelect].forEach(el => {
                if (el) el.removeAttribute('required');
            });

            item.querySelectorAll('.item-translation-input').forEach(input => {
                input.removeAttribute('required');
                input.disabled = false;
            });

            const linkType = select.value;

            if (isEntityLinkType(linkType)) {
                if (translationsHint) translationsHint.classList.remove('hidden');
                if (requiredStar) requiredStar.classList.add('hidden');
                if (translationsInputs) translationsInputs.classList.add('hidden');
                item.querySelectorAll('.item-translation-input').forEach(input => {
                    input.disabled = true;
                    input.value = '';
                });
            } else {
                if (translationsHint) translationsHint.classList.add('hidden');
                if (requiredStar) requiredStar.classList.remove('hidden');
                if (translationsInputs) translationsInputs.classList.remove('hidden');
                item.querySelectorAll('.item-translation-input').forEach(input => {
                    input.setAttribute('required', 'required');
                });
                if (customFields) customFields.style.display = 'block';
                if (urlInput) urlInput.setAttribute('required', 'required');
            }

            if (linkType === 'category') {
                if (categoryField) categoryField.style.display = 'block';
                if (categorySelect) categorySelect.setAttribute('required', 'required');
            } else if (linkType === 'page') {
                if (pageField) pageField.style.display = 'block';
                if (pageSelect) pageSelect.setAttribute('required', 'required');
            } else if (linkType === 'tour') {
                if (tourField) tourField.style.display = 'block';
                if (tourSelect) tourSelect.setAttribute('required', 'required');
            }

            updateItemDisplayName(index);
        }

        // Validation du formulaire
        document.getElementById('menu-form').addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // Vérifier le nom du menu
            const menuNameInput = document.querySelector('input[name="name"]');
            if (!menuNameInput || !menuNameInput.value.trim()) {
                isValid = false;
                errors.push('Le nom du menu est requis.');
            }

            // Vérifier les items
            const container = document.getElementById('menu-items-container');
            if (!container || container.children.length === 0) {
                isValid = false;
                errors.push('Veuillez ajouter au moins un item au menu.');
            } else {
                container.querySelectorAll('.menu-item').forEach((item, itemIndex) => {
                    const linkType = item.querySelector('.link-type-select').value;

                    if (isCustomLinkType(linkType)) {
                        item.querySelectorAll('.item-translation-input:not([disabled])').forEach(input => {
                            if (!input.value.trim()) {
                                isValid = false;
                                const locale = input.getAttribute('data-locale');
                                errors.push(`L'item #${itemIndex + 1} : le label en ${locale.toUpperCase()} est requis.`);
                            }
                        });

                        const linkUrl = item.querySelector('input[name*="[link_url]"]');
                        if (!linkUrl || !linkUrl.value.trim()) {
                            isValid = false;
                            errors.push(`L'item #${itemIndex + 1} : l'URL est requise.`);
                        }
                    }

                    if (linkType === 'category') {
                        const categoryId = item.querySelector('select[name*="[category_id]"]');
                        if (!categoryId || !categoryId.value) {
                            isValid = false;
                            errors.push(`L'item #${itemIndex + 1} : veuillez sélectionner une catégorie.`);
                        }
                    }

                    if (linkType === 'page') {
                        const pageId = item.querySelector('select[name*="[page_id]"]');
                        if (!pageId || !pageId.value) {
                            isValid = false;
                            errors.push(`L'item #${itemIndex + 1} : veuillez sélectionner une page.`);
                        }
                    }

                    if (linkType === 'tour') {
                        const tourId = item.querySelector('select[name*="[tour_id]"]');
                        if (!tourId || !tourId.value) {
                            isValid = false;
                            errors.push(`L'item #${itemIndex + 1} : veuillez sélectionner un tour.`);
                        }
                    }
                });
            }

            if (!isValid) {
                e.preventDefault();
                let errorMessage = 'Veuillez corriger les erreurs suivantes :\n\n';
                errors.forEach((error, index) => {
                    errorMessage += `${index + 1}. ${error}\n`;
                });
                alert(errorMessage);
                return false;
            }
        });
    </script>
@endsection
