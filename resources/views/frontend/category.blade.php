<x-app-layout>
    {{-- Category Hero --}}
    <div class="bg-gradient-to-r from-teal-500 via-cyan-500 to-blue-500 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ translate_model($category, 'name') }}</h1>
            <p class="text-xl text-white/90 max-w-2xl mx-auto">
                {{ translate_model($category, 'meta_description') ?: __('Discover the most appreciated tours by our travelers') }}
            </p>
        </div>
    </div>

    {{-- Tours Grid --}}
    <section class="section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($tours->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($tours as $tour)
                        <x-tour-card :tour="$tour" />
                    @endforeach
                </div>

                @if($tours->hasPages())
                    <div class="mt-12">
                        {{ $tours->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg mb-4">Aucun tour disponible dans cette catégorie</p>
                    <a href="{{ route('home') }}"
                       class="btn-modern inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
