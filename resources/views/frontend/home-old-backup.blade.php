<x-app-layout>
    {{-- HERO SECTION - SIMPLE ET ÉLÉGANT --}}
    <section class="relative bg-primary min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
            <div class="text-center">
                {{-- Badge Simple --}}
                <div class="inline-flex items-center px-6 py-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-full mb-8 border border-white border-opacity-20">
                    <i class="fas fa-globe text-white mr-2"></i>
                    <span class="text-white text-sm font-medium">Découvrez nos destinations</span>
                </div>

                {{-- Main Title --}}
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white mb-6">
                    Explorez le monde
                    <br>
                    <span class="text-white text-opacity-80">avec Tourify</span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-xl sm:text-2xl text-white text-opacity-70 mb-12 max-w-3xl mx-auto">
                    Découvrez des aventures extraordinaires et créez des souvenirs inoubliables
                </p>

                {{-- CTA Buttons Simples --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                    <a href="#tours" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary font-bold rounded-lg hover:bg-opacity-90 shadow-lg transition">
                        <i class="fas fa-compass mr-2"></i>
                        <span>Voir les tours</span>
                    </a>
                    <a href="{{ route('tours.index') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white border-opacity-30 text-white font-semibold rounded-lg hover:bg-white hover:bg-opacity-10 transition">
                        <i class="fas fa-list mr-2"></i>
                        <span>Tous les tours</span>
                    </a>
                </div>

                {{-- Stats Cards Simples --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-xl p-6 border border-white border-opacity-10">
                        <i class="fas fa-map-marked-alt text-2xl text-white text-opacity-60 mb-3"></i>
                        <div class="text-4xl font-bold text-white mb-2">{{ $tours->count() }}+</div>
                        <div class="text-white text-opacity-50 text-sm">Tours Disponibles</div>
                    </div>
                    <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-xl p-6 border border-white border-opacity-10">
                        <i class="fas fa-users text-2xl text-white text-opacity-60 mb-3"></i>
                        <div class="text-4xl font-bold text-white mb-2">500+</div>
                        <div class="text-white text-opacity-50 text-sm">Clients Satisfaits</div>
                    </div>
                    <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-xl p-6 border border-white border-opacity-10">
                        <i class="fas fa-th-large text-2xl text-white text-opacity-60 mb-3"></i>
                        <div class="text-4xl font-bold text-white mb-2">{{ $categories->count() }}</div>
                        <div class="text-white text-opacity-50 text-sm">Catégories</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CATÉGORIES SECTION - SIMPLE --}}
    @if($categories->count() > 0)
    <section id="categories" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Nos Catégories
                </h2>
                <p class="text-lg text-gray-600">
                    Choisissez le type d'aventure qui vous correspond
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($categories as $category)
                <a href="{{ route('category.show', $category->url_key) }}" 
                   class="group bg-white rounded-xl p-6 hover:shadow-xl transition border border-gray-200 hover:border-primary">
                    <div class="flex flex-col items-center text-center">
                        @if($category->images->where('is_primary', true)->first())
                            <img src="{{ asset('storage/' . $category->images->where('is_primary', true)->first()->path) }}" 
                                 alt="{{ translate_model($category, 'name') }}"
                                 class="w-16 h-16 object-cover rounded-lg mb-4">
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition">
                            {{ translate_model($category, 'name') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $category->tours_count ?? 0 }} tours
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- TOURS POPULAIRES - SIMPLE --}}
    @if($tours->count() > 0)
    <section id="tours" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Tours Populaires
                </h2>
                <p class="text-lg text-gray-600">
                    Découvrez nos expériences les plus appréciées
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tours->take(6) as $tour)
                <div class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:shadow-xl transition">
                    {{-- Image --}}
                    <div class="relative h-64 overflow-hidden">
                        @if($tour->images->where('is_primary', true)->first())
                            <img src="{{ asset('storage/' . $tour->images->where('is_primary', true)->first()->path) }}" 
                                 alt="{{ translate_model($tour, 'title') }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-4xl"></i>
                            </div>
                        @endif
                        
                        {{-- Badge Promotion --}}
                        @php $activePromo = $tour->activePromotion(); @endphp
                        @if($activePromo)
                            <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                -{{ $activePromo->discount_percentage }}%
                            </div>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    <div class="p-6">
                        {{-- Titre --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition">
                            {{ translate_model($tour, 'title') }}
                        </h3>

                        {{-- Infos --}}
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                {{ translate_model($tour, 'location') }}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-clock mr-1 text-gray-400"></i>
                                {{ translate_model($tour, 'duration') }}
                            </span>
                        </div>

                        {{-- Prix --}}
                        @php
                            $defaultPricing = $tour->defaultPricing();
                            $minPrice = $defaultPricing ? $defaultPricing->min_price : $tour->price;
                            $finalPrice = $activePromo ? $activePromo->calculateDiscountedPrice($minPrice) : $minPrice;
                        @endphp

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                @if($activePromo)
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($minPrice, 2) }} €</span>
                                    <span class="text-2xl font-bold text-gray-900 ml-2">{{ number_format($finalPrice, 2) }} €</span>
                                @else
                                    <span class="text-2xl font-bold text-gray-900">{{ number_format($minPrice, 2) }} €</span>
                                @endif
                                <span class="text-gray-500 text-sm">/pers</span>
                            </div>
                        </div>

                        {{-- Bouton --}}
                        <a href="{{ route('tours.show', $tour) }}" 
                           class="block w-full text-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-opacity-90 transition">
                            Voir détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Voir tous --}}
            <div class="text-center mt-12">
                <a href="{{ route('tours.index') }}" 
                   class="inline-flex items-center px-8 py-3 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary hover:text-white transition">
                    Voir tous les tours
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- CTA SECTION - SIMPLE --}}
    <section class="py-20 bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Prêt pour l'aventure ?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Réservez votre prochain voyage dès aujourd'hui
            </p>
            <a href="{{ route('tours.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-gray-900 font-bold rounded-lg hover:bg-opacity-90 shadow-lg transition">
                <i class="fas fa-search mr-2"></i>
                Découvrir nos tours
            </a>
        </div>
    </section>
</x-app-layout>

