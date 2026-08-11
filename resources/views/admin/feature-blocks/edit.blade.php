@extends('admin.layout')

@section('title', 'Modifier le bloc de fonctionnalité')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.feature-blocks.index') }}" class="text-indigo-600 hover:underline">← Retour</a>
    <h1 class="text-2xl font-bold mt-3 mb-6">Modifier le bloc de fonctionnalité</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.feature-blocks.update', $featureBlock) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Icône Font Awesome <span class="text-red-500">*</span></label>
            <input type="text" name="icon" id="icon-input" value="{{ old('icon', $featureBlock->icon) }}" class="w-full border-gray-300 rounded-lg px-4 py-3 mb-2" placeholder="fa-solid fa-phone" required>
            <p class="text-xs text-gray-500 mb-3">Ex: fa-solid fa-phone, fa-solid fa-lightbulb, fa-solid fa-star, fa-solid fa-calendar-alt</p>
            
            @php
                $allIcons = array_unique([
                    // Communication
                    'fa-phone', 'fa-envelope', 'fa-comments', 'fa-headset', 'fa-phone-alt', 'fa-video', 'fa-microphone',
                    // Business & Services
                    'fa-briefcase', 'fa-handshake', 'fa-building', 'fa-store', 'fa-shopping-cart', 'fa-credit-card', 'fa-wallet',
                    // Technology
                    'fa-laptop', 'fa-mobile-alt', 'fa-tablet-alt', 'fa-wifi', 'fa-cloud', 'fa-server', 'fa-database', 'fa-code',
                    // Security & Trust
                    'fa-shield-alt', 'fa-lock', 'fa-key', 'fa-user-shield', 'fa-check-circle', 'fa-certificate', 'fa-badge-check',
                    // Travel & Location
                    'fa-map-marker-alt', 'fa-globe', 'fa-plane', 'fa-car', 'fa-hotel', 'fa-umbrella-beach', 'fa-compass', 'fa-route',
                    // Time & Calendar
                    'fa-calendar-alt', 'fa-clock', 'fa-calendar-check', 'fa-calendar-day', 'fa-hourglass-half', 'fa-stopwatch',
                    // Users & Social
                    'fa-users', 'fa-user', 'fa-user-friends', 'fa-user-plus', 'fa-user-check', 'fa-user-tie', 'fa-user-graduate',
                    // Health & Wellness
                    'fa-heart', 'fa-heartbeat', 'fa-medkit', 'fa-hospital', 'fa-spa', 'fa-dumbbell', 'fa-running', 'fa-swimmer',
                    // Education & Learning
                    'fa-graduation-cap', 'fa-book', 'fa-book-open', 'fa-university', 'fa-chalkboard-teacher',
                    // Food & Dining
                    'fa-utensils', 'fa-coffee', 'fa-wine-glass', 'fa-birthday-cake', 'fa-pizza-slice', 'fa-hamburger',
                    // Entertainment & Media
                    'fa-music', 'fa-film', 'fa-camera', 'fa-gamepad', 'fa-theater-masks', 'fa-ticket-alt',
                    // Shopping & E-commerce
                    'fa-shopping-bag', 'fa-shopping-basket', 'fa-tags', 'fa-percent', 'fa-gift', 'fa-gift-card', 'fa-box',
                    // Finance & Money
                    'fa-dollar-sign', 'fa-euro-sign', 'fa-pound-sign', 'fa-coins', 'fa-chart-line', 'fa-piggy-bank', 'fa-hand-holding-usd',
                    // Home & Living
                    'fa-home', 'fa-couch', 'fa-bed', 'fa-bath', 'fa-kitchen-set', 'fa-toolbox', 'fa-hammer',
                    // Nature & Environment
                    'fa-leaf', 'fa-tree', 'fa-sun', 'fa-moon', 'fa-cloud-sun', 'fa-seedling', 'fa-recycle', 'fa-water',
                    // Sports & Activities
                    'fa-trophy', 'fa-medal', 'fa-football-ball', 'fa-basketball-ball', 'fa-volleyball-ball', 'fa-bicycle', 'fa-skiing',
                    // Transportation
                    'fa-bus', 'fa-train', 'fa-ship', 'fa-motorcycle', 'fa-taxi', 'fa-subway', 'fa-truck',
                    // General Icons
                    'fa-star', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-flag', 'fa-bell', 'fa-bell-slash', 'fa-bookmark',
                    'fa-lightbulb', 'fa-fire', 'fa-bolt', 'fa-magic', 'fa-gem', 'fa-crown', 'fa-award', 'fa-ribbon',
                    'fa-palette', 'fa-paint-brush', 'fa-image', 'fa-images', 'fa-photo-video', 'fa-folder', 'fa-folder-open',
                    'fa-file', 'fa-file-alt', 'fa-file-pdf', 'fa-file-word', 'fa-save', 'fa-download', 'fa-upload', 'fa-share',
                    'fa-link', 'fa-external-link-alt', 'fa-copy', 'fa-cut', 'fa-paste', 'fa-edit', 'fa-trash', 'fa-trash-alt',
                    'fa-search', 'fa-filter', 'fa-sort', 'fa-sort-up', 'fa-sort-down', 'fa-list', 'fa-list-ul', 'fa-list-ol',
                    'fa-th', 'fa-th-large', 'fa-th-list', 'fa-bars', 'fa-ellipsis-v', 'fa-ellipsis-h', 'fa-times', 'fa-times-circle',
                    'fa-check', 'fa-check-square', 'fa-plus', 'fa-plus-circle', 'fa-minus', 'fa-minus-circle', 'fa-exclamation-circle',
                    'fa-question-circle', 'fa-info-circle', 'fa-exclamation-triangle', 'fa-ban', 'fa-lock-open', 'fa-unlock',
                    'fa-eye', 'fa-eye-slash', 'fa-cog', 'fa-cogs', 'fa-sliders-h', 'fa-tools', 'fa-wrench', 'fa-screwdriver',
                    'fa-puzzle-piece', 'fa-cube', 'fa-cubes', 'fa-box-open', 'fa-archive', 'fa-inbox', 'fa-outbox',
                    'fa-arrow-right', 'fa-arrow-left', 'fa-arrow-up', 'fa-arrow-down', 'fa-chevron-right', 'fa-chevron-left',
                    'fa-chevron-up', 'fa-chevron-down', 'fa-angle-right', 'fa-angle-left', 'fa-angle-up', 'fa-angle-down',
                    'fa-caret-right', 'fa-caret-left', 'fa-caret-up', 'fa-caret-down', 'fa-hand-point-right', 'fa-hand-point-left',
                    'fa-hand-point-up', 'fa-hand-point-down', 'fa-hand-pointer', 'fa-mouse-pointer', 'fa-crosshairs',
                    'fa-sync', 'fa-sync-alt', 'fa-redo', 'fa-undo', 'fa-refresh', 'fa-spinner', 'fa-circle-notch',
                    'fa-play', 'fa-pause', 'fa-stop', 'fa-forward', 'fa-backward', 'fa-step-forward', 'fa-step-backward',
                    'fa-fast-forward', 'fa-fast-backward', 'fa-eject', 'fa-volume-up', 'fa-volume-down', 'fa-volume-mute',
                    'fa-volume-off', 'fa-headphones', 'fa-microphone-alt', 'fa-microphone-alt-slash',
                    'fa-print', 'fa-fax', 'fa-scanner', 'fa-printer', 'fa-keyboard', 'fa-mouse', 'fa-desktop', 'fa-laptop-code',
                    'fa-tablet', 'fa-mobile', 'fa-qrcode', 'fa-barcode', 'fa-rss', 'fa-rss-square', 'fa-broadcast-tower',
                    'fa-satellite', 'fa-satellite-dish', 'fa-signal', 'fa-bluetooth', 'fa-bluetooth-b',
                    'fa-network-wired', 'fa-ethernet', 'fa-hdd', 'fa-memory', 'fa-microchip', 'fa-microprocessor',
                    'fa-usb', 'fa-plug', 'fa-power-off', 'fa-battery-full', 'fa-battery-three-quarters', 'fa-battery-half',
                    'fa-battery-quarter', 'fa-battery-empty', 'fa-charging-station', 'fa-solar-panel', 'fa-wind',
                    'fa-fire-alt', 'fa-smog', 'fa-cloud', 'fa-cloud-rain', 'fa-cloud-moon', 'fa-cloud-showers-heavy',
                    'fa-snowflake', 'fa-icicles', 'fa-temperature-high', 'fa-temperature-low', 'fa-thermometer-half',
                    'fa-thermometer-full', 'fa-thermometer-empty', 'fa-thermometer-quarter', 'fa-thermometer-three-quarters',
                    'fa-star-half', 'fa-star-half-alt', 'fa-meteor', 'fa-rocket', 'fa-space-shuttle', 'fa-user-astronaut',
                    'fa-cloud-sun-rain', 'fa-cloud-moon-rain', 'fa-tornado', 'fa-smoking', 'fa-smoking-ban',
                    'fa-burn', 'fa-flame', 'fa-wand-magic-sparkles', 'fa-hat-wizard',
                    'fa-scroll', 'fa-book-open-reader', 'fa-book-dead', 'fa-book-medical', 'fa-book-reader',
                    'fa-bookmark-alt', 'fa-bookmark-slash', 'fa-bible', 'fa-quran', 'fa-torah', 'fa-journal-whills',
                    'fa-landmark', 'fa-vihara', 'fa-om', 'fa-yin-yang', 'fa-cross', 'fa-church', 'fa-mosque', 'fa-synagogue',
                    'fa-kaaba', 'fa-torii-gate', 'fa-place-of-worship', 'fa-pray', 'fa-praying-hands', 'fa-hands-praying',
                    'fa-hands-holding', 'fa-hands-holding-child', 'fa-hands-holding-circle', 'fa-hands-holding-heart',
                    'fa-hands-holding-medical', 'fa-hands-holding-usd', 'fa-hands-holding-water', 'fa-hands-helping',
                    'fa-hands-wash', 'fa-hand-holding', 'fa-hand-holding-heart', 'fa-hand-holding-medical',
                    'fa-hand-holding-usd', 'fa-hand-holding-water', 'fa-hand-lizard', 'fa-hand-middle-finger',
                    'fa-hand-paper', 'fa-hand-peace', 'fa-hand-rock', 'fa-hand-scissors', 'fa-hand-sparkles',
                    'fa-hand-spock', 'fa-hands', 'fa-hands-clapping', 'fa-handshake-alt', 'fa-handshake-alt-slash', 'fa-handshake-slash'
                ]);
                sort($allIcons);
                $currentIcon = old('icon', $featureBlock->icon);
                $currentIconName = str_replace('fa-solid ', '', $currentIcon);
            @endphp
            
            <div x-data="{ 
                open: false, 
                selectedIcon: '{{ $currentIconName }}',
                search: '',
                icons: @js($allIcons),
                get filteredIcons() {
                    if (!this.search) return this.icons;
                    return this.icons.filter(icon => 
                        icon.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                selectIcon(icon) {
                    this.selectedIcon = icon;
                    document.getElementById('icon-input').value = icon ? 'fa-solid ' + icon : '';
                    this.open = false;
                    this.search = '';
                }
            }" 
            class="relative">
                <button type="button" 
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full border-gray-300 rounded-lg px-4 py-3 bg-white text-left flex items-center justify-between hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="flex items-center gap-2">
                        <template x-if="selectedIcon">
                            <i :class="'fa-solid ' + selectedIcon" class="text-indigo-600"></i>
                        </template>
                        <span x-text="selectedIcon ? selectedIcon.replace('fa-', '') : '-- Sélectionner une icône --'" class="text-gray-700"></span>
                    </span>
                    <i class="fas fa-chevron-down text-gray-400" :class="{ 'transform rotate-180': open }"></i>
                </button>
                
                <div x-show="open" 
                     x-transition
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-96 overflow-hidden">
                    <div class="p-2 border-b border-gray-200">
                        <input type="text" 
                               x-model="search"
                               placeholder="Rechercher une icône..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               @click.stop>
                    </div>
                    <div class="overflow-y-auto max-h-80">
                        <template x-for="icon in filteredIcons" :key="icon">
                            <button type="button"
                                    @click="selectIcon(icon)"
                                    class="w-full px-4 py-2 text-left hover:bg-indigo-50 flex items-center gap-3 transition-colors"
                                    :class="{ 'bg-indigo-100': selectedIcon === icon }">
                                <i :class="'fa-solid ' + icon" class="text-indigo-600 w-5 text-center"></i>
                                <span x-text="icon.replace('fa-', '')" class="text-gray-700"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Sélectionnez une icône dans la liste déroulante ci-dessus</p>
        </div>

        <div class="border-t pt-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Image (SVG ou PNG) <span class="text-gray-500 font-normal">(optionnel)</span>
            </label>
            @if($featureBlock->image_path)
                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-2">Image actuelle:</p>
                    <img src="{{ Storage::url($featureBlock->image_path) }}" alt="Current image" class="h-16 w-16 object-contain">
                    <p class="text-xs text-gray-500 mt-2">{{ $featureBlock->image_path }}</p>
                </div>
            @endif
            <p class="text-xs text-gray-500 mb-2">Si vous uploadez une nouvelle image, elle remplacera l'icône Font Awesome et l'image actuelle</p>
            <input type="file" name="image" accept=".svg,.png,.jpg,.jpeg" class="w-full border-gray-300 rounded-lg px-4 py-2">
            <p class="text-xs text-gray-500 mt-1">Formats acceptés: SVG, PNG, JPG, JPEG (max 2MB)</p>
        </div>

        @php
            use App\Helpers\LanguageHelper;
            $locales = LanguageHelper::getAvailableLocales();
        @endphp

        <!-- Traductions avec Tabs -->
        <div class="border-t pt-6 mb-6 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ activeTab: '{{ $availableLocales[0] ?? 'fr' }}' }">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-language mr-2"></i>Contenu multilingue
                </h3>
                
                {{-- Tabs Navigation --}}
                <div class="border-b border-gray-200 -mx-6 px-6">
                    <div class="flex space-x-1">
                        @foreach($availableLocales as $locale)
                            @php
                                $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                            @endphp
                            <button 
                                type="button"
                                @click="activeTab = '{{ $locale }}'"
                                :class="activeTab === '{{ $locale }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="inline-flex items-center px-4 py-2 border-b-2 font-semibold text-sm transition-colors duration-200">
                                <span class="text-xl mr-2"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                                <span>{{ $localeInfo['native'] }}</span>
                                <span class="ml-2 text-xs opacity-75">({{ strtoupper($locale) }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Tab Content --}}
            <div class="p-6">
                @foreach($availableLocales as $locale)
                    @php
                        $localeInfo = $locales[$locale] ?? ['name' => $locale, 'flag' => '🌍', 'native' => $locale];
                        $translationIndex = $loop->index;
                        $translation = $featureBlock->translations->where('locale', $locale)->first();
                    @endphp
                    
                    <div x-show="activeTab === '{{ $locale }}'" x-transition class="space-y-6">
                        <div class="flex items-center mb-4">
                            <span class="text-2xl mr-2"><span class="fi fi-{{ \App\Helpers\LanguageHelper::getCountryCode($locale) }} fis" style="font-size: 1.25rem;"></span></span>
                            <h4 class="font-bold text-gray-900">Contenu pour {{ $localeInfo['native'] }}</h4>
                            <span class="ml-auto text-xs text-gray-500">{{ strtoupper($locale) }}</span>
                        </div>
                        
                        <input type="hidden" name="translations[{{ $translationIndex }}][locale]" value="{{ $locale }}">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Titre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="translations[{{ $translationIndex }}][title]" 
                                   value="{{ old("translations.{$translationIndex}.title", $translation->title ?? '') }}"
                                   class="w-full border-gray-300 rounded-lg px-4 py-3" 
                                   placeholder="24/7 customer support" 
                                   required>
                            @error("translations.{$translationIndex}.title")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="translations[{{ $translationIndex }}][description]" 
                                      rows="4" 
                                      class="w-full border-gray-300 rounded-lg px-4 py-3" 
                                      placeholder="No matter the time zone, we're here to help." 
                                      required>{{ old("translations.{$translationIndex}.description", $translation->description ?? '') }}</textarea>
                            @error("translations.{$translationIndex}.description")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t pt-4" x-data="{ iconColorEnabled: {{ old('icon_background_color_enabled', $featureBlock->icon_background_color_enabled ?? false) ? 'true' : 'false' }} }">
            <h3 class="text-lg font-bold text-gray-900 mb-4">🎨 Couleur personnalisée</h3>
            
            <div>
                <div class="flex items-center mb-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               x-model="iconColorEnabled"
                               name="icon_background_color_enabled"
                               value="1"
                               {{ old('icon_background_color_enabled', $featureBlock->icon_background_color_enabled ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700">Activer la couleur de fond de l'icône</span>
                    </label>
                </div>
                
                <div x-show="iconColorEnabled" x-transition class="space-y-3">
                    <div class="flex items-center gap-3">
                        <input type="color" 
                               id="icon_background_color_picker"
                               value="{{ old('icon_background_color', $featureBlock->icon_background_color ?? '#211951') }}" 
                               class="w-16 h-10 border-gray-300 rounded-lg cursor-pointer"
                               @input="document.getElementById('icon_background_color_text').value = $event.target.value; document.getElementById('icon_background_color_hidden').value = $event.target.value">
                        <input type="text" 
                               id="icon_background_color_text" 
                               value="{{ old('icon_background_color', $featureBlock->icon_background_color ?? '#211951') }}" 
                               pattern="^#[0-9A-Fa-f]{6}$"
                               placeholder="#211951"
                               class="flex-1 border-gray-300 rounded-lg px-3 py-2 font-mono text-sm"
                               @input="document.getElementById('icon_background_color_picker').value = $event.target.value; document.getElementById('icon_background_color_hidden').value = $event.target.value">
                    </div>
                    <p class="text-xs text-gray-500">Couleur de fond de l'icône (format hex: #RRGGBB). <strong>Note:</strong> La couleur de fond du conteneur est gérée dans les paramètres de la section.</p>
                </div>
                
                <input type="hidden" 
                       id="icon_background_color_hidden"
                       name="icon_background_color" 
                       x-bind:value="iconColorEnabled ? (document.getElementById('icon_background_color_text')?.value || '') : ''">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t pt-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ordre</label>
                <input type="number" name="order" value="{{ old('order', $featureBlock->order) }}" min="0" class="w-full border-gray-300 rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Définit l'ordre d'affichage</p>
            </div>
            <div class="flex items-center">
                <label class="inline-flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-green-600 border-gray-300 rounded" {{ old('is_active', $featureBlock->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Actif</span>
                </label>
            </div>
        </div>

        <div class="flex space-x-4 mt-6">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
            <a href="{{ route('admin.feature-blocks.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-bold">
                Annuler
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Synchroniser les champs couleur et texte
    document.addEventListener('DOMContentLoaded', function() {
        // Icon background color
        const iconColorInput = document.querySelector('input[name="icon_background_color"]');
        const iconColorText = document.querySelector('#icon_background_color_text');
        if (iconColorInput && iconColorText) {
            iconColorInput.addEventListener('input', function() {
                iconColorText.value = this.value;
            });
            iconColorText.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    iconColorInput.value = this.value;
                }
            });
        }
    });
</script>
@endpush
@endsection

