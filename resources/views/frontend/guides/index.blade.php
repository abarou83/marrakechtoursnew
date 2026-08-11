@extends('layouts.app')

@section('title', $seo['title'] ?? __('Guides'))
@section('meta_description', $seo['description'] ?? '')

@push('head')
    @foreach($hreflang as $tag)
        <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
    @endforeach
    <link rel="canonical" href="{{ url()->current() }}" />
@endpush

@section('content')
<section class="bg-gradient-to-br from-primary-600 to-primary-800 text-white py-14 lg:py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl lg:text-5xl font-display font-bold mb-4">{{ __('Guides voyage') }}</h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto">
            {{ __('Tout ce qu\'il faut savoir pour préparer votre aventure au Maroc') }}
        </p>
    </div>
</section>

<section class="py-12 lg:py-16 bg-sand-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="lg:w-72 flex-shrink-0">
                <div class="card p-4 mb-6">
                    <form method="GET" action="{{ route('guides.index') }}">
                        <label class="label">{{ __('Rechercher') }}</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="{{ __('Mot-clé...') }}" class="input flex-1">
                            <button type="submit" class="btn-primary px-4">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            </button>
                        </div>
                    </form>
                </div>

                @if($categories->isNotEmpty())
                    <div class="card p-4">
                        <h3 class="font-semibold text-sand-900 mb-3">{{ __('Catégories') }}</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('guides.index') }}"
                                   @class([
                                       'block py-1.5 text-sm hover:text-primary-600',
                                       'font-semibold text-primary-600' => !request('category'),
                                       'text-sand-600' => request('category'),
                                   ])>{{ __('Tous les guides') }}</a>
                            </li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('guides.index', ['category' => $category]) }}"
                                       @class([
                                           'block py-1.5 text-sm hover:text-primary-600 capitalize',
                                           'font-semibold text-primary-600' => request('category') === $category,
                                           'text-sand-600' => request('category') !== $category,
                                       ])>{{ str_replace('_', ' ', $category) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>

            <div class="flex-1">
                @if($guides->count())
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($guides as $guide)
                            @php $trans = $guide->translate(); @endphp
                            @if($trans)
                                <article class="card overflow-hidden group hover:shadow-lg transition">
                                    @if($guide->featured_image)
                                        <a href="{{ route('guides.show', $trans->slug) }}" class="block aspect-[16/9] overflow-hidden">
                                            <img src="{{ asset('storage/' . $guide->featured_image) }}"
                                                 alt="{{ $trans->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        </a>
                                    @endif
                                    <div class="p-5">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-primary-600">{{ $guide->category }}</span>
                                        <h2 class="text-xl font-display font-bold text-sand-900 mt-2 mb-2">
                                            <a href="{{ route('guides.show', $trans->slug) }}" class="hover:text-primary-600">
                                                {{ $trans->title }}
                                            </a>
                                        </h2>
                                        @if($trans->excerpt)
                                            <p class="text-sand-600 text-sm line-clamp-3">{{ $trans->excerpt }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-4 text-xs text-sand-500">
                                            <span>{{ $guide->reading_time }} min</span>
                                            @if($guide->published_at)
                                                <span>{{ $guide->published_at->translatedFormat('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $guides->links() }}
                    </div>
                @else
                    <div class="card p-10 text-center text-sand-600">
                        {{ __('Aucun guide disponible pour le moment.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
