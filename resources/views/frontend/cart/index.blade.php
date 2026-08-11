<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
    @endpush

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-poppins font-bold text-gray-900">{{ __('Cart') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('Manage your bookings') }}</p>
            </div>

            @if(request('added') == '1' || session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') ?? __('Tour added to cart successfully!') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(empty($cartItems))
                <!-- Panier vide -->
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-poppins font-bold text-gray-900 mb-2">{{ __('Your cart is empty') }}</h2>
                    <p class="text-gray-600 mb-6">{{ __('Add tours to your cart to get started') }}</p>
                    <a href="{{ route('tours.index') }}" 
                       class="inline-block px-6 py-3 rounded-lg font-semibold text-white transition-all duration-300 hover:shadow-lg"
                       style="background-color: {{ primary_color() }};">
                        {{ __('Explore tours') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Liste des items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $itemId => $item)
                            @php
                                $tour = $item['tour'];
                                $currencySymbol = \App\Helpers\CurrencyHelper::current()?->symbol ?? '€';
                            @endphp
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                <!-- Image et infos principales -->
                                <div class="flex flex-col sm:flex-row">
                                    <!-- Image -->
                                    <div class="sm:w-48 h-48 sm:h-auto flex-shrink-0">
                                        @php
                                            $tourImage = $tour->primaryImage ?? $tour->images->first();
                                            $imageUrl = $tourImage ? Storage::url($tourImage->path) : null;
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $tour->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Contenu -->
                                    <div class="flex-1 p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="flex-1">
                                                <h3 class="text-xl font-poppins font-bold text-gray-900 mb-2">
                                                    <a href="{{ route('tours.show', $tour->url_key) }}" class="hover:text-primary transition-colors">
                                                        {{ $tour->name }}
                                                    </a>
                                                </h3>
                                                
                                                <div class="space-y-2 text-sm text-gray-600">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                                                        <span>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                                                        @if(isset($item['departure_time']))
                                                            <span class="ml-2">• {{ $item['departure_time'] }}</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center">
                                                        <i class="fas fa-users mr-2 text-primary"></i>
                                                        <span>
                                                            {{ $item['adults'] }} {{ __('adult(s)') }}
                                                            @if($item['children'] > 0)
                                                                , {{ $item['children'] }} {{ __('child(ren)') }}
                                                            @endif
                                                            @if($item['infants'] > 0)
                                                                , {{ $item['infants'] }} {{ __('baby(ies)') }}
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center">
                                                        <i class="fas fa-tag mr-2 text-primary"></i>
                                                        <span class="capitalize">
                                                            @if(isset($item['pricing_title']))
                                                                {{ $item['pricing_title'] }}
                                                            @else
                                                                {{ $item['pricing_mode'] === 'group' ? __('Group') : __('Private') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    
                                                    @if(isset($item['price_data']['addons']) && count($item['price_data']['addons']) > 0)
                                                        <div class="flex items-start mt-2">
                                                            <i class="fas fa-plus-circle mr-2 text-primary mt-0.5"></i>
                                                            <div class="flex-1">
                                                                <div class="font-semibold text-gray-700 mb-1">{{ __('Additional options:') }}</div>
                                                                <div class="space-y-1">
                                                                    @foreach($item['price_data']['addons'] as $addon)
                                                                        <div class="text-xs text-gray-600">
                                                                            • {{ $addon['addon_name'] }}
                                                                            @if($addon['total_price'] > 0)
                                                                                <span class="font-semibold">(+{{ $currencySymbol }}{{ number_format($addon['total_price'], 2, ',', ' ') }})</span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if(isset($item['price_data']['base_breakdown']))
                                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                                            <div class="text-xs font-semibold text-gray-700 mb-2">{{ __('Price detail:') }}</div>
                                                            <div class="space-y-1 text-xs text-gray-600">
                                                                @if($item['pricing_mode'] === 'group')
                                                                    @if(isset($item['price_data']['base_breakdown']['adults']) && $item['price_data']['base_breakdown']['adults']['quantity'] > 0)
                                                                        <div class="flex justify-between">
                                                                            <span>{{ $item['price_data']['base_breakdown']['adults']['quantity'] }} {{ __('Adult(s)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['adults']['unit_price'], 2, ',', ' ') }}</span>
                                                                            <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['adults']['total'], 2, ',', ' ') }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($item['price_data']['base_breakdown']['children']) && $item['price_data']['base_breakdown']['children']['quantity'] > 0)
                                                                        <div class="flex justify-between">
                                                                            <span>{{ $item['price_data']['base_breakdown']['children']['quantity'] }} {{ __('Child(ren)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['children']['unit_price'], 2, ',', ' ') }}</span>
                                                                            <span class="font-semibold text-green-700">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['children']['total'], 2, ',', ' ') }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($item['price_data']['base_breakdown']['infants']) && $item['price_data']['base_breakdown']['infants']['quantity'] > 0)
                                                                        <div class="flex justify-between">
                                                                            <span>{{ $item['price_data']['base_breakdown']['infants']['quantity'] }} {{ __('Baby(ies)') }}</span>
                                                                            <span class="font-semibold text-blue-700">
                                                                                @if($item['price_data']['base_breakdown']['infants']['unit_price'] > 0)
                                                                                    {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['infants']['total'], 2, ',', ' ') }}
                                                                                @else
                                                                                    {{ __('FREE') }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <div class="flex justify-between">
                                                                        <span>{{ $item['price_data']['base_breakdown']['people'] }} {{ __('person(s)') }} × {{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['unit_price'], 2, ',', ' ') }}</span>
                                                                        <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['base_breakdown']['total'], 2, ',', ' ') }}</span>
                                                                    </div>
                                                                @endif
                                                                @if(isset($item['price_data']['addons_total']) && $item['price_data']['addons_total'] > 0)
                                                                    <div class="flex justify-between pt-1 border-t border-gray-100">
                                                                        <span>{{ __('Additional options') }}</span>
                                                                        <span class="font-semibold">{{ $currencySymbol }}{{ number_format($item['price_data']['addons_total'], 2, ',', ' ') }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Prix -->
                                            <div class="ml-4 text-right">
                                                <div class="text-2xl font-poppins font-bold text-gray-900">
                                                    {{ $currencySymbol }}{{ number_format($item['total_price'], 2, ',', ' ') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                            <form action="{{ route('cart.remove', $itemId) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-700 font-medium text-sm transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    {{ __('Remove') }}
                                                </button>
                                            </form>

                                            <a href="{{ route('tours.select-formula', ['tour' => $tour->id, 'date' => $item['date'], 'participants' => $item['participants'], 'adults' => $item['adults'], 'children' => $item['children'], 'infants' => $item['infants'], 'pricing_id' => $item['pricing_id'], 'selected_time' => $item['tour_date_id']]) }}" 
                                               class="text-primary hover:text-primary-dark font-medium text-sm transition-colors">
                                                <i class="fas fa-edit mr-1"></i>
                                                {{ __('Modify') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Vider le panier -->
                        <div class="mt-6">
                            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir vider votre panier ?');">
                                @csrf
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-700 font-medium transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    {{ __('Clear cart') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Résumé et checkout -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-4">
                            <h2 class="text-xl font-poppins font-bold text-gray-900 mb-4">{{ __('Summary') }}</h2>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>{{ count($cartItems) }} {{ __('tour(s)') }}</span>
                                    <span class="font-semibold text-gray-900">{{ $currencySymbol }}{{ number_format($totalAmount, 2, ',', ' ') }}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-poppins font-bold text-gray-900">{{ __('Total') }}</span>
                                    <span class="text-2xl font-poppins font-bold text-primary">
                                        {{ $currencySymbol }}{{ number_format($totalAmount, 2, ',', ' ') }}
                                    </span>
                                </div>
                            </div>

                            <form action="{{ route('cart.checkout') }}" method="GET">
                                <button type="submit" 
                                        class="w-full px-6 py-3 rounded-lg font-bold text-white transition-all duration-300 hover:shadow-lg text-center"
                                        style="background-color: {{ primary_color() }};">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    {{ __('Proceed to checkout') }}
                                </button>
                            </form>

                            <a href="{{ route('tours.index') }}" 
                               class="block mt-4 text-center text-gray-600 hover:text-primary transition-colors text-sm">
                                <i class="fas fa-arrow-left mr-1"></i>
                                {{ __('Continue shopping') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

