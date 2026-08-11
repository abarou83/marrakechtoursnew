@extends('layouts.app')

@section('title', $seo['title'] ?? $translation->title)
@section('meta_description', $seo['description'] ?? '')

@push('head')
    @foreach($hreflang as $tag)
        <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
    @endforeach
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:type" content="article" />
    <meta property="article:published_time" content="{{ ($post->published_at ?? $post->created_at)->toIso8601String() }}" />
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}" />

    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
<article class="bg-sand-50 min-h-screen">
    {{-- Hero with Featured Image --}}
    <header class="relative">
        @if($post->getFirstMediaUrl('featured') || $post->featured_image)
            <div class="aspect-[21/9] max-h-[500px] overflow-hidden">
                <img src="{{ $post->getFirstMediaUrl('featured') ?: $post->featured_image }}"
                     alt="{{ $translation->title }}"
                     class="w-full h-full object-cover">
            </div>
        @else
            <div class="bg-gradient-to-br from-primary-600 to-primary-800 h-48"></div>
        @endif
    </header>

    <div class="container mx-auto px-4 -mt-20 relative z-10">
        <div class="max-w-4xl mx-auto">
            {{-- Article Card --}}
            <div class="card p-6 md:p-10">
                {{-- Breadcrumbs --}}
                <nav class="mb-6" aria-label="Breadcrumb">
                    <ol class="flex items-center flex-wrap gap-2 text-sm text-sand-500">
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <li>
                                    <a href="{{ $crumb['url'] }}" class="hover:text-primary-600">
                                        {{ $crumb['title'] }}
                                    </a>
                                </li>
                                <li><span class="mx-1">/</span></li>
                            @else
                                <li class="text-sand-800 font-medium truncate max-w-xs">{{ $crumb['title'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                {{-- Meta info --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-sand-500 mb-4">
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        {{ $post->reading_time }} min {{ __('de lecture') }}
                    </span>
                    @if($post->views_count > 100)
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-eye class="w-4 h-4" />
                            {{ number_format($post->views_count) }} {{ __('vues') }}
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-3xl md:text-4xl font-display font-bold text-sand-900 mb-6 leading-tight">
                    {{ $translation->title }}
                </h1>

                {{-- Categories --}}
                @if($post->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($post->categories as $category)
                            @php $catTrans = $category->translate(); @endphp
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                               class="badge bg-primary-100 text-primary-700 hover:bg-primary-200">
                                {{ $catTrans?->name ?? $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Excerpt --}}
                @if($translation->excerpt)
                    <p class="text-lg text-sand-600 mb-8 leading-relaxed border-l-4 border-primary-500 pl-4 italic">
                        {{ $translation->excerpt }}
                    </p>
                @endif

                {{-- Content --}}
                <div class="prose prose-lg prose-sand max-w-none
                            prose-headings:font-display prose-headings:text-sand-900
                            prose-a:text-primary-600 prose-img:rounded-lg">
                    {!! $translation->content !!}
                </div>

                {{-- Tags --}}
                @if($post->tags && count($post->tags) > 0)
                    <div class="mt-8 pt-6 border-t border-sand-200">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm text-sand-500">{{ __('Tags') }}:</span>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag]) }}" 
                                   class="text-sm text-primary-600 hover:underline">
                                    #{{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Share --}}
                <div class="mt-8 pt-6 border-t border-sand-200">
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-sand-500">{{ __('Partager') }}:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($translation->title) }}" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center rounded-full bg-sky-500 text-white hover:bg-sky-600">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($translation->title . ' ' . url()->current()) }}" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center rounded-full bg-green-500 text-white hover:bg-green-600">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($translation->title) }}" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-700 text-white hover:bg-blue-800">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Author Bio --}}
            @if($post->author)
                <div class="card p-6 mt-8 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary-600">
                            {{ substr($post->author->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-sand-900">{{ $post->author->name }}</p>
                        <p class="text-sm text-sand-500">{{ __('Rédacteur chez MarrakechTours') }}</p>
                    </div>
                </div>
            @endif

            {{-- Related Posts --}}
            @if($relatedPosts->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-2xl font-display font-bold text-sand-900 mb-6">{{ __('Articles similaires') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $relatedPost)
                            @php $relTrans = $relatedPost->translate(); @endphp
                            <a href="{{ route('blog.show', $relTrans?->slug ?? $relatedPost->id) }}" 
                               class="card overflow-hidden hover:shadow-lg transition-shadow group">
                                @if($relatedPost->getFirstMediaUrl('featured', 'thumb') || $relatedPost->featured_image)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ $relatedPost->getFirstMediaUrl('featured', 'thumb') ?: $relatedPost->featured_image }}" 
                                             alt="{{ $relTrans?->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy">
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-sand-900 group-hover:text-primary-600 line-clamp-2">
                                        {{ $relTrans?->title }}
                                    </h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Back link --}}
            <div class="mt-8 mb-12">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center text-primary-600 hover:underline font-semibold">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                    {{ __('Retour au blog') }}
                </a>
            </div>
        </div>
    </div>
</article>
@endsection
