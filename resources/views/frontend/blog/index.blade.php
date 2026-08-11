@extends('layouts.app')

@section('title', $seo['title'] ?? __('Blog'))
@section('meta_description', $seo['description'] ?? '')

@push('head')
    @foreach($hreflang as $tag)
        <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
    @endforeach
    <link rel="canonical" href="{{ url()->current() }}" />
@endpush

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-600 to-primary-800 text-white py-14 lg:py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl lg:text-5xl font-display font-bold mb-4">{{ __('Blog') }}</h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto">
            {{ __('Conseils, guides et inspiration pour votre voyage au Maroc') }}
        </p>
    </div>
</section>

<section class="py-12 lg:py-16 bg-sand-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar --}}
            <aside class="lg:w-72 flex-shrink-0 order-2 lg:order-1">
                {{-- Search --}}
                <div class="card p-4 mb-6">
                    <form method="GET" action="{{ route('blog.index') }}">
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

                {{-- Categories --}}
                @if(isset($categories) && $categories->count() > 0)
                    <div class="card p-4 mb-6">
                        <h3 class="font-semibold text-sand-900 mb-3">{{ __('Catégories') }}</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('blog.index') }}" 
                                   @class([
                                       'flex justify-between items-center py-1.5 text-sm hover:text-primary-600',
                                       'font-semibold text-primary-600' => !request('category'),
                                       'text-sand-600' => request('category'),
                                   ])>
                                    <span>{{ __('Tous les articles') }}</span>
                                </a>
                            </li>
                            @foreach($categories as $category)
                                @php $catTrans = $category->translate(); @endphp
                                <li>
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                       @class([
                                           'flex justify-between items-center py-1.5 text-sm hover:text-primary-600',
                                           'font-semibold text-primary-600' => request('category') === $category->slug,
                                           'text-sand-600' => request('category') !== $category->slug,
                                       ])>
                                        <span>{{ $catTrans?->name ?? $category->name }}</span>
                                        <span class="text-xs text-sand-400">({{ $category->posts_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Popular Tags --}}
                @if(isset($popularTags) && $popularTags->count() > 0)
                    <div class="card p-4">
                        <h3 class="font-semibold text-sand-900 mb-3">{{ __('Tags populaires') }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($popularTags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag]) }}" 
                                   @class([
                                       'badge text-xs',
                                       'bg-primary-500 text-white' => request('tag') === $tag,
                                       'bg-sand-200 text-sand-700 hover:bg-primary-100' => request('tag') !== $tag,
                                   ])>
                                    #{{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            {{-- Posts Grid --}}
            <div class="flex-1 order-1 lg:order-2">
                @if(request()->hasAny(['search', 'category', 'tag']))
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sand-600">
                            {{ $posts->total() }} {{ __('résultat(s)') }}
                            @if(request('search'))
                                {{ __('pour') }} "{{ request('search') }}"
                            @endif
                        </p>
                        <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:underline">
                            {{ __('Effacer les filtres') }}
                        </a>
                    </div>
                @endif

                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($posts as $post)
                            @php $translation = $post->translate(); @endphp
                            <article class="card overflow-hidden hover:shadow-lg transition-shadow group">
                                <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="block">
                                    @if($post->getFirstMediaUrl('featured', 'card'))
                                        <div class="aspect-video overflow-hidden">
                                            <img src="{{ $post->getFirstMediaUrl('featured', 'card') }}" 
                                                 alt="{{ $translation?->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 loading="lazy">
                                        </div>
                                    @elseif($post->featured_image)
                                        <div class="aspect-video overflow-hidden">
                                            <img src="{{ $post->featured_image }}" 
                                                 alt="{{ $translation?->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 loading="lazy">
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <div class="flex items-center gap-3 text-xs text-sand-500 mb-2">
                                            <span>{{ ($post->published_at ?? $post->created_at)->translatedFormat('d M Y') }}</span>
                                            <span>•</span>
                                            <span>{{ $post->reading_time }} min {{ __('de lecture') }}</span>
                                        </div>
                                        <h2 class="text-lg font-semibold text-sand-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                            {{ $translation?->title }}
                                        </h2>
                                        @if($translation?->excerpt)
                                            <p class="text-sand-600 text-sm line-clamp-2">
                                                {{ $translation->excerpt }}
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    @if($posts->hasPages())
                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 card">
                        <x-heroicon-o-newspaper class="w-16 h-16 mx-auto text-sand-300 mb-4" />
                        <h3 class="text-lg font-semibold text-sand-700 mb-2">{{ __('Aucun article trouvé') }}</h3>
                        <p class="text-sand-500">{{ __('Essayez une autre recherche ou catégorie.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
