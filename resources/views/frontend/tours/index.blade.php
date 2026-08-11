@section('seo_meta_tags')
    <title>{{ $fullPageTitle ?? 'Tours & Activities - ' . config('app.name', 'Tourify') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Discover amazing tours and activities. Book your next adventure today!' }}">
    <meta name="keywords" content="{{ $location ? 'tours ' . $location . ', activities ' . $location . ', ' . $location . ' excursions' : 'tours, activities, excursions, travel, adventures' }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle ?? 'Tours & Activities' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Discover amazing tours and activities. Book your next adventure today!' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Tourify') }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Tours & Activities' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover amazing tours and activities. Book your next adventure today!' }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Robots --}}
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
@endsection

<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filtres island (Livewire) --}}
            <livewire:islands.tour-filters-island />

            {{-- Résultats avec H1 SEO-friendly --}}
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ $pageTitle ?? 'Tours & Activities' }}
                </h1>
                <p class="text-gray-600 mb-4">
                    {{ $tours->total() }} {{ $tours->total() == 1 ? __('tour found') : __('tours found') }}
                </p>
            </div>

            {{-- Grid des Tours --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($tours as $tour)
                    <x-tour-card :tour="$tour" />
                @empty
                    <div class="col-span-3 text-center py-20">
                        <div class="text-8xl mb-4">🔍</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('No tours found') }}</h3>
                        <p class="text-gray-600 mb-6">{{ __('Try modifying your search criteria') }}</p>
                        <a href="{{ route('tours.index') }}" 
                           class="inline-block px-6 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700">
                            {{ __('View all tours') }}
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($tours->hasPages())
                <div class="mt-12">
                    {{ $tours->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
