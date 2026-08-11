@extends('layouts.app')

@section('title', $seo['title'] ?? $translation->title)
@section('meta_description', $seo['description'] ?? '')

@push('head')
    @foreach($hreflang as $tag)
        <link rel="alternate" hreflang="{{ $tag['hreflang'] }}" href="{{ $tag['href'] }}" />
    @endforeach
    <link rel="canonical" href="{{ url()->current() }}" />
    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
<article class="bg-sand-50 min-h-screen">
    <header class="relative">
        @if($guide->featured_image)
            <div class="aspect-[21/9] max-h-[500px] overflow-hidden">
                <img src="{{ asset('storage/' . $guide->featured_image) }}"
                     alt="{{ $translation->title }}"
                     class="w-full h-full object-cover">
            </div>
        @else
            <div class="bg-gradient-to-br from-primary-600 to-primary-800 h-48"></div>
        @endif
    </header>

    <div class="container mx-auto px-4 -mt-20 relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="card p-6 md:p-10">
                <nav class="mb-6" aria-label="Breadcrumb">
                    <ol class="flex items-center flex-wrap gap-2 text-sm text-sand-500">
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last && $crumb['url'])
                                <li><a href="{{ $crumb['url'] }}" class="hover:text-primary-600">{{ $crumb['title'] }}</a></li>
                                <li><span class="mx-1">/</span></li>
                            @else
                                <li class="text-sand-800 font-medium truncate max-w-xs">{{ $crumb['title'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                <div class="flex flex-wrap items-center gap-4 text-sm text-sand-500 mb-4">
                    <span class="badge badge-primary capitalize">{{ $guide->category }}</span>
                    <span>{{ $guide->reading_time }} min {{ __('de lecture') }}</span>
                    @if($guide->published_at)
                        <span>{{ $guide->published_at->translatedFormat('d F Y') }}</span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-4xl font-display font-bold text-sand-900 mb-6">
                    {{ $translation->title }}
                </h1>

                @if($translation->excerpt)
                    <p class="text-lg text-sand-600 mb-8 leading-relaxed">{{ $translation->excerpt }}</p>
                @endif

                <div class="prose prose-sand max-w-none prose-headings:font-display">
                    {!! $translation->content !!}
                </div>
            </div>

            @if($guide->tours->isNotEmpty())
                <div class="mt-10">
                    <h2 class="text-2xl font-display font-bold text-sand-900 mb-6">{{ __('Tours recommandés') }}</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($guide->tours as $tour)
                            <x-tour-card :tour="$tour" />
                        @endforeach
                    </div>
                </div>
            @endif

            @if($relatedGuides->isNotEmpty())
                <div class="mt-10 mb-16">
                    <h2 class="text-2xl font-display font-bold text-sand-900 mb-6">{{ __('Guides similaires') }}</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedGuides as $related)
                            @php $relTrans = $related->translate(); @endphp
                            @if($relTrans)
                                <a href="{{ route('guides.show', $relTrans->slug) }}" class="card p-5 hover:shadow-lg transition">
                                    <span class="text-xs text-primary-600 uppercase">{{ $related->category }}</span>
                                    <h3 class="font-semibold text-sand-900 mt-2">{{ $relTrans->title }}</h3>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</article>
@endsection
